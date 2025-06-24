<!-- Lead Notification dropdown -->
@if (Auth::user()->is_employee == 2)
    <div class="badge-top-container">
        <span
            class="badge badge-primary">{{ auth()->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->count() }}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        @php
            $k = 0;
        @endphp
        @foreach (auth()->user()->unreadnotifications()->latest()->take(10)->get() as $notifications)
            @if ($notifications->type == 'App\Notifications\LeadNotification')
                <a href="{{ route('admin.client.shownotification', ['client' => $notifications->data['id'], 'id' => $notifications->id]) }}"
                    class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
            @elseif ($notifications->type == 'App\CustomInvoiceNotification' && isset($notifications->data['invoice_id']))
                <a href="{{ route('v2.invoices.show', ['invoice_id' => $notifications->data['invoice_id']]) }}"
                    class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <a href="" class="unread_notification_nav dropdown-item d-flex"
                        data-id="{{ $notifications->id }}">
                    @else
                        <a href="" class="unread_notification_nav dropdown-item d-flex"
                            data-id="{{ $notifications->id }}">
            @endif
            <div class="notification-icon">
                @if ($notifications->type == 'App\Notifications\LeadNotification')
                    <i class="i-Checked-User text-primary mr-1"></i>
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <i class="i-Money-Bag text-success mr-1"></i>
                @elseif($notifications->type == 'App\CustomInvoiceNotification')
                    <i class="fas fa-dollar-sign text-success mr-1"></i>
                @else
                    <i class="fa-solid fa-bell"></i>
                @endif
            </div>
            <div class="notification-details flex-grow-1">
                <p class="m-0 d-flex align-items-center">
                    <span class="lead-heading">{{ $notifications->data['text'] }}</span>
                    <span class="flex-grow-1"></span>
                    <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                </p>
                <p class="text-small text-muted m-0">
                    {{ $notifications->data['name'] }}</p>
            </div>
            </a>
            @if ($loop->last)
            @endif
            @php
                $k++;
            @endphp
        @endforeach

        @if (v2_acl([0, 1, 4, 5, 6]))
            <a href="{{ route('v2.notifications') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">View all</span>
                    </p>
                </div>
            </a>

            <a href="{{ route('notification.all.read') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-check-double text-success"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">Mark all as read</span>
                    </p>
                </div>
            </a>
        @endif
    </div>
@elseif(Auth::user()->is_employee == 6)
    @php
        $k = 0;
        $notification = auth()
            ->user()
            ->unreadnotifications()
            //for qa manager accounts
            ->when(in_array(auth()->id(), [3839, 3838, 3837]), function ($q) {
                return $q->where('type', 'App\Notifications\MessageNotification');
            })
            ->where('type', '!=', 'App\Notifications\MessageNotification')
            ->latest()
            ->take(10)
            ->get();
    @endphp
    <div class="badge-top-container">
        <span class="badge badge-primary">{{ count($notification) }}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        @foreach (auth()->user()->unreadnotifications()->latest()->take(10)->get() as $notifications)
            @if ($notifications->type == 'App\Notifications\LeadNotification')
                <a href="{{ route('admin.client.shownotification', ['client' => $notifications->data['id'], 'id' => $notifications->id]) }}"
                    class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif ($notifications->type == 'App\CustomInvoiceNotification' && isset($notifications->data['invoice_id']))
                    <a href="{{ route('v2.invoices.show', ['invoice_id' => $notifications->data['invoice_id']]) }}"
                       class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <a href="" class="unread_notification_nav dropdown-item d-flex"
                        data-id="{{ $notifications->id }}">
                    @elseif($notifications->type == 'App\Notifications\MessageNotification')
                        <a href="{{ route('manager.message.show', ['id' => $notifications->data['id'], 'name' => $notifications->data['name']]) }}"
                            class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                        @else
                            <a href="" class="unread_notification_nav dropdown-item d-flex"
                                data-id="{{ $notifications->id }}">
            @endif
            <div class="notification-icon">
                @if ($notifications->type == 'App\Notifications\LeadNotification')
                    <i class="i-Checked-User text-primary mr-1"></i>
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <i class="i-Money-Bag text-success mr-1"></i>
                @elseif($notifications->type == 'App\CustomInvoiceNotification')
                    <i class="fas fa-dollar-sign text-success mr-1"></i>
                @else
                    <i class="fa-solid fa-bell"></i>
                @endif
            </div>
            <div class="notification-details flex-grow-1">
                <p class="m-0 d-flex align-items-center">
                    <span class="lead-heading">{{ $notifications->data['text'] }}</span>
                    <span class="flex-grow-1"></span>
                    <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                </p>
                <p class="text-small text-muted m-0">
                    {{ $notifications->data['name'] }}</p>
            </div>
            </a>
            @if ($loop->last)
            @endif
            @php
                $k++;
            @endphp
        @endforeach

        @if (v2_acl([0, 1, 4, 5, 6]))
            <a href="{{ route('v2.notifications') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">View all</span>
                    </p>
                </div>
            </a>

            <a href="{{ route('notification.all.read') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-check-double text-success"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">Mark all as read</span>
                    </p>
                </div>
            </a>
        @endif
    </div>
