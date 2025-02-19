<?php

namespace App\Http\Livewire;

use App\Models\CRMNotification;
use App\Models\ProductionMemberAssign;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskStatusChangedLog;
use App\Models\User;
use App\Notifications\TaskNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductionDashboard extends Component
{
    use WithPagination, WithFileUploads;

    public $layout;
    protected $paginationTheme = 'bootstrap';
    public $active_page = 'production_dashboard';
    public $history = ['production_dashboard'];

    public $dashboard_project_status = [
        0 => 1,
        1 => 1,
        2 => 1,
        3 => 0,
        4 => 1,
        5 => 1,
        6 => 1,
    ];
    public $dashboard_category_id = 'All';
    public $dashboard_search = '';
    public $dashboard_current_page = null;

    public $project_detail_search_message_query = '';

    protected $listeners = [
        'mutate' => 'mutate',
        'refresh' => 'refresh',
        'set_select2_field_value' => 'set_select2_field_value',
        'assign_subtask' => 'assign_subtask',
        'send_message' => 'send_message',
        'clear_subtask_notification' => 'clear_subtask_notification',
        'test' => 'test',
    ];

    public function construct()
    {
        if (!auth()->check() || !in_array(auth()->user()->is_employee, [1]) || auth()->id() != 3117) {
//        if (!auth()->check() || !in_array(auth()->user()->is_employee, [1])) {
            return false;
        }

//        $layout_map = [
//            1 => 'layouts.app-production',
//        ];
//
//        $this->layout = $layout_map[auth()->user()->is_employee];
        $this->layout = 'layouts.app-production';

        return true;
    }

    public function mount()
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        if (session()->has('livewire_history')) {
            $this->history = session()->get('livewire_history');
            $this->active_page = end($this->history);
        }
    }

    public function set_active_page ($page)
    {
        $this->active_page = $page;
        $this->history[] = $this->active_page;

        //put history in session
        session()->put('livewire_history', $this->history);

        $this->resetPage(); // Reset pagination

        $this->emit('emit_pre_render');
        $this->render(); // Trigger rendering
    }

    public function back () {
        array_pop($this->history);
        $this->active_page = end($this->history);

        //put history in session
        session()->put('livewire_history', $this->history);

        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        if ($this->active_page == 'production_dashboard') {
            return $this->production_dashboard();
        } else if (str_contains($this->active_page, 'project_detail')) {
            $slug = explode('-', str_replace('project_detail-', '', $this->active_page));

            //persistent pagination
            $this->dashboard_current_page = (intval($slug[1]) != 1) ? intval($slug[1]) : null;

            $project_id = intval($slug[0]);
            return $this->project_detail($project_id);
        } else {
            return redirect()->route('login');
        }
    }

    public function mutate ($data)
    {
        $property = $data['name'];
        $this->$property = $data['value'];
    }

    public function refresh () {
        $this->render();
    }

    public function production_dashboard () {
        $user_category_ids = array();
        foreach(auth()->user()->category as $category){
            array_push($user_category_ids, $category->id);
        }

        $status_in_array = [];
        foreach ($this->dashboard_project_status as $status => $state) {
            if ($state == 1) {
                $status_in_array []= $status;
            }
        }

        $notification_project_ids = [];
        foreach (DB::table('notifications')->whereNull('read_at')->where([
            'type' => 'App\Notifications\TaskNotification',
            'notifiable_id' => auth()->id(),
        ])->get() as $item) {
            $data = json_decode($item->data);
            if (isset($data->task_id) && $data->task_id && $data->task_id != null && $data->task_id != '') {
                $notification_project_ids []= $data->task_id;
            }
        }

        $current_projects = Task::with('sub_tasks')
            ->when($this->dashboard_category_id !== 'All', function ($q) {
                return $q->where('category_id', $this->dashboard_category_id);
            })
            ->when($this->dashboard_search !== '', function ($q) {
                return $q->where(function ($q) {
                    return $q->whereHas('projects', function ($q) {
                        return $q->where('name', 'LIKE', '%'.$this->dashboard_search.'%')
                            ->orWhere('description', 'LIKE', '%'.$this->dashboard_search.'%')
                            ->orWhereHas('added_by', function ($q) {
                                return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$this->dashboard_search.'%')
                                    ->orWhere('name', 'LIKE', "%".$this->dashboard_search."%")
                                    ->orWhere('last_name', 'LIKE', "%".$this->dashboard_search."%")
                                    ->orWhere('email', 'LIKE', "%".$this->dashboard_search."%")
                                    ->orWhere('contact', 'LIKE', "%".$this->dashboard_search."%");
                            });

                    })->orWhereHas('user', function ($q) {
                        return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$this->dashboard_search.'%')
                            ->orWhere('name', 'LIKE', "%".$this->dashboard_search."%")
                            ->orWhere('last_name', 'LIKE', "%".$this->dashboard_search."%")
                            ->orWhere('email', 'LIKE', "%".$this->dashboard_search."%")
                            ->orWhere('contact', 'LIKE', "%".$this->dashboard_search."%");
                    })->orWhereHas('sub_tasks_default_order', function ($q) {
                        return $q->whereHas('user', function ($q) {
                            return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$this->dashboard_search.'%')
                                ->orWhere('name', 'LIKE', "%".$this->dashboard_search."%")
                                ->orWhere('last_name', 'LIKE', "%".$this->dashboard_search."%")
                                ->orWhere('email', 'LIKE', "%".$this->dashboard_search."%")
                                ->orWhere('contact', 'LIKE', "%".$this->dashboard_search."%");
                        });
                    })->orWhere('id', '=', $this->dashboard_search)->orWhere('description', 'LIKE', "%".$this->dashboard_search."%");
                });
            })
            ->whereIn('category_id', $user_category_ids)
            ->whereIn('status', $status_in_array)
            ->addSelect(['latest_subtask_created_at' => SubTask::selectRaw('MAX(created_at)')
                ->whereColumn('task_id', 'tasks.id')
            ])
            ->orderByDesc('latest_subtask_created_at')
    //        ->orderBy('status', 'ASC')
            ->paginate(8);

        //persistent pagination
        $this->setPage((!is_null($this->dashboard_current_page) ? $this->dashboard_current_page : 1), 'page');
        $this->dashboard_current_page = !is_null($this->dashboard_current_page) ? null : 1;

        //focus search input
        if ($this->dashboard_search !== '') {
            $this->emit('focus_input', '#input_dashboard_search');
        }

        return view('livewire.production.dashboard', compact('current_projects', 'notification_project_ids'))->extends($this->layout);
    }

    public function project_detail ($project_id) {
        $project = Task::find($project_id);

        //subtask - messages
        $sub_task_messages = SubTask::with('assign_members.assigned_to_user')->whereHas('user')
            ->where([
                'task_id' => $project->id,
                'sub_task_id' => 0,
            ])->when($this->project_detail_search_message_query != '', function ($q) {
                return $q->where('description', 'LIKE', '%'.$this->project_detail_search_message_query.'%');
            })
            ->orderBy('id', 'ASC')->get();

        $notification_subtask_ids = [];
        $notification_notification_ids = [];
        foreach (DB::table('notifications')->whereNull('read_at')->where([
            'type' => 'App\Notifications\TaskNotification',
            'notifiable_id' => auth()->id(),
        ])->get() as $item) {
            $data = json_decode($item->data);
            if (isset($data->sub_task_id) && $data->sub_task_id && $data->sub_task_id != null && $data->sub_task_id != '') {
                $notification_subtask_ids []= $data->sub_task_id;
                $notification_notification_ids[$data->sub_task_id] = $item->id;
            }
        }

        $this->emit('scroll_to_bottom', 'chat_bubbles_wrapper');
        $this->emit('init_file_uploader', ['selector' => '#input_upload_files', 'task_id' => $project_id, 'modal_selector' => '#upload_files_modal']);

        return view('livewire.production.project-detail', compact('project', 'sub_task_messages', 'notification_subtask_ids', 'notification_notification_ids'))->extends($this->layout);
    }

    public function set_project_status ($project_id, $status) {
        $task = Task::find($project_id);
        if ($task->status == $status) {
            return $this->render();
        }

        $user = $task->user;

        //if task status changed create logs
        if ($task && $task->status != $status) {
            TaskStatusChangedLog::create([
                'task_id' => $task->id,
                'user_id' => auth()->id() ?? null,
                'column' => 'status',
                'old' => $task->status,
                'new' => $status,
            ]);
        }

        $task->status = $status;
        $task->save();
        $assignData = [
            'id' => auth()->user()->id,
            'task_id' => $task->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $task->projects->name . " Marked as " . $status,
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

        $this->emit('success', 'Status updated.');
        return $this->render();
    }

    public function assign_subtask ($data) {
//        dd($data);
        $subtask = SubTask::Find($data['subtask_id']);

        $record = ProductionMemberAssign::create([
            'task_id' => $subtask->task->id,
            'subtask_id' => $subtask->id,
            'assigned_by' => auth()->user()->id,
            'assigned_to' => $data['member_id'],
            'comments' => $data['comment'] ?? '',
            'duadate' => Carbon::today(),
            'status' => 0
        ]);

        $assignData = [
            'id' => $record->id,
            'task_id' => $subtask->task->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $subtask->task->projects->name . '- Task Assigned',
            'details' => \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($subtask->description)), 50, $end='...')
        ];

        $user = User::find($data['member_id']);
        $user->notify(new TaskNotification($assignData));

        //mail_notification
        $project = Project::find($subtask->task->project_id);
        $assigned_to_user = $user;
        $html = '<p>'. 'New task on project `'.$project->name.'`: ' . $data['comment'] .'</p><br />';
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

        $this->emit('success', 'Subtask assigned.');
        return $this->render();
    }

    public function send_message ($data) {
        $task = Task::find($data['task_id']);
        $sub_task = SubTask::create([
            'task_id' => $data['task_id'],
            'created_at' => Carbon::now(),
            'description' => $data['message'],
            'user_id' => auth()->id(),
        ]);
        $assignData = [
            'id' => auth()->id(),
            'task_id' => $data['task_id'],
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($data['message'])), 25, $end='...'),
            'details' => '',
        ];
        $task->user->notify(new TaskNotification($assignData));

        $this->emit('success', 'Message sent.');
        return $this->render();
    }

    public function clear_subtask_notification ($data) {
        if($record = DB::table('notifications')->where('id', $data['notification_id'])->first()) {
            if($record = CRMNotification::where('id', $data['notification_id'])->first()) {
                $record->read_at = Carbon::now();
                $record->save();
            }
        }

        $this->emit('success', 'Notification cleared.');
        return $this->render();
    }

    public function test ($data) {
        dd($data);
    }
}
