<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductionMemberAssign;
use App\Models\ProductionMessage;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\User;
use App\Notifications\SubTaskNotification;
use App\Notifications\TaskNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubtaskController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([1, 5])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $auth_categories = auth()->user()->category_list();
        $categories = Category::whereIn('id', $auth_categories)->get();
        $subtasks = ProductionMemberAssign::whereHas('task', function ($q) use ($auth_categories) {
            return $q->whereIn('category_id', $auth_categories);
        })->when(
            $request->has('category_id') &&
            $request->get('category_id') != '' &&
            in_array($request->get('category_id'), $auth_categories)
            , function ($q) {
            return $q->whereHas('task', function ($q) {
                return $q->where('category_id', request()->get('category_id'));
            });
        })->when($request->get('status') != '', function ($q) {
            return $q->where('status', request()->get('status'));
        })->when(v2_acl([5]), function ($q) {
            return $q->where('assigned_to', auth()->id());
        })->orderByRaw("FIELD(status, 0, 1, 4, 2, 6, 5, 7, 3)")->orderBy('created_at', 'DESC')->paginate(20);

        return view('v2.subtask.index', compact('subtasks', 'categories'));
    }

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

    public function show (Request $request, $id)
    {
        if (!v2_acl([1, 5])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$subtask = ProductionMemberAssign::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        if (v2_acl([1]) && !in_array($subtask->task->category_id, auth()->user()->category_list())) {
            return redirect()->back()->with('error', 'Not allowed.');
        }

        if (v2_acl([5]) && $subtask->assigned_to != auth()->id()) {
            return redirect()->back()->with('error', 'Not allowed.');
        }

        return view('v2.subtask.show', compact('subtask'));
    }

    public function updateStatus (Request $request, $id)
    {
        if (!v2_acl([5])) {
            return response()->json(['status' => false, 'message' => 'Access denied.']);
        }

        if (!$subtask = ProductionMemberAssign::find($id)) {
            return response()->json(['status' => false, 'message' => 'Not found.']);
        }

        if ($subtask->assigned_to != auth()->id()) {
            return response()->json(['status' => false, 'message' => 'Not allowed.']);
        }

        $value = $request->value;

        $allowed_status_map = [
            5 => [2, 3, 4, 5, 6],
        ];

        if (!in_array($value, $allowed_status_map[auth()->user()->is_employee])) {
            return response()->json(['status' => false, 'message' => 'Not allowed.']);
        }

        $task = ProductionMemberAssign::find($id);
        $task->status = $value;
        $task->save();

        $status = get_task_status_text($value);
        $description = $task->task->projects->name . " Marked as " . $status;
        $assignData = [
            'id' => auth()->user()->id,
            'task_id' => $task->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $description,
            'details' => 'Updated by '. auth()->user()->name . ' ' . auth()->user()->last_name,
        ];
        $user = User::find($task->assigned_by);
        $user->notify(new SubTaskNotification($assignData));

        return response()->json(['status' => true, 'message' => 'Status Updated Successfully']);
    }

    public function assign (Request $request)
    {
        if (!v2_acl([1])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'sub_task' => 'required',
        ]);

        $subtask = SubTask::Find($request->sub_task);

        if (!in_array($subtask->task->category_id, auth()->user()->category_list())) {
            return redirect()->back()->with('error', 'Not allowed.');
        }
//        dd($request->all());
        $assignMember = new ProductionMemberAssign();
        $assignMember->task_id = $subtask->task->id;
        $assignMember->subtask_id = $subtask->id;
        $assignMember->assigned_by = auth()->user()->id;
        $assignMember->assigned_to = $request->assign_sub_task_user_id;
        $assignMember->comments = $request->comment;
        $assignMember->duadate = $request->duadate;
        $assignMember->status = 0;
        $assignMember->save();
        $assignData = [
            'id' => $assignMember->id,
            'task_id' => $subtask->task->id,
            'name' => Auth()->user()->name . ' ' . Auth()->user()->last_name,
            'text' => $subtask->task->projects->name . '- Task Assigned',
            'details' => \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($subtask->description)), 50, $end='...')
        ];
        $user = User::find($request->assign_sub_task_user_id);
        $user->notify(new TaskNotification($assignData));

        //mail_notification
        $project = Project::find($subtask->task->project_id);
        $assigned_to_user = User::find($request->assign_sub_task_user_id);
        $html = '<p>'. 'New task on project `'.$project->name.'`: ' . $request->comment .'</p><br />';
        $html .= '<strong>Assigned by:</strong> <span>'.auth()->user()->name.'</span><br />';
        $html .= '<strong>Assigned to:</strong> <span>'. $assigned_to_user->name.' ('.$assigned_to_user->email.') ' .'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
        $html .= '<br /><strong>Description</strong> <span>' . $subtask->task->description;

        mail_notification(
            '',
            [$assigned_to_user->email],
            'New Task',
            view('mail.crm-mail-template')->with([
                'subject' => 'New Task',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return redirect()->back()->with('success', 'Sub Task Assigned');
    }

    public function updateComments (Request $request, $id)
    {
        if (!v2_acl([1])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        if (!$subtask = ProductionMemberAssign::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        if (!in_array($subtask->task->category_id, auth()->user()->category_list())) {
            return redirect()->back()->with('error', 'Not allowed.');
        }

        $subtask->comments = $request->get('comments');
        $subtask->save();

        return redirect()->back()->with('success', 'Subtask comment updated Successfully.');
    }

    public function addMessage (Request $request, $id)
    {
        if (!v2_acl([1, 5])) {
            return redirect()->back()->with('error', 'Access denied.');
        }
        $request->validate([
            'description' => 'required',
        ]);

        if (!$subtask = ProductionMemberAssign::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        if (!in_array($subtask->task->category_id, auth()->user()->category_list())) {
            return redirect()->back()->with('error', 'Not allowed.');
        }

        $message = new ProductionMessage();
        $message->production_member_assigns_id = $subtask->id;
        $message->messages = $request->description;
        $message->user_id = auth()->user()->id;
        $message->save();
        $description = $subtask->task->projects->name . " - Sub Task Updated";
        $assignData = [
            'id' => auth()->user()->id,
            'task_id' => $subtask->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $description,
            'details' => 'Updated by '. auth()->user()->name . ' ' . auth()->user()->last_name,
        ];
        $user = User::find($subtask->assigned_to);
        $user->notify(new SubTaskNotification($assignData));

        return redirect()->back()->with('success', 'Subtask comment updated Successfully.');
    }
}