@elseif(Auth::user()->is_employee == 4)
    <div class="badge-top-container">
        <span
            class="badge badge-primary">{{ auth()->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->count() }}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        @php
            $k = 0;
        @endphp
        @foreach (auth()->user()->unreadnotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->latest()->take(10)->get() as $notifications)
            @if ($notifications->type == 'App\Notifications\AssignProjectNotification')
                <a href="{{ route('create.task.by.project.id', ['id' => $notifications->data['project_id'], 'name' => $notifications->data['text'], 'notify' => $notifications->id]) }}"
                    class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif ($notifications->type == 'App\CustomInvoiceNotification' && isset($notifications->data['invoice_id']))
                    <a href="{{ route('v2.invoices.show', ['invoice_id' => $notifications->data['invoice_id']]) }}"
                       class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif($notifications->type == 'App\Notifications\TaskNotification')
                    <a href="{{ route('v2.tasks.show', ['id' => $notifications->data['task_id'], 'notify' => $notifications->id]) }}"
                        class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                    @elseif($notifications->type == 'App\Notifications\MessageNotification')
                        @php
                            $client_user_id = $notifications->data['id'];
                            $name = $notifications->data['name'];
                        @endphp
                        <a href="{{ route('support.message.show.id', ['id' => $client_user_id, 'name' => $name]) }}"
                            class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                        @else
                            @php
                                $task_id = 0;
                                $project = \App\Models\Project::where('client_id', $notifications->data['id'])->first();
                                if ($project != null) {
                                    if (count($project->tasks) != 0) {
                                        $task_id = $project->tasks[0]->id;
                                    }
                                }
                            @endphp
                            <a href="{{ route('v2.tasks.show', ['id' => $task_id, 'notify' => $notifications->id]) }}"
                                class="dropdown-item d-flex">
            @endif
            <div class="notification-icon">
                @if ($notifications->type == 'App\Notifications\LeadNotification')
                    <i class="i-Checked-User text-primary mr-1"></i>
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <i class="i-Money-Bag text-success mr-1"></i>
                @elseif($notifications->type == 'App\CustomInvoiceNotification')
                    <i class="fas fa-dollar-sign text-success mr-1"></i>
                @else
                    <i class="fa-solid fa-bell"></i>
                @endif
            </div>
            <div class="notification-details flex-grow-1">
                <p class="m-0 d-flex align-items-center">
                    <span class="lead-heading">{{ $notifications->data['text'] }}</span>
                    <span class="flex-grow-1"></span>
                    <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                </p>
                <p class="text-small text-muted m-0">
                    {{ $notifications->data['name'] }}</p>
            </div>
            </a>
            @if ($loop->last)
            @endif
            @php
                $k++;
            @endphp
        @endforeach

        @if (v2_acl([0, 1, 4, 5, 6]))
            <a href="{{ route('v2.notifications') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">View all</span>
                    </p>
                </div>
            </a>

            <a href="{{ route('notification.all.read') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-check-double text-success"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">Mark all as read</span>
                    </p>
                </div>
            </a>
        @endif
    </div>
