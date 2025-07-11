<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\ClientFile;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskStatusChangedLog;
use App\Models\User;
use App\Notifications\TaskNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 6, 4, 1])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brand_ids = v2_acl([2, 1]) ? Brand::all()->pluck('id')->toArray() : auth()->user()->brand_list();
        $brands =  Brand::whereIn('id', $brand_ids)->get();
        $category_ids = [];
        if (v2_acl([1])) {
            foreach(auth()->user()->category as $category){
                array_push($category_ids, $category->id);
            }
        }
        $categories = v2_acl([1]) ? (Category::whereIn('id', $category_ids)->get()) : Category::all();
        $restricted_brands = get_restricted_brands();
        $task_array = [];
        $notification_task = auth()->user()->unreadnotifications->where('type', 'App\Notifications\TaskNotification')->all();
        foreach($notification_task as $notification_tasks){
            array_push($task_array, $notification_tasks['data']['task_id']);
        }

        $tasks = Task::when(!v2_acl([1, 2]), function ($q) use ($brand_ids, $task_array) {
                return $q->whereIn('brand_id', $brand_ids)->whereNotIn('id', $task_array);
            })
            ->when(request()->get('project_id') != '', function ($q) {
                return $q->where('project_id', request()->get('project_id'));
            })->when(request()->get('project') != '', function ($q) {
                $project = request()->get('project');
                return $q->whereHas('projects', function($q) use($project){
                    $q->where('name', 'LIKE', "%$project%");
                });
            })->when(request()->get('id') != '', function ($q) {
                return $q->where('id', request()->get('id'));
            })->when(request()->get('brand_id') != '', function ($q) {
                return $q->where('brand_id', request()->get('brand_id'));
            })->when(request()->get('category_id') != '', function ($q) {
                return $q->where('category_id', request()->get('category_id'));
            })->when(request()->get('status') != '', function ($q) {
                return $q->where('status', request()->get('status'));
            })->when(request()->get('name') != '', function ($q) {
                $name = request()->get('name');
                return $q->whereHas('projects.client', function ($query) use ($name) {
                    return $query->where('name', 'LIKE', "%{$name}%")
                        ->orWhere('last_name', 'LIKE', "%{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%");
                });
            })->when(request()->get('project_id') != '', function ($q) {
                return $q->where('project_id', request()->get('project_id'));
            })->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand_id', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand_id', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            })->when(user_is_cs(), function ($q) {
                return $q->whereHas('projects', function ($query) {
                    return $query->where('user_id', '=', auth()->user()->id);
                });
            })
            ->when(v2_acl([1]), function ($q) use ($category_ids) {
                return $q->whereIn('category_id', $category_ids)
                    ->orderByRaw("FIELD(status, 0, 1, 4, 2, 6, 5, 7, 3)");
            })->orderBy('id', 'DESC')->paginate(20);

        $notify_data = Task::whereIn('brand_id', auth()->user()->brand_list())
            ->when(v2_acl([4]) && !auth()->user()->is_support_head, function ($q) {
                return $q->whereHas('projects', function ($query) {
                    return $query->where('user_id', '=', auth()->user()->id);
                });
            })
            ->whereIn('id', $task_array)->orderBy('id', 'desc')->get();

        return view('v2.task.index', compact('tasks', 'brands', 'categories', 'notify_data'));
    }

    public function create (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$project = Project::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non-admin checks
        if (!v2_acl([2])) {
            if (!in_array($project->brand_id, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (user_is_cs() && $project->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $categories = Category::all();

        return view('v2.task.create', compact('project', 'categories'));
    }

    public function store (Request $request)
    {
        if (!v2_acl([2, 6, 4])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'project_id' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'duedate' => 'required|after_or_equal:today',
        ]);

        $project = Project::where('status', 1)
//            ->whereIn('brand_id', auth()->user()->brand_list())
            ->where('id', $request->project_id)
            ->first();

        //non-admin checks
        if (!v2_acl([2])) {
            if (!in_array($project->brand_id, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (user_is_cs() && $project->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        foreach ($request->category_id as $category_id) {
            $task = Task::create([
                'brand_id' => $project->brand->id,
                'project_id' => $request->get('project_id'),
                'category_id' => $category_id,
                'user_id' => auth()->id(),
                'description' => $request->description
            ]);

            if($request->hasfile('filenames'))
            {
                $i = 0;
                foreach($request->file('filenames') as $file)
                {
                    $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $name = $file_name . '_' . $i . '_]' .time().'.'.$file->extension();
                    $file->move(public_path().'/files/', $name);
                    $i++;
                    $client_file = new ClientFile();
                    $client_file->name = $file_name;
                    $client_file->path = $name;
                    $client_file->user_id = auth()->id();
                    $client_file->user_check = auth()->user()->is_employee;
                    $client_file->production_check = 1;
                    $client_file->task_id = $task->id;
                    $client_file->save();
                }
            }
            $subtask = new SubTask();
            $subtask->task_id = $task->id;
            $subtask->description = $task->description;
            $subtask->user_id = $task->user_id;
            $subtask->user_id = $task->user_id;
            $subtask->duedate = $task->duedate;
            $subtask->save();

            send_task_notification($task->id, 1);

            //mail_notification
            $project = Project::find($request->project_id);
            $departments_leads_ids = array_unique(DB::table('category_users')->whereIn('category_id', $request->category_id)->pluck('user_id')->toArray());
            $departments_leads_emails = User::where('is_employee', 1)->whereIn('id', $departments_leads_ids)->pluck('email')->toArray();
            $departments_leads_names = User::where('is_employee', 1)->whereIn('id', $departments_leads_ids)->pluck('name')->toArray();
            $html = '<p>'. 'New task on project `'.$project->name.'`' .'</p><br />';
            $html .= '<strong>Assigned by:</strong> <span>'.auth()->user()->name.'</span><br />';
            $html .= '<strong>Assigned to:</strong> <span>'. implode(', ', $departments_leads_names) . '.' .'</span><br />';
            $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
            $html .= '<strong>Task status:</strong> <span>'.get_task_status_text($task->status).'</span><br />';
            $html .= '<br /><strong>Description</strong> <span>' . $task->description;

    //        mail_notification('', [$user->email], 'CRM | Project assignment', $html, true);
            mail_notification(
                '',
                $departments_leads_emails,
                'New Task',
                view('mail.crm-mail-template')->with([
                    'subject' => 'New Task',
                    'brand_name' => $project->brand->name,
                    'brand_logo' => asset($project->brand->logo),
                    'additional_html' => $html
                ]),
                true
            );

            //mail_notification
            $customer_support_user = User::find($project->user_id);
            foreach ($departments_leads_emails as $departments_leads_email) {
                $departments_lead = User::where('is_employee', 1)->where('email', $departments_leads_email)->first();
                if (!is_null($project) && $client = Client::find($project->client_id)) {
                    $brand = Brand::find($client->brand_id);

                    $html = '<p>'. 'Hello ' . $departments_lead->name .'</p>';
                    $html .= '<p>'. 'A new task has been assigned to you by '.$customer_support_user->name.'. Please review the task details and begin working on it as soon as possible.' .'</p>';
                    $html .= '<p><ul>'. '<li><strong>*Task: ('.$task->notes.' / '.$task->id.')</strong></li><li><strong>*Deadline: '.Carbon::parse($task->duedate)->format('d M Y, h:i A').'</strong></li>' .'</ul></p>';
                    $html .= '<p>'. 'Access the task here: <a href="'.route('support.task.show', $task->id).'">'.route('support.task.show', $task->id).'</a>' .'</p>';
                    $html .= '<p>'. 'Thank you for your dedication and hard work.' .'</p>';
                    $html .= '<p>'. 'Best Regards,' .'</p>';
                    $html .= '<p>'. $brand->name .'.</p>';

                    mail_notification(
                        '',
                        [$departments_leads_email],
                        'New Task Assignment: ('.$task->notes.' / '.$task->id.')',
                        view('mail.crm-mail-template')->with([
                            'subject' => 'New Task Assignment: ('.$task->notes.' / '.$task->id.')',
                            'brand_name' => $brand->name,
                            'brand_logo' => asset($brand->logo),
                            'additional_html' => $html
                        ]),
                    //            true
                    );
                }
            }

            //pusher notification
            $pusher_notification_data = [
                'for_ids' => $departments_leads_ids,
                'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
                'redirect_url' => route('v2.tasks.show', ['id' => $task->id, 'name' => auth()->user()->name]),
            ];

            emit_pusher_notification('v2-message-channel', 'v2-new-message', $pusher_notification_data);
        }


        return redirect()->route('v2.tasks.show', $task->id)->with('success','Task assigned Successfully.');
    }

    public function edit (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$client = Client::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.client.edit', compact('client'));
    }

    public function update (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$client = Client::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        $request->validate([
//            'name' => 'required',
//            'brand_id' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|unique:clients,email,'.$client->id,
////            'email' => 'required' . !is_null($client->user) ? ('|unique:users,email,'.$client->user->id) : '',
//            'status' => 'required',
//        ]);
//
//        $client->update($request->all());
//
//        return redirect()->route('v2.clients')->with('success','Client updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4, 1])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$task = Task::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non-admin checks
        $members = [];
        if (!v2_acl([2])) {
            if (!v2_acl([1]) && !in_array($task->brand_id, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (user_is_cs() && $task->projects->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([1])) {
                //check for ownership
                if (!in_array($task->category_id, auth()->user()->category()->pluck('id')->toArray())) {
                    return redirect()->back()->with('error', 'Not allowed.');
                }

                $members = User::where('is_employee', 5)->where('status','1')->whereHas('category', function ($query) use ($task){
                    return $query->when($task, function ($q) use ($task) {
                        return $q->where('category_id', '=', $task->category_id);
                    });
                })->get();
            }
        }

        return view('v2.task.show', compact('task', 'members'));
    }

    public function updateStatus (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4, 1])) {
            return response()->json(['status' => false, 'message' => 'Access denied.']);
        }

        $value = $request->value;

        $allowed_status_map = [
//            2 => [0, 1, 2, 3, 4, 5, 6, 7],
            2 => [1, 2, 3, 4, 5, 6],
            6 => [1],
            4 => [1],
            1 => [2, 3, 4, 5, 6, 7],
        ];

        if (!in_array($value, $allowed_status_map[auth()->user()->is_employee])) {
            return response()->json(['status' => false, 'message' => 'Not allowed.']);
        }

        $task = Task::find($id);
        $user = $task->user;

        //non-admin checks
        if (!v2_acl([2])) {
            if (!v2_acl([1]) && !in_array($task->brand_id, auth()->user()->brand_list())) {
                return response()->json(['status' => false, 'message' => 'Not allowed.']);
            }

            if (user_is_cs() && $task->projects->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([1])) {
                //check for ownership
                if (!in_array($task->category_id, auth()->user()->category()->pluck('id')->toArray())) {
                    return redirect()->back()->with('error', 'Not allowed.');
                }

                if ($value == 3) { notify_qa_of_outgoing_task($task->id); }

                if ($value == 7) {
                    notify_qa_of_incoming_task($task->id);

                    emit_pusher_notification('qa-channel', 'incoming-task', [
                        'task' => $task,
                        'redirect_url' => route('qa.task.show', $task->id)
                    ]);
                }
            }
        }

        $status = get_task_status_text($value);

        //if task status changed create logs
        if ($task->status != $request->value) {
            TaskStatusChangedLog::create([
                'task_id' => $task->id,
                'user_id' => auth()->id() ?? null,
                'column' => 'status',
                'old' => $task->status,
                'new' => $request->value,
            ]);
        }

        $task->status = $value;
        $task->save();

        $description = $task->projects->name . " Marked as " . $status;
        $assignData = [
            'id' => auth()->user()->id,
            'task_id' => $task->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $description,
            'details' => '',
        ];
        //notify task agent
        $user->notify(new TaskNotification($assignData));
        //notify buh
        if ($task->brand_id) {
            foreach (
                User::whereIn('id', get_buh_ids_by_brand_id($task->brand_id))->get() as $buh
            ) { $buh->notify(new TaskNotification($assignData)); }
        }

        //mail_notification
        $project = Project::find($task->project_id);
        $assigned_agent_emails = [$user->email];
        $html = '<p>'. (auth()->user()->name) .' has updated task on project `'.$project->name.'`' .'</p><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
        $html .= '<strong>Task status:</strong> <span>'.get_task_status_text($task->status).'</span><br />';
        $html .= '<br /><strong>Description</strong> <span>' . $task->description;

        mail_notification(
            '',
            $assigned_agent_emails,
            'Task updated',
            view('mail.crm-mail-template')->with([
                'subject' => 'Task updated',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return response()->json(['status' => true, 'message' => 'Status Updated Successfully']);
    }

    public function uploadFiles (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4, 1])) {
            return response()->json(['status' => false, 'message' => 'Access denied.']);
        }

        $task = Task::find($id);

        //non-admin checks
        if (!v2_acl([2])) {
            if (!v2_acl([1]) && !in_array($task->brand_id, auth()->user()->brand_list())) {
                return response()->json(['status' => false, 'message' => 'Not allowed.']);
            }

            if (user_is_cs() && $task->projects->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([1])) {
                //check for ownership
                if (!in_array($task->category_id, auth()->user()->category()->pluck('id')->toArray())) {
                    return redirect()->back()->with('error', 'Not allowed.');
                }
            }
        }

        if($request->hasfile('images'))
        {
            $files_array = array();
            $i = 0;
            foreach($request->file('images') as $file)
            {
                $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $name = $file_name . '_' . $i . '_]' .time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/files/', $name);
                $i++;
                $client_file = new ClientFile();
                $client_file->name = $file_name;
                $client_file->path = $name;
                $client_file->task_id = $id;
                $client_file->user_id = auth()->id();
                $client_file->user_check = auth()->user()->is_employee;
                $client_file->production_check = 1;
                $client_file->save();
                array_push($files_array, $client_file->id);
            }
        }

        $data = $request->hasfile('images') ? ClientFile::whereIn('id', $files_array)->get() : [];
        $role = '';
        $uploaded_by = auth()->user()->name . ' ' . auth()->user()->last_name;


        //mail_notification
        $project = Project::find($task->project_id);
        $departments_leads_ids = array_unique(DB::table('category_users')->where('category_id', $task->category_id)->pluck('user_id')->toArray());
        $departments_leads_emails = User::where('is_employee', 1)->whereIn('id', $departments_leads_ids)->pluck('email')->toArray();
        $html = '<p>'. auth()->user()->name.' has uploaded files on task on project `'.$project->name.'`' .'</p><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
        $html .= '<strong>Task status:</strong> <span>'.get_task_status_text($task->status).'</span><br />';
        $html .= '<br /><strong>Description</strong> <span>' . $task->description;

//        mail_notification('', [$user->email], 'CRM | Project assignment', $html, true);
        mail_notification(
            '',
            $departments_leads_emails,
            'New Task',
            view('mail.crm-mail-template')->with([
                'subject' => 'New Task',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return response()->json(['uploaded' => 'success', 'files' => $data, 'uploaded_by' => $uploaded_by]);
    }
}
