<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

function mail_notification ($from, $to, $subject, $html, $for_admin = false) {
    try {
        $mails = $to;

        if ($for_admin) { $mails []= 'ssidduit@gmail.com'; }

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