@elseif(Auth::user()->is_employee == 0)
    <div class="badge-top-container">
        <span
            class="badge badge-primary">{{ auth()->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->count() }}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        @php
            $k = 0;
        @endphp
        @foreach (auth()->user()->unreadnotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->latest()->take(10)->get() as $notifications)
            @if ($notifications->type == 'App\Notifications\AssignProjectNotification')
                <a href="{{ route('create.task.by.project.id', ['id' => $notifications->data['project_id'], 'name' => $notifications->data['text'], 'notify' => $notifications->id]) }}"
                    class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif ($notifications->type == 'App\CustomInvoiceNotification' && isset($notifications->data['invoice_id']))
                    <a href="{{ route('v2.invoices.show', ['invoice_id' => $notifications->data['invoice_id']]) }}"
                       class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                @elseif($notifications->type == 'App\Notifications\TaskNotification')
                    <a href="{{ route('v2.tasks.show', ['id' => $notifications->data['task_id'], 'notify' => $notifications->id]) }}"
                        class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                    @elseif($notifications->type == 'App\Notifications\MessageNotification')
                        @php
                            $client_user_id = $notifications->data['id'];
                            $name = $notifications->data['name'];
                        @endphp
                        <a href="{{ route('support.message.show.id', ['id' => $client_user_id, 'name' => $name]) }}"
                            class="unread_notification_nav dropdown-item d-flex" data-id="{{ $notifications->id }}">
                        @else
                            @php
                                $task_id = 0;
                                $project = \App\Models\Project::where('client_id', $notifications->data['id'])->first();
                                if ($project != null) {
                                    if (count($project->tasks) != 0) {
                                        $task_id = $project->tasks[0]->id;
                                    }
                                }
                            @endphp
                            <a href="{{ route('v2.tasks.show', ['id' => $task_id, 'notify' => $notifications->id]) }}"
                                class="dropdown-item d-flex">
            @endif
            <div class="notification-icon">
                @if ($notifications->type == 'App\Notifications\LeadNotification')
                    <i class="i-Checked-User text-primary mr-1"></i>
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <i class="i-Money-Bag text-success mr-1"></i>
                @elseif($notifications->type == 'App\CustomInvoiceNotification')
                    <i class="fas fa-dollar-sign text-success mr-1"></i>
                @else
                    <i class="i-Blinklist text-info mr-1"></i>
                @endif
            </div>
            <div class="notification-details flex-grow-1">
                <p class="m-0 d-flex align-items-center">
                    <span class="lead-heading">{{ strip_tags($notifications->data['text']) }}</span>
                    <span class="flex-grow-1"></span>
                    <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                </p>
                <p class="text-small text-muted m-0">Name: {{ $notifications->data['name'] }}</p>
            </div>
            </a>
            @if ($loop->last)
            @endif
            @php
                $k++;
            @endphp
        @endforeach

        @if (v2_acl([0, 1, 4, 5, 6]))
            <a href="{{ route('v2.notifications') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">View all</span>
                    </p>
                </div>
            </a>

            <a href="{{ route('notification.all.read') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-check-double text-success"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">Mark all as read</span>
                    </p>
                </div>
            </a>
        @endif
    </div>
@elseif(Auth::user()->is_employee == 5 || Auth::user()->is_employee == 1)
    <div class="badge-top-container">
        <span
            class="badge badge-primary">{{count(auth()->user()->unreadNotifications)}}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        @php
            $k = 0;
        @endphp
        @foreach(auth()->user()->unreadnotifications()->latest()->take(10)->get() as $notifications)
        @if($notifications->type == 'App\Notifications\SubTaskNotification')
        <a href="{{ route('v2.subtasks.show', ['id' => $notifications->data['task_id'], 'notify' => $notifications->id]) }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
        @else
        <a href="{{ isset($notifications->data['task_id']) ? (route('v2.tasks.show', ['id' => $notifications->data['task_id'], 'notify' => $notifications->id])) : '#' }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
        @endif
            <div class="notification-icon">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="notification-details flex-grow-1">
                <p class="m-0 d-flex align-items-center">
                    <span class="lead-heading">{{$notifications->data['text']}}</span>
                    <span class="flex-grow-1"></span>
                    <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                </p>
                <p class="text-small text-muted m-0">{{$notifications->data['details']}}</p>
            </div>
        </a>
        @if($loop->last)

        @endif
        @php
            $k++;
        @endphp
        @endforeach

        @if (v2_acl([0, 1, 4, 5, 6]))
            <a href="{{ route('v2.notifications') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">View all</span>
                    </p>
                </div>
            </a>

            <a href="{{ route('notification.all.read') }}" class="unread_notification_nav dropdown-item d-flex">
                <div class="notification-icon">
                    <i class="fa-solid fa-check-double text-success"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">Mark all as read</span>
                    </p>
                </div>
            </a>
        @endif
    </div>
@endif
