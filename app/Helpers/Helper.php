<?php

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Client;
use App\Models\ContentWritingForm;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

function mail_notification ($from, $to, $subject, $html, $for_admin = false) {
    try {
        $mails = $to;

        if ($for_admin) { $mails []= 'test-crm@mailinator.com'; }

        Mail::send([], [], function ($message) use ($from, $to, $subject, $html, $mails) {
            $message->to($mails)
            ->subject($subject)
            ->setBody($html, 'text/html');
        });

//        foreach ($mails as $mail) {
//            mail($mail, $subject, $html, "Content-Type: text/html; charset=UTF-8");
//        }

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
    ];

    return ucfirst($arr[$num]) ?? 'Open';
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

function get_brief_client_user_ids () {
    $client_user_ids = [];

    $res = LogoForm::where('logo_name', '')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = WebForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SmmForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = ContentWritingForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SeoForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookFormatting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = AuthorWebsite::where('author_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookWriting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NoForm::whereHas('invoice', function ($query) {
        return $query->where('brand', Auth::user()->brand_list());
    })->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Proofreading::where('description', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookCover::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Isbnform::where('pi_fullname', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Bookprinting::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Logo brief';
    }

    if ((WebForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Website brief';
    }

    if ((SmmForm::where('business_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'SMM brief';
    }

    if ((ContentWritingForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Content writing brief';
    }

    if ((SeoForm::where('company_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'SEO brief';
    }

    if ((BookFormatting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Book formatting and publishing';
    }

    if ((AuthorWebsite::where('author_name', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Author website';
    }

    if ((BookWriting::where('book_title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Book writing';
    }

    if ((NoForm::whereHas('invoice', function ($query) {
        return $query->where('brand', Auth::user()->brand_list());
    })->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'No Brief';
    }

    if ((Proofreading::where('description', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Editing & Proof reading';
    }

    if ((BookCover::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'Cover design';
    }

    if ((Isbnform::where('pi_fullname', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
        ->where('user_id', $client_user_id)->count()) > 0) {
        $briefs_pending_array []= 'ISBN form';
    }

    if ((Bookprinting::where('title', null)
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })
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
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = WebForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SmmForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = ContentWritingForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = SeoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookFormatting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookWriting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = AuthorWebsite::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Proofreading::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = BookCover::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Isbnform::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = Bookprinting::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);

    $res = NoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($query) {
                    return $query->where('brand', Auth::user()->brand_list());
                });
            })
        ->groupBy('user_id')->pluck('user_id')->toArray();
    $client_user_ids = array_merge($res, $client_user_ids);


    return array_unique($client_user_ids);
}

function get_pending_projects ($client_user_id) {
    $pending_projects = [];

    foreach (LogoForm::with('project')->doesntHave('project')
        ->when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereHas('invoice', function ($query) {
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
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
                return $query->where('brand', Auth::user()->brand_list());
            });
        })->where('user_id', $client_user_id)->get() as $item) {
        $pending_projects []= [
            'project_type' => 'Book printing',
            'id' => $item->id,
            'form_number' => 12,
            'brand_id' => $item->invoice->brands->id,
        ];
    }

    return $pending_projects;
}