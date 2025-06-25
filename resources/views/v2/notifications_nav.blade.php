<!-- Lead Notification dropdown -->
@if (Auth::user()->is_employee == 2)
    <div class="badge-top-container">
        <span
            class="badge badge-primary">{{ auth()->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->count() }}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        <?php
            $k = 0;
        ?>
        @foreach (auth()->user()->unreadNotifications()->latest()->take(10)->get() as $notifications)
            <?php
                try {
                    $type = $notifications->type ?? null;
                    $data = $notifications->data ?? [];
                    $id = $notifications->id ?? null;

                    // Safely set the URL
                    if ($type == 'App\Notifications\LeadNotification' && isset($data['id'])) {
                        $url = route('admin.client.shownotification', ['client' => $data['id'], 'id' => $id]);
                    } elseif ($type == 'App\CustomInvoiceNotification' && isset($data['invoice_id'])) {
                        $url = route('v2.invoices.show', ['invoice_id' => $data['invoice_id']]);
                    } elseif ($type == 'App\CustomInvoicePaidNotification' && isset($data['invoice_id'])) {
                        $url = route('v2.invoices.show', ['invoice_id' => $data['invoice_id']]);
                    } elseif ($type == 'App\Notifications\PaymentNotification') {
                        $url = '#';
                    } else {
                        $url = '#';
                    }
            ?>

            <a href="{{ $url }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{ $id }}">
                <div class="notification-icon">
                    @if ($type == 'App\Notifications\LeadNotification')
                        <i class="i-Checked-User text-primary mr-1"></i>
                    @elseif ($type == 'App\Notifications\PaymentNotification')
                        <i class="i-Money-Bag text-success mr-1"></i>
                    @elseif ($type == 'App\CustomInvoiceNotification')
                        <i class="fas fa-dollar-sign text-danger mr-1"></i>
                    @elseif ($type == 'App\CustomInvoicePaidNotification')
                        <i class="fas fa-dollar-sign text-success mr-1"></i>
                    @else
                        <i class="fa-solid fa-bell"></i>
                    @endif
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">{{ $data['text'] ?? 'No Text' }}</span>
                        <span class="flex-grow-1"></span>
                        <span class="text-small text-muted ml-3">{{ optional($notifications->created_at)->diffForHumans() ?? '' }}</span>
                    </p>
                    <p class="text-small text-muted m-0">
                        {{ $data['name'] ?? 'No Name' }}
                    </p>
                </div>
            </a>

            <?php
                    $k++;
                } catch (\Exception $e) {
                    // Optional: You can log the error if you want
                    // \Log::error('Notification Render Error: ' . $e->getMessage());
                    continue; // Skip this notification safely if any error occurs
                }
            ?>
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
    <?php
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
    ?>
    <div class="badge-top-container">
        <span class="badge badge-primary">{{ count($notification) }}</span>
    </div>
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none"
        aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
        @foreach (auth()->user()->unreadNotifications()->latest()->take(10)->get() as $notifications)
            <?php
                try {
                    $type = $notifications->type ?? null;
                    $data = $notifications->data ?? [];
                    $id = $notifications->id ?? null;

                    // Initialize the URL to prevent undefined variable issues
                    $url = '';

                    if ($type == 'App\Notifications\LeadNotification') {
                        $url = route('admin.client.shownotification', ['client' => $data['id'] ?? 0, 'id' => $id]);
                    } elseif ($type == 'App\CustomInvoiceNotification') {
                        $url = route('v2.invoices.show', ['invoice_id' => $data['invoice_id'] ?? 0]);
                    } elseif ($type == 'App\Notifications\PaymentNotification') {
                        $url = '#';
                    } elseif ($type == 'App\Notifications\MessageNotification') {
                        $url = route('manager.message.show', ['id' => $data['id'] ?? 0, 'name' => $data['name'] ?? '']);
                    } else {
                        $url = '#';
                    }
            ?>

            <a href="{{ $url }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{ $id }}">
                <div class="notification-icon">
                    @if ($type == 'App\Notifications\LeadNotification')
                        <i class="i-Checked-User text-primary mr-1"></i>
                    @elseif($type == 'App\Notifications\PaymentNotification')
                        <i class="i-Money-Bag text-success mr-1"></i>
                    @elseif($type == 'App\CustomInvoiceNotification')
                        <i class="fas fa-dollar-sign text-success mr-1"></i>
                    @else
                        <i class="fa-solid fa-bell"></i>
                    @endif
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">{{ $data['text'] ?? 'No Text' }}</span>
                        <span class="flex-grow-1"></span>
                        <span class="text-small text-muted ml-3">{{ optional($notifications->created_at)->diffForHumans() ?? '' }}</span>
                    </p>
                    <p class="text-small text-muted m-0">
                        {{ $data['name'] ?? 'No Name' }}
                    </p>
                </div>
            </a>

            <?php
                } catch (\Exception $e) {
                    // You can log the error if needed
                    // \Log::error('Notification Render Error: ' . $e->getMessage());
                    continue; // Skip this iteration safely
                }
            ?>
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
        <?php
            $k = 0;
        ?>
        @foreach (auth()->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->latest()->take(10)->get() as $notifications)
            <?php
                try {
                    $type = $notifications->type ?? null;
                    $data = $notifications->data ?? [];
                    $id = $notifications->id ?? null;
                    $url = '#';

                    if ($type == 'App\Notifications\AssignProjectNotification' && isset($data['project_id'], $data['text'])) {
                        $url = route('create.task.by.project.id', ['id' => $data['project_id'], 'name' => $data['text'], 'notify' => $id]);
                    } elseif ($type == 'App\CustomInvoiceNotification' && isset($data['invoice_id'])) {
                        $url = route('v2.invoices.show', ['invoice_id' => $data['invoice_id']]);
                    } elseif ($type == 'App\Notifications\TaskNotification' && isset($data['task_id'])) {
                        $url = route('v2.tasks.show', ['id' => $data['task_id'], 'notify' => $id]);
                    } elseif ($type == 'App\Notifications\MessageNotification' && isset($data['id'], $data['name'])) {
                        $url = route('support.message.show.id', ['id' => $data['id'], 'name' => $data['name']]);
                    } else {
                        // Handle the else case safely
                        $task_id = 0;
                        if (isset($data['id'])) {
                            $project = \App\Models\Project::where('client_id', $data['id'])->first();
                            if ($project && $project->tasks->count() > 0) {
                                $task_id = $project->tasks->first()->id;
                            }
                        }
                        $url = route('v2.tasks.show', ['id' => $task_id, 'notify' => $id]);
                    }
            ?>

            <a href="{{ $url }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{ $id }}">
                <div class="notification-icon">
                    @if ($type == 'App\Notifications\LeadNotification')
                        <i class="i-Checked-User text-primary mr-1"></i>
                    @elseif ($type == 'App\Notifications\PaymentNotification')
                        <i class="i-Money-Bag text-success mr-1"></i>
                    @elseif ($type == 'App\CustomInvoiceNotification')
                        <i class="fas fa-dollar-sign text-success mr-1"></i>
                    @else
                        <i class="fa-solid fa-bell"></i>
                    @endif
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">{{ $data['text'] ?? 'No Text' }}</span>
                        <span class="flex-grow-1"></span>
                        <span class="text-small text-muted ml-3">{{ optional($notifications->created_at)->diffForHumans() ?? '' }}</span>
                    </p>
                    <p class="text-small text-muted m-0">
                        {{ $data['name'] ?? 'No Name' }}
                    </p>
                </div>
            </a>

            <?php
                    $k++;
                } catch (\Exception $e) {
                    // Optional: Log the error
                    // \Log::error('Notification Render Error: ' . $e->getMessage());
                    continue; // Skip this notification safely
                }
            ?>
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
        <?php
            $k = 0;
        ?>
        @foreach (auth()->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\MessageNotification')->latest()->take(10)->get() as $notifications)
            <?php
                try {
                    $type = $notifications->type ?? null;
                    $data = $notifications->data ?? [];
                    $id = $notifications->id ?? null;
                    $url = '#';

                    if ($type == 'App\Notifications\AssignProjectNotification' && isset($data['project_id'], $data['text'])) {
                        $url = route('create.task.by.project.id', ['id' => $data['project_id'], 'name' => $data['text'], 'notify' => $id]);
                    } elseif ($type == 'App\CustomInvoiceNotification' && isset($data['invoice_id'])) {
                        $url = route('v2.invoices.show', ['invoice_id' => $data['invoice_id']]);
                    } elseif ($type == 'App\Notifications\TaskNotification' && isset($data['task_id'])) {
                        $url = route('v2.tasks.show', ['id' => $data['task_id'], 'notify' => $id]);
                    } elseif ($type == 'App\Notifications\MessageNotification' && isset($data['id'], $data['name'])) {
                        $url = route('support.message.show.id', ['id' => $data['id'], 'name' => $data['name']]);
                    } else {
                        // Handle other types safely
                        $task_id = 0;
                        if (isset($data['id'])) {
                            $project = \App\Models\Project::where('client_id', $data['id'])->first();
                            if ($project && $project->tasks->count() > 0) {
                                $task_id = $project->tasks->first()->id;
                            }
                        }
                        $url = route('v2.tasks.show', ['id' => $task_id, 'notify' => $id]);
                    }
            ?>

            <a href="{{ $url }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{ $id }}">
                <div class="notification-icon">
                    @if ($type == 'App\Notifications\LeadNotification')
                        <i class="i-Checked-User text-primary mr-1"></i>
                    @elseif ($type == 'App\Notifications\PaymentNotification')
                        <i class="i-Money-Bag text-success mr-1"></i>
                    @elseif ($type == 'App\CustomInvoiceNotification')
                        <i class="fas fa-dollar-sign text-success mr-1"></i>
                    @else
                        <i class="i-Blinklist text-info mr-1"></i>
                    @endif
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">{{ strip_tags($data['text'] ?? 'No Text') }}</span>
                        <span class="flex-grow-1"></span>
                        <span class="text-small text-muted ml-3">{{ optional($notifications->created_at)->diffForHumans() ?? '' }}</span>
                    </p>
                    <p class="text-small text-muted m-0">Name: {{ $data['name'] ?? 'No Name' }}</p>
                </div>
            </a>

            <?php
                    $k++;
                } catch (\Exception $e) {
                    // Optional: Log the error for debugging
                    // \Log::error('Notification Render Error: ' . $e->getMessage());
                    continue; // Skip this notification safely if any error occurs
                }
            ?>
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
        <?php
            $k = 0;
        ?>
        @foreach (auth()->user()->unreadNotifications()->latest()->take(10)->get() as $notifications)
            <?php
                try {
                    $type = $notifications->type ?? null;
                    $data = $notifications->data ?? [];
                    $id = $notifications->id ?? null;

                    if ($type == 'App\Notifications\SubTaskNotification' && isset($data['task_id'])) {
                        $url = route('v2.subtasks.show', ['id' => $data['task_id'], 'notify' => $id]);
                    } elseif (isset($data['task_id'])) {
                        $url = route('v2.tasks.show', ['id' => $data['task_id'], 'notify' => $id]);
                    } else {
                        $url = '#';
                    }
            ?>

            <a href="{{ $url }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{ $id }}">
                <div class="notification-icon">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        <span class="lead-heading">{{ $data['text'] ?? 'No Text' }}</span>
                        <span class="flex-grow-1"></span>
                        <span class="text-small text-muted ml-3">{{ optional($notifications->created_at)->diffForHumans() ?? '' }}</span>
                    </p>
                    <p class="text-small text-muted m-0">{{ $data['details'] ?? 'No Details' }}</p>
                </div>
            </a>

            <?php
                    $k++;
                } catch (\Exception $e) {
                    // Optional: Log the error
                    // \Log::error('Notification Render Error: ' . $e->getMessage());
                    continue; // Skip this notification safely if any error occurs
                }
            ?>
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
