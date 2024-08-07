<?php

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\CRMNotification;
use App\Models\Invoice;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\NoForm;
use App\Models\Project;
use App\Models\Proofreading;
use App\Models\SeoForm;
use App\Models\SmmForm;
use App\Models\Task;
use App\Models\User;
use App\Models\WebForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

function mail_notification ($from, $to, $subject, $html, $for_admin = false) {
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

function get_brief_client_user_ids (Request $request = null) {
    $client_user_ids = [];

    $res = LogoForm::where('logo_name', '')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->whereIn('brand', Auth::user()->brand_list());
            });
        })
        ->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NoForm::whereHas('invoice', function ($query) {
        return $query->whereIn('brand', Auth::user()->brand_list());
    })->whereHas('invoice', function ($q) { return $q->whereHas('brands'); })
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
        ->when($request && $request->has('brand_id'), function ($q) use ($request) {
            return $q->whereHas('invoice', function ($q) use ($request) {
                return $q->where('brand', $request->get('brand_id'));
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
                3 => 'client.home',
                4 => 'support.home',
                5 => 'member.dashboard',
                6 => 'salemanager.dashboard',
                7 => 'qa.home',
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
    //testing
    return false;

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
        if (client_user_has_unread_message($client_user_id)) {
            $notification_count += 1;
        }
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