<?php

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\BookMarketing;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Brand;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\CRMNotification;
use App\Models\Invoice;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\NewSMM;
use App\Models\NoForm;
use App\Models\PressReleaseForm;
use App\Models\Project;
use App\Models\Proofreading;
use App\Models\SeoBrief;
use App\Models\SeoForm;
use App\Models\Service;
use App\Models\SmmForm;
use App\Models\Task;
use App\Models\User;
use App\Models\WebForm;
use App\Notifications\TaskNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

function mail_notification ($from, $to, $subject, $html, $for_admin = false) {
    if(env('APP_URL') == 'http://localhost/crm/public') {
        return true;
    }

    try {
        $mails = $to;

        if ($for_admin) { $mails []= 'info@designcrm.net'; }

        Mail::send([], [], function ($message) use ($from, $to, $subject, $html, $mails) {
            $message->to($mails)
            ->subject($subject)
            ->setBody($html, 'text/html');
        });

        return (boolean)count(Mail::failures());
    } catch (\Exception $e) {
        Log::error('function mail_notification failed. Error: ' . $e->getMessage());
        return false;
    }
}

function get_task_status_text ($num = 0) {
    if (!$num) {
        $num = 0;
    }

    $arr = [
        0 => 'Open',
        1 => 'Re Open',
        4 => 'In Progress',
        2 => 'On Hold',
        5 => 'Sent for Approval',
        6 => 'Incomplete Brief',
        3 => 'Completed',
        7 => 'Sent for QA',
    ];

    return ucfirst($arr[$num]) ?? 'Open';
}

function get_task_status_color_class ($num = 0) {
    $arr = [
        0 => 'danger',
        1 => 'danger',
        4 => 'warning',
        2 => 'info',
        5 => 'info',
        6 => 'warning',
        3 => 'success',
        7 => 'info',
    ];

    return $arr[$num] ?? 'danger';
}

function sale_manager_notifications ($brand_id = null)
{
    if (!Auth::check() || Auth::user()->is_employee != 6) {
        return [];
    }

    $notification_ids = [];
    $notifications = DB::table('notifications')->where('notifiable_id', Auth::id())->get();
    foreach ($notifications as $notification) {
        if ($notification->type == 'App\Notifications\LeadNotification') {
            $notification_data = json_decode($notification->data);
            if (!$notification_data->email) {
                continue;
            }

            if (
                !$client = Client::where('email', $notification_data->email)->when($brand_id, function ($q) use ($brand_id) {
                    return $q->where('brand_id', $brand_id);
                })->first()
            ) {
                continue;
            }

            $notification_ids []= $notification->id;
        }

        else if ($notification->type == 'App\Notifications\TaskNotification') {
            $notification_data = json_decode($notification->data);
            if (!$notification_data->task_id) {
                continue;
            }

            if (
                !$task = Task::where('id', $notification_data->task_id)->when($brand_id, function ($q) use ($brand_id) {
                    return $q->where('brand_id', $brand_id);
                })->first()
            ) {
                continue;
            }

            $notification_ids []= $notification->id;
        }

        else if ($notification->type == 'App\Notifications\PaymentNotification') {
            $notification_data = json_decode($notification->data);
            if (!$notification_data->id) {
                continue;
            }

            if (
                !$invoice = Invoice::where('id', $notification_data->id)->when($brand_id, function ($q) use ($brand_id) {
                    return $q->where('brand', $brand_id);
                })->first()
            ) {
                continue;
            }

            $notification_ids []= $notification->id;
        }

        else if ($notification->type == 'App\Notifications\MessageNotification') {
            $notification_data = json_decode($notification->data);
            if (!$notification_data->id) {
                continue;
            }

            if (
                !$client = User::where('id', $notification_data->id)
                    ->whereHas('client', function ($q) use ($brand_id) {
                        return $q->when($brand_id, function ($q) use ($brand_id) {
                            return $q->where('brand_id', $brand_id);
                        });
                    })->first()
            ) {
                continue;
            }

            $notification_ids []= $notification->id;
        }

        else if ($notification->type == 'App\Notifications\AssignProjectNotification') {
            $notification_data = json_decode($notification->data);
            if (!$notification_data->project_id) {
                continue;
            }

            if (
                !$client = Project::where('id', $notification_data->project_id)->when($brand_id, function ($q) use ($brand_id) {
                    return $q->where('brand_id', $brand_id);
                })->first()
            ) {
                continue;
            }

            $notification_ids []= $notification->id;
        }

        else { continue; }
    }

    return DB::table('notifications')
        ->whereIn('id', $notification_ids)
        ->orderBy('created_at','desc')
        ->paginate(30);
}

function get_leads_by_brand ($brand_id, $start_date = null, $end_date = null) {
    $leads = [];
    foreach (
        DB::table('notifications')
            ->where('type', 'App\Notifications\LeadNotification')
//            ->where('notifiable_id', Auth::id())
            ->when(!is_null($start_date) && $start_date != "", function ($q) use ($start_date) {
                return $q->where('created_at', '>=', Carbon::parse($start_date));
            })
            ->when(!is_null($end_date) && $end_date != "", function ($q) use ($end_date) {
                return $q->where('created_at', '<=', Carbon::parse($end_date));
            })
            ->orderBy('created_at','desc')
            ->get() as $notification
    ) {
        $notification_data = json_decode($notification->data);
        if (!$notification_data->id) {
            continue;
        }

//        if (
//            !$client = Client::where('id', $notification_data->id)->where('brand_id', $brand_id)->first()
//        ) {
//            continue;
//        }

        $leads []= $notification_data;
    }

    return $leads;
}

