<?php

namespace App\Http\Livewire;

use App\Models\SubTask;
use App\Models\Task;
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

    public $auth_category_ids = [];

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
    public $dashboard_persistent_pagination = false;

    public $project_detail_search_message_query = '';
    public $project_detail_message_count = 5;

    protected $listeners = [
        'back' => 'back',
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

        foreach(auth()->user()->category as $category){
            array_push($this->auth_category_ids, $category->id);
        }

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
        $project_detail_visit_check = str_contains(end($this->history), 'project_detail');
        //persistent pagination
        $this->dashboard_persistent_pagination = (bool) $project_detail_visit_check;
        //see older messages
        $this->project_detail_message_count = $project_detail_visit_check ? 5 : $this->project_detail_message_count;

        //prevent back with one page in history
        if ($this->history === ['production_dashboard']) {
            $this->active_page = 'production_dashboard';
            $this->resetPage();
            return $this->render();
        }

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
            return $this->project_detail(intval(str_replace('project_detail-', '', $this->active_page)));
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
            ->when($this->dashboard_category_id !== 'All' && in_array(intval($this->dashboard_category_id), $this->auth_category_ids), function ($q) {
                return $q->where('category_id', $this->dashboard_category_id);
            })
            ->when($this->dashboard_search !== '', function ($q) {
                return $q->where(function ($q) {
                    return $q->whereHas('projects', function ($q) {
                        return $q->where('name', 'LIKE', '%'.$this->dashboard_search.'%')
                            ->orWhere('description', 'LIKE', '%'.$this->dashboard_search.'%')
                            ->orWhereHas('added_by', function ($q) {
                                return get_user_search($q, $this->dashboard_search);
                            });
                    })->orWhereHas('user', function ($q) {
                        return get_user_search($q, $this->dashboard_search);
                    })->orWhereHas('sub_tasks_default_order', function ($q) {
                        return $q->where('description', 'LIKE', "%".$this->dashboard_search."%")
                        ->orWhereHas('user', function ($q) {
                            return get_user_search($q, $this->dashboard_search);
                        });
                    })->orWhere('id', '=', $this->dashboard_search)->orWhere('description', 'LIKE', "%".$this->dashboard_search."%");
                });
            })
            ->whereIn('category_id', $this->auth_category_ids)
            ->whereIn('status', $status_in_array)
            ->addSelect(['latest_subtask_created_at' => SubTask::whereHas('user', function ($q) {
                return $q->whereIn('is_employee', [0, 2, 4, 6]);
            })->selectRaw('MAX(created_at)')->whereColumn('task_id', 'tasks.id')])
            ->orderByDesc('latest_subtask_created_at')
    //        ->orderBy('status', 'ASC')
            ->paginate(8);

        //persistent pagination
        if ($this->dashboard_persistent_pagination) {
            $this->setPage($this->dashboard_current_page, 'page');
            $this->dashboard_persistent_pagination = false;
        }
        $this->dashboard_current_page = $current_projects->currentPage();

        //focus search input
        if ($this->dashboard_search !== '') {
            $this->emit('focus_input', '#input_dashboard_search');
        }

        $this->emit('set_refresh_time', 300000);
        return view('livewire.production.dashboard', compact('current_projects', 'notification_project_ids'))->extends($this->layout);
    }

    public function project_detail ($project_id) {
        $project = Task::find($project_id);

        //check for ownership
        if (!in_array($project->category_id, $this->auth_category_ids)) {
            $this->emit('error', 'Access denied.');
            $this->back();
        }

        //subtask - messages
        $sub_task_messages = SubTask::with('assign_members.assigned_to_user')
            ->whereHas('user')
            ->where([
                'task_id' => $project->id,
                'sub_task_id' => 0,
            ])
            ->when($this->project_detail_search_message_query != '', function ($q) {
                return $q->where('description', 'LIKE', '%'.$this->project_detail_search_message_query.'%');
            });

        //see older messages
        $all_messages_fetched = (bool)($sub_task_messages->count() <= $this->project_detail_message_count);

        $sub_task_messages = $sub_task_messages
            ->orderBy('id', 'DESC') // Get latest messages
            ->take($this->project_detail_message_count) // Limit to last $n messages
            ->get()
            ->sortBy('id') // Sort back in ascending order
            ->values(); // Reset array indexes

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

        $this->emit('set_refresh_time', 1800000);
        return view('livewire.production.project-detail', compact('project', 'sub_task_messages', 'notification_subtask_ids', 'notification_notification_ids', 'all_messages_fetched'))->extends($this->layout);
    }

    public function set_project_status ($project_id, $status) {
        $task = DB::table('tasks')->where('id', $project_id)->first();
        if ($task->status == $status) {
            return $this->render();
        }

        $user = User::find($task->user_id);
        $project = DB::table('projects')->where('id', $task->project_id)->first();

        //if task status changed create logs
        if ($task && $task->status != $status) {
            DB::table('task_status_changed_logs')->insert([
                'task_id' => $task->id,
                'user_id' => auth()->id() ?? null,
                'created_at' => Carbon::now(),
                'column' => 'status',
                'old' => $task->status,
                'new' => $status,
            ]);
        }

        DB::table('tasks')->where('id', $project_id)->update(['status' => $status]);
        $assignData = [
            'id' => auth()->user()->id,
            'task_id' => $task->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $project->name . " Marked as " . $status,
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
        $assigned_agent_emails = [$user->email];
        $html = '<p>'. (auth()->user()->name) .' has updated task on project `'.$project->name.'`' .'</p><br />';
        $html .= '<strong>Task status:</strong> <span>'.get_task_status_text($task->status).'</span><br />';
        $html .= '<br /><strong>Description</strong> <span>' . $task->description;

        mail_notification(
            '',
            $assigned_agent_emails,
            'Task updated',
            view('mail.crm-mail-template')->with([
                'subject' => 'Task updated',
                'additional_html' => $html
            ]),
            true
        );

        $this->emit('success', 'Status updated.');
        return $this->render();
    }

    public function assign_subtask ($data) {
        $subtask = DB::table('sub_task')->where('id', $data['subtask_id'])->first();
        $task = DB::table('tasks')->where('id', $subtask->task_id)->first();
        $project = DB::table('projects')->where('id', $task->project_id)->first();

        $record_id = DB::table('production_member_assigns')->insertGetId([
            'task_id' => $subtask->task_id,
            'subtask_id' => $subtask->id,
            'assigned_by' => auth()->user()->id,
            'assigned_to' => $data['member_id'],
            'comments' => $data['comment'] ?? '',
            'duadate' => Carbon::today(),
            'created_at' => Carbon::now(),
            'status' => 0
        ]);

        $assignData = [
            'id' => $record_id,
            'task_id' => $task->id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => $project->name . '- Task Assigned',
            'details' => \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($subtask->description)), 50, $end='...')
        ];

        $user = User::find($data['member_id']);
        $user->notify(new TaskNotification($assignData));

        //mail_notification
        $html = '<p>'. 'New task on project `'.$project->name.'`: ' . $data['comment'] .'</p><br />';
        $html .= '<strong>Assigned by:</strong> <span>'.auth()->user()->name.'</span><br />';
        $html .= '<strong>Assigned to:</strong> <span>'. $user->name.' ('.$user->email.') ' .'</span><br />';
        $html .= '<br /><strong>Description</strong> <span>' . $task->description;

        mail_notification(
            '',
            [$user->email],
            'New Task',
            view('mail.crm-mail-template')->with([
                'subject' => 'New Task',
                'additional_html' => $html
            ]),
            true
        );

        $this->emit('success', 'Subtask assigned.');
        return $this->render();
    }

    public function send_message ($data) {
        if (!$task = DB::table('tasks')->where('id', $data['task_id'])->first()) {
            return $this->render();
        }
        $task_user = User::find($task->user_id);

        $description = str_replace("\n", '&nbsp;<br>', $data['message']);

        DB::table('sub_task')->insert([
            'task_id' => $data['task_id'],
            'created_at' => Carbon::now(),
            'description' => $description,
            'user_id' => auth()->id(),
        ]);
        $assignData = [
            'id' => auth()->id(),
            'task_id' => $data['task_id'],
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($description)), 25, $end='...'),
            'details' => '',
        ];
        $task_user->notify(new TaskNotification($assignData));

        $this->emit('success', 'Message sent.');
        return $this->render();
    }

    public function clear_subtask_notification ($data) {
        DB::table('notifications')->where('id', $data['notification_id'])->update([
            'read_at' => Carbon::now()
        ]);

        $this->emit('success', 'Notification cleared.');
        return $this->render();
    }

    public function load_more_messages()
    {
        $this->project_detail_message_count += 5;
        $this->emit('scroll_to_top', '#chat_bubbles_wrapper');
    }

    public function test ($data) {
        dd($data);
    }
}
