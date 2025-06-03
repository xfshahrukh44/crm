<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubtaskController extends Controller
{
    public function store (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'description' => 'required',
            'duedate' => 'required|after_or_equal:today',
        ]);

        $request->request->add(['user_id' => auth()->id()]);
        $sub_task = SubTask::create($request->all());
        $cat_id = $sub_task->task->category_id;
        $data = SubTask::find($sub_task->id);
        $duedate = null;
        if($data->duedate != null){
            $duedate = date('d M, y', strtotime($data->duedate));
        }
        $leads = User::where('is_employee', 1)->where('status', 1)->whereHas('category', function($q) use ($cat_id){
            $q->where('category_id', $cat_id);
        })->get();
        $taskData = [
            'id' => auth()->user()->id,
            'name' => 'Task Assigned by ' . auth()->user()->name . ' ' . auth()->user()->last_name,
            'task_id' => $sub_task->task->id,
            'sub_task_id' => $sub_task->id,
            'text' => 'Task Assigned by ' . auth()->user()->name . ' ' . auth()->user()->last_name . ' (' . $sub_task->task->category->name . ')',
            'details' => Str::limit(filter_var($request->description, FILTER_SANITIZE_STRING), 40 ),
        ];
        foreach($leads as $lead){
            $lead->notify(new TaskNotification($taskData));
        }
        $admins = User::where('is_employee', 2)->get();
        $adminTaskData = [
            'id' => auth()->user()->id,
            'name' => 'Task Assigned by ' . auth()->user()->name . ' ' . auth()->user()->last_name . ' (' . $sub_task->task->category->name . ')',
            'task_id' => $sub_task->task->id,
            'sub_task_id' => $sub_task->id,
            'text' => 'Task Assigned by ' . auth()->user()->name . ' ' . auth()->user()->last_name . ' (' . $sub_task->task->category->name . ')',
            'details' => Str::limit(filter_var($request->description, FILTER_SANITIZE_STRING), 40 ),
        ];
        foreach($admins as $admin){
            $admin->notify(new TaskNotification($adminTaskData));
        }

        //mail_notification
        $task = Task::find($request->input('task_id'));
        $project = Project::find($task->project_id);

//        $departments_leads_ids = array_unique(DB::table('category_users')->whereIn('category_id', $task->category_id)->pluck('user_id')->toArray());
        $departments_leads_ids = array_unique(DB::table('category_users')->whereIn('category_id', [$task->category_id])->pluck('user_id')->toArray());
        $departments_leads_emails = User::where('is_employee', 1)->whereIn('id', $departments_leads_ids)->pluck('email')->toArray();
        $departments_leads_names = User::where('is_employee', 1)->whereIn('id', $departments_leads_ids)->pluck('name')->toArray();
        $sales_head_emails = User::where('is_employee', 6)->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $project->brand_id)->pluck('user_id')->toArray()))->pluck('email')->toArray();
        $customer_support_user = User::find($project->user_id);
        $sales_head_emails []= $customer_support_user->email;

        $html = '<p>'. 'New task on project `'.$project->name.'`' .'</p><br />';
        $html .= '<strong>Assigned by:</strong> <span>'.auth()->user()->name.'</span><br />';
        $html .= '<strong>Assigned to:</strong> <span>'. implode(', ', $departments_leads_names) . '.' .'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
        $html .= '<strong>Task status:</strong> <span>'.get_task_status_text($task->status).'</span><br />';
        $html .= '<br /><strong>Description</strong> <span>' . $task->description;

//        mail_notification('', [$user->email], 'CRM | Project assignment', $html, true);
        mail_notification(
            '',
            array_merge($departments_leads_emails, $sales_head_emails),
            'New Task',
            view('mail.crm-mail-template')->with([
                'subject' => 'New Task',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        //pusher notification
        $pusher_notification_data = [
            'for_ids' => $departments_leads_ids,
            'text' => auth()->user()->name . ' ' . auth()->user()->last_name . ' has sent you a Message',
            'redirect_url' => route('production.task.show', ['id' => $data->task_id, 'name' => auth()->user()->name]),
        ];
        emit_pusher_notification('message-channel', 'new-message', $pusher_notification_data);

        return redirect()->back()->with('success','Task assigned Successfully.');
    }
}