function get_leads_count_by_brand ($brand_id) {
    $count = 0;
    foreach (
        DB::table('notifications')
            ->where('type', 'App\Notifications\LeadNotification')
            ->where('notifiable_id', Auth::id())
            ->orderBy('created_at','desc')
            ->get() as $notification
    ) {
        $notification_data = json_decode($notification->data);
        if (!$notification_data->id) {
            continue;
        }

        if (
            !$client = Client::where('id', $notification_data->id)->where('brand_id', $brand_id)->first()
        ) {
            continue;
        }

        $count += 1;
    }

    return $count;
}

function no_pending_tasks_left ($project_id) {
    if (!$project = Project::find($project_id)) {
        return false;
    }

    return ($project->tasks()->count() == 0) || array_unique($project->tasks()->pluck('status')->toArray()) === [3];
}

function get_brief_client_user_ids (Request $request = null, $brand_id = null) {
    if (is_null($brand_id)) {
        if ($request && $request->has('brand_id')) {
            $brand_id = $request->get('brand_id');
        }
    }
    $client_user_ids = [];

    $res = LogoForm::where('logo_name', '')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = WebForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SmmForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = ContentWritingForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SeoForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookFormatting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = AuthorWebsite::where('author_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookWriting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NoForm::whereHas('invoice', function ($query) {
        return $query->whereIn('brand', Auth::user()->brand_list());
    })->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
    ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Proofreading::where('description', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookCover::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Isbnform::where('pi_fullname', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Bookprinting::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SeoBrief::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookMarketing::where('client_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NewSMM::where('client_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = PressReleaseForm::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($brand_id, function ($q) use ($brand_id) {
            return $q->whereHas('invoice', function ($q) use ($brand_id) {
                return $q->where('brand', $brand_id);
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    return array_unique($client_user_ids);
}

function get_briefs_pending ($client_user_id) {
    $briefs_pending_array = [];

    if ((LogoForm::where('logo_name', '')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Logo brief';
    }

    if ((WebForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Website brief';
    }

    if ((SmmForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'SMM brief';
    }

    if ((ContentWritingForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Content writing brief';
    }

    if ((SeoForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'SEO brief';
    }

    if ((BookFormatting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Book formatting and publishing';
    }

    if ((AuthorWebsite::where('author_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Author website';
    }

    if ((BookWriting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Book writing';
    }

    if ((NoForm::whereHas('invoice', function ($query) {
        return $query->whereIn('brand', Auth::user()->brand_list());
    })->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'No Brief';
    }

    if ((Proofreading::where('description', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Editing & Proof reading';
    }

    if ((BookCover::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Cover design';
    }

    if ((Isbnform::where('pi_fullname', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'ISBN form';
    }

    if ((Bookprinting::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Book printing';
    }

    if ((SeoBrief::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'SEO';
    }

    if ((BookMarketing::where('client_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Book Marketing';
    }

    if ((NewSMM::where('client_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Social Media Marketing (NEW)';
    }

    if ((PressReleaseForm::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Press Release';
    }

    return $briefs_pending_array;
}

function get_project_client_user_ids () {
    $client_user_ids = [];

    $res = LogoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = WebForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SmmForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = ContentWritingForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SeoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookFormatting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookWriting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = AuthorWebsite::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Proofreading::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookCover::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Isbnform::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Bookprinting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->whereIn('brand', Auth::user()->brand_list());
                });
            })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SeoBrief::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookMarketing::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NewSMM::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = PressReleaseForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);


    return array_unique($client_user_ids);
}

function get_pending_projects ($client_user_id) {
    $user = User::find($client_user_id);
    $pending_projects = [];

    foreach (LogoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Logo brief',
            'id' => $item->id,
            'form_number' => 1,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (WebForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Website brief',
            'id' => $item->id,
            'form_number' => 2,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (SmmForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'SMM brief',
            'id' => $item->id,
            'form_number' => 3,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (ContentWritingForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Content writing brief',
            'id' => $item->id,
            'form_number' => 4,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (SeoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'SEO brief',
            'id' => $item->id,
            'form_number' => 5,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (BookFormatting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Book formatting and publishing',
            'id' => $item->id,
            'form_number' => 6,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (BookWriting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Book writing',
            'id' => $item->id,
            'form_number' => 7,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (AuthorWebsite::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Author website',
            'id' => $item->id,
            'form_number' => 8,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (Proofreading::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Editing & Proof reading',
            'id' => $item->id,
            'form_number' => 9,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (BookCover::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Cover design',
            'id' => $item->id,
            'form_number' => 10,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (Isbnform::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'ISBN form',
            'id' => $item->id,
            'form_number' => 11,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (Bookprinting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
         ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Book printing',
            'id' => $item->id,
            'form_number' => 12,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (NoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) use ($user) {
            return $q->whereHas('brands')->whereHas('client', function ($q) use ($user) {
                return $q->where('id', $user->client->id);
            });
        })
//         ->where('user_id', $client_user_id)->get() as $item) {
         ->get() as $item) {
        $pending_projects []= [
            'project_type' => 'No Form',
            'id' => $item->id,
            'form_number' => 0,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (SeoBrief::with('project')->doesntHave('project')
                 ->when(auth()->user()->is_employee != 2, function ($q) {
                     return $q->whereHas('invoice', function ($query) {
                         return $query->whereIn('brand', Auth::user()->brand_list());
                     });
                 })
                 ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
                 ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'SEO',
            'id' => $item->id,
            'form_number' => 13,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (BookMarketing::with('project')->doesntHave('project')
                 ->when(auth()->user()->is_employee != 2, function ($q) {
                     return $q->whereHas('invoice', function ($query) {
                         return $query->whereIn('brand', Auth::user()->brand_list());
                     });
                 })
                 ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
                 ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Book Marketing',
            'id' => $item->id,
            'form_number' => 14,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (NewSMM::with('project')->doesntHave('project')
                 ->when(auth()->user()->is_employee != 2, function ($q) {
                     return $q->whereHas('invoice', function ($query) {
                         return $query->whereIn('brand', Auth::user()->brand_list());
                     });
                 })
                 ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
                 ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Social Media Marketing (NEW)',
            'id' => $item->id,
            'form_number' => 15,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    foreach (PressReleaseForm::with('project')->doesntHave('project')
                 ->when(auth()->user()->is_employee != 2, function ($q) {
                     return $q->whereHas('invoice', function ($query) {
                         return $query->whereIn('brand', Auth::user()->brand_list());
                     });
                 })
                 ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
                 ->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Press Release',
            'id' => $item->id,
            'form_number' => 16,
            'brand_id' => $item->invoice->brands->id,
            'invoice_id' => $item->invoice_id
        ];
    }

    return $pending_projects;
}

function login_bypass ($email) {
    auth()->logout();

    if ($user = User::where('email', $email)->whereIn('is_employee', [0, 1, 2, 3, 4, 5, 6, 7])->first()) {
        auth()->login($user);

        if (auth()->check()) {
            Session::put('valid_user', true);

            $route_map = [
                0 => 'sale.home',
                1 => 'production.dashboard',
                2 => 'admin.home',
                3 => 'client.dashboard',
                4 => 'support.home',
                5 => 'member.dashboard',
                6 => 'salemanager.dashboard',
                7 => 'qa.dashboard',
                8 => 'billing.client.index',
            ];

            return redirect()->route($route_map[auth()->user()->is_employee]);
        }
    }

    return redirect()->route('login');
}

function get_invoice_totals ($invoice_ids) {
    $invoice_totals = [];
    foreach (\App\Models\Currency::all() as $currency) { $invoice_totals[str_replace('Â', '', $currency->sign)] = 0.00; }

    foreach (
        Invoice::whereIn('id', $invoice_ids)->get() as $invoice
    ) {
        $invoice_totals[str_replace('Â', '', $invoice->_currency->sign)] += $invoice->amount;
    }

    foreach ($invoice_totals as $key => $value) { if ($value == 0.00) { unset($invoice_totals[$key]); } }

    return $invoice_totals;
}

function get_invoice_totals_in_usd ($invoice_ids) {
    $invoice_totals = [];
    $total = 0.00;

    foreach (
        Invoice::whereIn('id', $invoice_ids)->get() as $invoice
    ) {
        $total += $invoice->amount;
    }

    return $total;
}

function get_invoice_refunds_totals_in_usd ($invoice_ids) {
    $invoice_totals = [];
    $total = 0.00;

    foreach (
        Invoice::whereIn('id', $invoice_ids)->whereNotNull('refunded_cb')->get() as $invoice
    ) {
        $total += $invoice->refunded_cb;
    }

    return $total;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function fetch_search_bar_content ($query = null) {
    $final = [];

    //clients
    $clients = Client::
        when(in_array(auth()->user()->is_employee, [0, 4, 6]), function ($q) {
            return $q->whereIn('brand_id', Auth::user()->brand_list());
        })
        ->when($query && $query != "", function ($q) use ($query) {
            return $q->where(function($q) use ($query) {
                return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$query.'%')
                    ->orWhere('name', 'like', '%'.$query.'%')
                    ->orWhere('last_name', 'like', '%'.$query.'%')
                    ->orWhere('email', 'like', '%'.$query.'%');
            });
        })
        ->orderBy('created_at', 'DESC')->take(10)->get();

    foreach ($clients as $client) {
        $label = $client->name . " " . $client->last_name . (in_array(auth()->user()->is_employee, [0, 2, 6]) ? "    (".$client->email.")" : "");
        $final []= [
            'label' => $label,
            'category' => 'Clients',
            'id' => $client->id,
        ];
    }

    //brands
    $brands = \App\Models\Brand::
        when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereIn('id', auth()->user()->brand_list());
        })
        ->when($query, function ($q) use ($query) {
            return $q->where('name', 'like', '%'.$query.'%');
        })
        ->orderBy('created_at', 'DESC')->take(10)->get();
    foreach ($brands as $brand) {
        $final []= [
            'label' => $brand->name,
            'category' => 'Brands',
            'id' => $brand->id,
        ];
    }

    return json_encode($final);
}

function emit_pusher_notification ($channel, $event, $data) {
    try {
        $pusher = new \Pusher\Pusher('7d1bc788fe2aaa7a2ea5', '5758ce8139ba816eb7d7', '1838156', [
            'cluster' => 'ap2',
            'useTLS' => true
        ]);

        $pusher->trigger($channel, $event, $data);

        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function check_if_external_client (Request $request) {
    if (!$client = Client::where([ 'brand_id' => $request->get('brand_id'), 'email' => $request->get('email') ])->first()) {
        return '';
    }

    if ($user = User::where('client_id', $client->id)->first()) {
        return '';
    }

    if (is_null($client->url) || $client->added_by) {
        return '';
    }

    $edit_client_map = [
        2 => route('admin.client.edit', $client->id),
        6 => route('manager.client.edit', $client->id),
        0 => route('client.edit', $client->id),
        4 => route('support.client.edit', $client->id),
    ];

    return $edit_client_map[intval(Auth::user()->is_employee)] ?? '';
}

function remove_div_tags ($input) {
    // Use a regular expression to remove all opening and closing <div> tags
    $output = preg_replace('/<\/?div[^>]*>/', '', $input);
    return $output;
}

function client_user_has_unread_message ($client_user_id) {
    return CRMNotification::
        where('client_user_id', $client_user_id)
        ->where('type', 'App\Notifications\MessageNotification')
        ->whereNull('read_at')
//        ->whereJsonContains('data->id', $client_user_id)
        ->exists();
}

function get_unread_notification_count_for_buh () {
    $notification_count = 0;

    foreach (User::where('is_employee', 3)->where('client_id', '!=', 0)->whereHas('client', function ($q) {
        return $q->whereIn('brand_id', auth()->user()->brand_list());
    })->orderBy('id', 'desc')->pluck('id') as $client_user_id) {
        $unread_messages_count = Message::where('role_id', '3')->where('client_id', $client_user_id)->whereNull('is_read')->count();
        if ($unread_messages_count > 0) {
            $notification_count += 1;
        }
//        if (client_user_has_unread_message($client_user_id)) {
//            $notification_count += 1;
//        }
    }

    return $notification_count;
}

function purge_notifications ($date) {
    if (!$date) { return false; }

    CRMNotification::whereDate('created_at', '<=', Carbon::parse($date))->delete();

    return true;
}

function get_buh_ids_by_brand_id ($brand_id) {
    $user_ids = array_unique(DB::table('brand_users')->where('brand_id', $brand_id)->pluck('user_id')->toArray());

    return User::where('is_employee', 6)->whereIn('id', $user_ids)->pluck('id')->toArray();
}

function get_qa_ids_by_category_id ($category_id) {
    $user_ids = array_unique(DB::table('category_users')->where('category_id', $category_id)->pluck('user_id')->toArray());

    return User::where('is_employee', 7)->whereIn('id', $user_ids)->pluck('id')->toArray();
}

function clear_notification ($notification_id) {
//    if ($notification = auth()->user()->notifications()->find($notification_id)) {
    if ($notification = DatabaseNotification::find($notification_id)) {
        if ($notification->unread()) {
            $notification->markAsRead();

            return true;
        }

        return false;
    }

    return false;
}

function notify_qa_of_incoming_task ($task_id) {
    if (!$task = Task::find($task_id)) {
        return false;
    }

    $user_name = auth()->user()->name.' '.auth()->user()->last_name;
    $message = 'Task # '.$task_id.' has been sent to QA by '.$user_name.'.';

    $assignData = [
        'id' => auth()->user()->id,
        'task_id' => $task_id,
        'name' => $user_name,
        'text' => $message,
        'details' => '',
    ];

    foreach (get_qa_ids_by_category_id($task->category_id) as $qa_id) {
        if ($qa = User::find($qa_id)) {
            $qa->notify(new TaskNotification($assignData));
        }
    }

    return true;
}

function notify_qa_of_outgoing_task ($task_id) {
    if (!$task = Task::find($task_id)) {
        return false;
    }

    //check if any qa actually done on task
    if (!count($task->qa_feedbacks)) {
        return false;
    }

    $user_name = auth()->user()->name.' '.auth()->user()->last_name;
    $message = 'Task # '.$task_id.' has been marked as COMPLETE by '.$user_name.'.';

    $assignData = [
        'id' => auth()->user()->id,
        'task_id' => $task_id,
        'name' => $user_name,
        'text' => $message,
        'details' => '',
    ];

    foreach (get_qa_ids_by_category_id($task->category_id) as $qa_id) {
        if ($qa = User::find($qa_id)) {
            $qa->notify(new TaskNotification($assignData));
        }
    }

    return true;
}

function get_restricted_brand_ids_for_qa () {
    $restricted_brand_ids = [];
    $danny_brand_ids = [3, 10, 16, 17, 21, 22, 26, 33, 34, 51, 48, 44, 27];
    $ashmara_brand_ids = [13, 23, 38, 36, 37, 66];
    $zech_brand_ids = [1, 8, 5, 31, 32, 46, 49, 50, 57, 60, 47, 67, 60];
    $new = [65, 68, 69, 70, 71];

    return array_merge($danny_brand_ids, $ashmara_brand_ids, $zech_brand_ids, $new);
}

function get_auth_category_ids () {
    $category_id_array = array();
    foreach(auth()->user()->category as $category){
        array_push($category_id_array, $category->id);
    }

    return $category_id_array;
}

function create_client_auth ($data) {
    try {
        $pass = $data['pass'];
        $id = $data['id'];
        $client = Client::find($id);
        $user = new User();
        $user->name = $client->name;
        $user->last_name = $client->last_name;
        $user->email = $client->email;
        $user->contact = $client->contact;
        $user->status = 1;
        $user->password = Hash::make($pass);
        $user->is_employee = 3;
        $user->client_id = $id;
        $user->save();

        //mail_notification
        $brand = Brand::find($client->brand_id);

        $html = '<p>'. 'Dear ' . $client->name . ',' .'</p>';
        $html .= '<p>'. 'Welcome to '.$brand->name.'! We are excited to have you on board.' .'</p>';
        $html .= '<p>'. 'Your account has been successfully created. Below are your login credentials and some basic instructions to help you get started:' .'</p>';
        $html .= '<p><ul>'. '<li><strong>*Username: '.$client->email.'</strong></li><li><strong>*Password: '.$pass.'</strong></li>' .'</ul></p>';
        $html .= '<p>'. 'For your security, please change your password upon your first login. You can access your account here: <a href="'.route('login').'">'.route('login').'</a>' .'</p>';
        $html .= '<p>'. 'If you have any questions or need further assistance, please do not hesitate to contact our support team.' .'</p>';
        $html .= '<p>'. 'Welcome aboard!' .'</p>';
        $html .= '<p>'. 'Best Regards,' .'</p>';
        $html .= '<p>'. $brand->name .'.</p>';

        mail_notification(
            '',
            [$client->email],
            'Welcome to '.$brand->name.' – Your Account is Ready!',
            view('mail.crm-mail-template')->with([
                'subject' => 'Welcome to '.$brand->name.' – Your Account is Ready!',
                'brand_name' => $brand->name,
                'brand_logo' => asset($brand->logo),
                'additional_html' => $html
            ]),
//            true
        );

        return true;
    } catch (\Exception $e) {
        return response()->json(['success' => false , 'message' => $e->getMessage()]);
    }
}

function create_update_auth ($data) {
    try {
        $id = $data['id'];
        $pass = $data['pass'];
        $user = User::where('client_id', $id)->first();
        $user->password = Hash::make($pass);
        $user->save();

        $client = Client::find($id);

        //mail_notification
        $brand = Brand::find($client->brand_id);

        $html = '<p>'. 'Dear ' . $client->name . ',' .'</p>';
        $html .= '<p>'. 'Your account credentials have been reset. Below are your login credentials:' .'</p>';
        $html .= '<p><ul>'. '<li><strong>*Username: '.$client->email.'</strong></li><li><strong>*Password: '.$pass.'</strong></li>' .'</ul></p>';
        $html .= '<p>'. 'You can access your account here: <a href="'.route('login').'">'.route('login').'</a>' .'</p>';
        $html .= '<p>'. 'If you have any questions or need further assistance, please do not hesitate to contact our support team.' .'</p>';
        $html .= '<p>'. 'Best Regards,' .'</p>';
        $html .= '<p>'. $brand->name .'.</p>';

        mail_notification(
            '',
            [$client->email],
            'CRM | Password reset',
            view('mail.crm-mail-template')->with([
                'subject' => 'CRM | Password reset',
                'brand_name' => $brand->name,
                'brand_logo' => asset($brand->logo),
                'additional_html' => $html
            ]),
//            true
        );

        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function create_clients_merchant_accounts ($client_id) {
    $res = create_stripe_customer($client_id);

    return $res;
}

function create_stripe_customer ($client_id) {
    $client = Client::find($client_id);

    if (!is_null($client->stripe_customer_id)) {
        return true;
    }

    $stripe = new \Stripe\StripeClient('sk_test_51PwC1NKIH1Ehl47nVzMn8kCWtLqWlPv9bsjRm26tUi3sTUCacRZ3aiw4autOySf3rSt965n1EBGv5EwyQAfApxu300wnj3IhbS');

    $stripe_customer_res = $stripe->customers->create([
        'name' => $client->name . ' ' . $client->last_name,
        'email' => $client->email
    ]);

    if (!$stripe_customer_res->id) {
        return false;
    }

    $client->stripe_customer_id = $stripe_customer_res->id;
    $client->save();

    return true;
}

function create_stripe_invoice ($invoice_id, $currency = 'usd') {
    if (!$invoice = Invoice::find($invoice_id)) {
        return false;
    }

    if (($invoice->amount * 100) < 100) {
        return false;
    }

    if (!$client = Client::find($invoice->client_id)) {
        return false;
    }

    if (is_null($client->stripe_customer_id)) {
        create_stripe_customer($client->id);

        $client = Client::find($invoice->client_id);
    }

    $stripe = new \Stripe\StripeClient('sk_test_51PwC1NKIH1Ehl47nVzMn8kCWtLqWlPv9bsjRm26tUi3sTUCacRZ3aiw4autOySf3rSt965n1EBGv5EwyQAfApxu300wnj3IhbS');

    $stripe_invoice_res = $stripe->invoices->create([
        'customer' => $client->stripe_customer_id,
        'collection_method' => 'send_invoice',
        'currency' => $currency,
        'due_date' => Carbon::now()->addYear()->timestamp,
    ]);

    $stripe_price_res = $stripe->prices->create([
        'currency' => $currency,
        'unit_amount' => $invoice->amount * 100,
        'product_data' => ['name' => 'Gold Plan'],
    ]);

    $stripe_invoice_item_res = $stripe->invoiceItems->create([
        'invoice' => $stripe_invoice_res->id,
        'customer' => $client->stripe_customer_id,
        'currency' => $currency,
        'price' => $stripe_price_res->id
    ]);

    $stripe->invoices->finalizeInvoice($stripe_invoice_res->id);
    $stripe_invoice = $stripe->invoices->retrieve($stripe_invoice_res->id);

    $invoice->stripe_invoice_id = $stripe_invoice->id;
    $invoice->stripe_invoice_url = $stripe_invoice->hosted_invoice_url;
    $invoice->save();

    return true;
}

function get_authorize_keys ($merchant_id) {
    $keys_map = [
        3 => [
            'login_id' => '5vExY98p',
//            'transaction_key' => '6P7r37fD58pVLmRA',
            'transaction_key' => '5W2q4ks2aME95DRT',
        ],
        5 => [
            'login_id' => '9GeNn93MCpVn',
            'transaction_key' => '5Q3xv7WGvF939axv',
        ],
        7 => [
            'login_id' => '7HaWx27h',
            'transaction_key' => '3N5DcFd49PB3um3g',
        ],
        8 => [
            'login_id' => '4uBtJ26yL',
            'transaction_key' => '4p2b4KQb5836U2hq',
        ],
        9 => [
            'login_id' => '3ZY6f2nX',
            'transaction_key' => '63Ya68h6SC87tkRt',
        ],
        10 => [
            'login_id' => '8EyHx4tG2tP2',
//            'transaction_key' => '8gk77wQQ2UY3398V',
            'transaction_key' => '622w76eD3T83Ybu8',
        ],
        11 => [
            'login_id' => '46Ef5LqK',
            'transaction_key' => '56Kd3w32QUUh39pN',
        ]
    ];

    return $keys_map[$merchant_id];
}

function authorize_charge ($data) {
    try {
        $invoice = Invoice::find($data['invoice_id']);
        $currency = strtoupper($invoice->_currency->short_name ?? 'usd');
        $client = Client::find($invoice->client_id);

        $keys = get_authorize_keys($invoice->merchant_id);

        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($keys['login_id']);
        $merchantAuthentication->setTransactionKey($keys['transaction_key']);

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($data['card_number']);
        $creditCard->setExpirationDate($data['exp_year'] . "-" . $data['exp_month']);
        $creditCard->setCardCode($data['cvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($data['invoice_id']);
//        $order->setDescription($invoice->discription);
        $order->setDescription('DesignCRM | Invoice');

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($client->name);
        $customerAddress->setLastName($client->last_name);
        $customerAddress->setCompany("company");
        $customerAddress->setAddress($data['address']);
        $customerAddress->setCity($data['city']);
        $customerAddress->setState($data['state']);
        $customerAddress->setZip($data['zip']);
        $customerAddress->setCountry($data['country']);

        \App\Models\ClientBillingInfo::create([
            'invoice_id' => $invoice->id,
            'country' => $data['country'],
            'city' => $data['city'],
            'state' => $data['state'],
            'address' => $data['address'],
            'zip_code' => $data['zip'],
        ]);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($client->id);
        $customerData->setEmail($client->email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

//        // Add some merchant defined fields. These fields won't be stored with the transaction,
//        // but will be echoed back in the response.
//        $merchantDefinedField1 = new AnetAPI\UserFieldType();
//        $merchantDefinedField1->setName("customerLoyaltyNum");
//        $merchantDefinedField1->setValue("1128836273");

//        $merchantDefinedField2 = new AnetAPI\UserFieldType();
//        $merchantDefinedField2->setName("favoriteColor");
//        $merchantDefinedField2->setValue("blue");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($invoice->amount);
        $transactionRequestType->setCustomerIP($data['end_user_ip']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

//        // Add currency code setting
//        $currencySetting = new AnetAPI\SettingType();
//        $currencySetting->setSettingName("currencyCode");
//        $currencySetting->setSettingValue($currency);
//        $transactionRequestType->addToTransactionSettings($currencySetting); // Add currency setting
//        $transactionRequestType->addToUserFields($merchantDefinedField1);
//        $transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);


        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
//                    echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
//                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
//                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
//                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
//                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";

                    return [
                        'success' => true,
                        'data' => [
                            'transaction_id' => $tresponse->getTransId()
                        ],
                        'message' => 'Transaction successfull!',
                    ];
                } else {
//                    echo "Transaction Failed \n";
//                    if ($tresponse->getErrors() != null) {
//                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
//                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
//                    }

                    return [
                        'success' => false,
                        'data' => [],
                        'message' => $tresponse->getErrors()[0]->getErrorText(),
                    ];
                }
                // Or, print errors if the API request wasn't successful
            } else {
                echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
//                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
//                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";

                    return [
                        'success' => false,
                        'data' => [],
                        'message' => $tresponse->getErrors()[0]->getErrorText(),
                    ];
                } else {
//                    echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
//                    echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";

                    return [
                        'success' => false,
                        'data' => [],
                        'message' => $response->getMessages()->getMessage()[0]->getText(),
                    ];
                }
            }
        } else {
//            echo  "No response returned \n";

            return [
                'success' => false,
                'data' => [],
                'message' => 'No response returned',
            ];
        }

        return $response;
    } catch (\Exception $e) {
        return [
            'success' => false,
            'data' => [],
            'message' => $e->getMessage(),
        ];
    }
}

function get_authorize_token ($invoice_id) {
    $invoice = Invoice::find($invoice_id);
    $keys = get_authorize_keys($invoice->merchant_id);
    $loginID = $keys['login_id'];
    $transactionKey = $keys['transaction_key'];

    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName($loginID);
    $merchantAuthentication->setTransactionKey($transactionKey);

    // Set the transaction's refId
    $refId = 'ref' . time();

    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction");
    $transactionRequestType->setAmount($invoice->amount);

    // Set Hosted Form options
    $setting1 = new AnetAPI\SettingType();
    $setting1->setSettingName("hostedPaymentButtonOptions");
    $setting1->setSettingValue("{\"text\": \"Pay\"}");

    $setting2 = new AnetAPI\SettingType();
    $setting2->setSettingName("hostedPaymentOrderOptions");
    $setting2->setSettingValue("{\"show\": false}");

    $setting3 = new AnetAPI\SettingType();
    $setting3->setSettingName("hostedPaymentReturnOptions");
    $setting3->setSettingValue(
        "{\"url\": \"".route('confirm.authorize.payment', $invoice_id)."\", \"cancelUrl\": \"".route('client.pay.with.authorize', $invoice_id)."\", \"showReceipt\": true}"
    );

    // Build transaction request
    $request = new AnetAPI\GetHostedPaymentPageRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest($transactionRequestType);

    $request->addToHostedPaymentSettings($setting1);
    $request->addToHostedPaymentSettings($setting2);
    $request->addToHostedPaymentSettings($setting3);

    //execute request
    $controller = new AnetController\GetHostedPaymentPageController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo $response->getToken()."\n";
    } else {
        echo "ERROR :  Failed to get hosted payment page token\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "RESPONSE : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response->getToken() ?? '';
}

function mark_invoice_as_paid ($invoice_id) {
    if (!$invoice = Invoice::find($invoice_id)) {
        return false;
    }

    $invoice->payment_status = 2;
    $invoice->invoice_date = Carbon::today()->toDateTimeString();
    $invoice->save();

    if (!$user = Client::find($invoice->client_id)) {
        $user = Client::where('email', $invoice->client->email)->first();
    }
    $user_client = User::where('client_id', $user->id)->first();
    if($user_client != null || $user->user){
        $service_array = explode(',', $invoice->service);
        for($i = 0; $i < count($service_array); $i++){
            $service = Service::find($service_array[$i]);
            if($service->form == 0){
                //No Form
                //if($invoice->createform == 1){
                $no_form = new NoForm();
                $no_form->name = $invoice->custom_package;
                $no_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $no_form->user_id = $user_client->id;
                }
                $no_form->client_id = $user->id;
                $no_form->agent_id = $invoice->sales_agent_id;
                $no_form->save();
                //}
            }elseif($service->form == 1){
                // Logo Form
                //if($invoice->createform == 1){
                $logo_form = new LogoForm();
                $logo_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $logo_form->user_id = $user_client->id;
                }
                $logo_form->client_id = $user->id;
                $logo_form->agent_id = $invoice->sales_agent_id;
                $logo_form->save();
                //}
            }elseif($service->form == 2){
                // Website Form
                //if($invoice->createform == 1){
                $web_form = new WebForm();
                $web_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $web_form->user_id = $user_client->id;
                }
                $web_form->client_id = $user->id;
                $web_form->agent_id = $invoice->sales_agent_id;
                $web_form->save();
                //}
            }elseif($service->form == 3){
                // Smm Form
                //if($invoice->createform == 1){
                $smm_form = new SmmForm();
                $smm_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $smm_form->user_id = $user_client->id;
                }
                $smm_form->client_id = $user->id;
                $smm_form->agent_id = $invoice->sales_agent_id;
                $smm_form->save();
                //}
            }elseif($service->form == 4){
                // Content Writing Form
                //if($invoice->createform == 1){
                $content_writing_form = new ContentWritingForm();
                $content_writing_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $content_writing_form->user_id = $user_client->id;
                }
                $content_writing_form->client_id = $user->id;
                $content_writing_form->agent_id = $invoice->sales_agent_id;
                $content_writing_form->save();
                //}
            }elseif($service->form == 5){
                // Search Engine Optimization Form
                //if($invoice->createform == 1){
                $seo_form = new SeoForm();
                $seo_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $seo_form->user_id = $user_client->id;
                }
                $seo_form->client_id = $user->id;
                $seo_form->agent_id = $invoice->sales_agent_id;
                $seo_form->save();
                //}
            }elseif($service->form == 6){
                // Book Formatting & Publishing
                //if($invoice->createform == 1){
                $book_formatting_form = new BookFormatting();
                $book_formatting_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $book_formatting_form->user_id = $user_client->id;
                }
                $book_formatting_form->client_id = $user->id;
                $book_formatting_form->agent_id = $invoice->sales_agent_id;
                $book_formatting_form->save();
                //}
            }elseif($service->form == 7){
                // Book Formatting & Publishing
                //if($invoice->createform == 1){
                $book_writing_form = new BookWriting();
                $book_writing_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $book_writing_form->user_id = $user_client->id;
                }
                $book_writing_form->client_id = $user->id;
                $book_writing_form->agent_id = $invoice->sales_agent_id;
                $book_writing_form->save();
                //}
            }elseif($service->form == 8){
                // Author Website
                //if($invoice->createform == 1){
                $author_website_form = new AuthorWebsite();
                $author_website_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $author_website_form->user_id = $user_client->id;
                }
                $author_website_form->client_id = $user->id;
                $author_website_form->agent_id = $invoice->sales_agent_id;
                $author_website_form->save();
                //}
            }elseif($service->form == 9){
                // Author Website
                //if($invoice->createform == 1){
                $proofreading_form = new Proofreading();
                $proofreading_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $proofreading_form->user_id = $user_client->id;
                }
                $proofreading_form->client_id = $user->id;
                $proofreading_form->agent_id = $invoice->sales_agent_id;
                $proofreading_form->save();
                //}
            }elseif($service->form == 10){
                // Author Website
                //if($invoice->createform == 1){
                $bookcover_form = new BookCover();
                $bookcover_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $bookcover_form->user_id = $user_client->id;
                }
                $bookcover_form->client_id = $user->id;
                $bookcover_form->agent_id = $invoice->sales_agent_id;
                $bookcover_form->save();
                //}
            }elseif($service->form == 11){
                // Author Website
                //if($invoice->createform == 1){
                $isbn_form = new Isbnform();
                $isbn_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $isbn_form->user_id = $user_client->id;
                }
                $isbn_form->client_id = $user->id;
                $isbn_form->agent_id = $invoice->sales_agent_id;
                $isbn_form->save();
                //}
            }
            elseif($service->form == 12){
                // Author Website
                //if($invoice->createform == 1){
                $book_printing = new Bookprinting();
                $book_printing->invoice_id = $invoice->id;
                if($user_client != null){
                    $book_printing->user_id = $user_client->id;
                }
                $book_printing->client_id = $user->id;
                $book_printing->agent_id = $invoice->sales_agent_id;
                $book_printing->save();
                //}
            }
            elseif($service->form == 13){
                $seo_form = new SeoBrief();
                $seo_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $seo_form->user_id = $user_client->id;
                }
                $seo_form->client_id = $user->id;
                $seo_form->agent_id = $invoice->sales_agent_id;
                $seo_form->save();
            }
            elseif($service->form == 14){
                $book_marketing_form = new BookMarketing();
                $book_marketing_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $book_marketing_form->user_id = $user_client->id;
                }
                $book_marketing_form->client_id = $user->id;
                $book_marketing_form->agent_id = $invoice->sales_agent_id;
                $book_marketing_form->save();
            }
            elseif($service->form == 15){
                $new_smm_form = new NewSMM();
                $new_smm_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $new_smm_form->user_id = $user_client->id;
                }
                $new_smm_form->client_id = $user->id;
                $new_smm_form->agent_id = $invoice->sales_agent_id;
                $new_smm_form->save();
            }
            elseif($service->form == 16){
                $press_release_form = new PressReleaseForm();
                $press_release_form->invoice_id = $invoice->id;
                if($user_client != null){
                    $press_release_form->user_id = $user_client->id;
                }
                $press_release_form->client_id = $user->id;
                $press_release_form->agent_id = $invoice->sales_agent_id;
                $press_release_form->save();
            }


        }
    }

    return true;
}

function get_clients_priority_badge ($client_id, $small = false) {
    if (!$client = Client::find($client_id)) {
        return '';
    }

    $badge_map = [
        1 => 'danger',
        2 => 'warning',
        3 => 'info',
    ];

    $badge_map_2 = [
        1 => 'HIGH',
        2 => 'MEDIUM',
        3 => 'LOW',
    ];

    return '<span class="span_client_priority_badge badge badge-'.$badge_map[$client->priority] . ($small ? ' badge-sm' : ''). '">' .$badge_map_2[$client->priority].'</span>';
}

function get_month_name($monthNumber) {
    $monthName = Carbon::createFromFormat('m', $monthNumber)->format('F');
    return $monthName;
}

function buh_merchant_map () {
    return [
        33 => [5, 6, 11],
        18 => [7, 6, 11],
        1169 => [8, 6, 11],
        7 => [3, 9, 10, 6, 11],
        4191 => [11],
        4491 => [11],
        //testing
        3425 => [1, 2, 3, 4],
    ];
}

function get_my_buh () {
    if (auth()->user()->is_employee == 6) {
        return auth()->id();
    }

    $buh_ids = User::where('is_employee', 6)->pluck('id')->toArray();

    $buh_ids_array = DB::table('brand_users')
        ->whereIn('user_id', $buh_ids)
        ->whereIn('brand_id', auth()->user()->brand_list())
        //exclude QA BUHs
        ->whereNotIn('user_id', [2260, 3837, 3839, 3838, 4192])
        ->pluck('user_id')->toArray();

    $buh_count_map = [];
    foreach ($buh_ids_array as $buh_id) {
        if (!isset($buh_count_map[$buh_id])) {
            $buh_count_map[$buh_id] = 0;
        }

        $buh_count_map[$buh_id] += 1;
    }

    if ($buh_count_map == [] && auth()->id() == 1) {
        $buh_count_map[3425] = 1;
    }

    $max_count = max($buh_count_map);
    $buh_id_with_max_count = array_keys($buh_count_map, $max_count);

    return $buh_id_with_max_count[0] ?? null;
}

function get_my_merchants () {
    if (auth()->id() == 1) {
        return Merchant::all();
    }

    $my_buh_id = get_my_buh();
    if (is_null($my_buh_id)) {
        return Merchant::where('id', 6)->get();
    }

    $map = buh_merchant_map();
    if (!isset($map[$my_buh_id])) {
        return Merchant::where('id', 6)->get();
    }

    return Merchant::whereIn('id', $map[$my_buh_id])
        //Wire
        ->orWhere('id', 6)
        ->get();
}

function populate_clients_show_service_forms (Client $client) {
    $invoice_services = [];

    foreach (Invoice::where('client_id', $client->id)->get() as $invoice) {
        $inv_services = explode(',', $invoice->service);
        $invoice_services = array_merge($invoice_services, $inv_services);
    }

    $client->show_service_forms = implode(',', array_unique($invoice_services));
    $client->save();
}

function get_clients_show_service_forms ($client_id) {
    $return = [];

    if ($client = Client::find($client_id)) {
        $return = explode(',', $client->show_service_forms) ?? [];
    }

    return $return;
}

function get_clients_show_service_form_types ($client_id) {
    return array_unique(Service::whereIn('id', get_clients_show_service_forms($client_id))->pluck('form')->toArray());
}

function get_clients_service_status_data () {
    $in_progress_count = Project::where('client_id', auth()->id())->where('service_status', 0)->count();
    $on_hold_count = Project::where('client_id', auth()->id())->where('service_status', 1)->count();
    $completed_count = Project::where('client_id', auth()->id())->where('service_status', 2)->count();

    return [
        $in_progress_count,
        $on_hold_count,
        $completed_count
    ];
}

function lead_status_color_class_map ($status) {
    $map = [
        'Closed' => 'danger',
        'On Discussion' => 'warning',
        'Onboarded' => 'success',
    ];

    return $map[$status] ?? 'info';
}

function form_checker_model_map ($form_checker) {
    $map = [
        0 => NoForm::class,
        1 => LogoForm::class,
        2 => WebForm::class,
        3 => SmmForm::class,
        4 => ContentWritingForm::class,
        5 => SeoForm::class,
        6 => BookFormatting::class,
        7 => BookWriting::class,
        8 => AuthorWebsite::class,
        9 => Proofreading::class,
        10 => BookCover::class,
        11 => Isbnform::class,
        12 => Bookprinting::class,
        13 => SeoBrief::class
    ];

    return $map[$form_checker] ?? false;
}

function limitTextAtWord($text, $limit = 100, $ellipsis = '...') {
    if (strlen($text) > $limit) {
        $text = wordwrap($text, $limit);
        $text = substr($text, 0, strpos($text, "\n")) . $ellipsis;
    }
    return $text;
}

function get_crm_tutorials () {
    return [
        [
            'title' => 'How to add client on DesignCRM',
            'src' => asset('video/how-to-add-client-on-DesignCRM.mp4'),
        ],
    ];
}

function get_my_members_by_category ($category_id) {
    if (!auth()->check() || auth()->user()->is_employee != 1) {
        return [];
    }

    return User::select('id', 'name', 'email', 'last_name')->where('is_employee', 5)->whereHas('category', function ($query) use ($category_id){
        return $query->where('category_id', '=', $category_id);
    })->get();
}

function get_authorize_merchant_ids () {
    return [3, 5, 7, 8, 9, 10, 11];
}
