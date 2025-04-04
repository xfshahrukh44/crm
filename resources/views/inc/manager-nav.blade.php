
<div class="main-header">
    <div class="logo">
        <img src="{{ asset('global/img/sidebarlogo.png') }}" alt="{{ config('app.name') }}">
    </div>
    <div class="menu-toggle">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="d-flex align-items-center" style="width: 40%;">
        <!-- / Mega menu -->
        <div class="search-bar">
            <input type="text" placeholder="Search" id="search-bar">
            <i class="search-icon text-muted i-Magnifi-Glass1"></i>
        </div>
        @if(session()->has('coming-from-admin'))
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <span>
                <a href="{{route('admin.back_to_admin')}}" class="btn btn-info">
                    <i class="fas fa-arrow-left"></i>
                    Back to admin
                </a>
            </span>
        @endif

        <?php if(auth()->user()->id == "2260"){ ?>

            <a href="javascript:;" class="brands-list" style="margin-top: 3px;">
                <span> All Brands For QA Team </span>
            </a>


        <?php }else if(auth()->user()->id == "2314"){ ?>

            <a href="javascript:;" class="brands-list" style="margin-top: 3px;">
                <span> All Brands For QA Team </span>
            </a>

        <?php }else if(auth()->user()->id == "1314"){ ?>

             <a href="javascript:;" class="brands-list" style="margin-top: 3px;">
                <span> All Brands For QA Team </span>
            </a>

        <?php }else if(auth()->user()->id == "3088"){ ?>

             <a href="javascript:;" class="brands-list" style="margin-top: 3px;">
                <span> All Brands For QA Team </span>
            </a>

        <?php }else{ ?>

            <!--<a href="javascript:;" class="brands-list" style="margin-top: 3px;">-->
            <!--    @foreach(Auth::user()->brands as $brands)-->
            <!--    <span>{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $brands->name))) }}</span>-->
            <!--    @endforeach -->
            <!--</a>-->
            &nbsp;&nbsp;&nbsp;&nbsp;
{{--            <label style="margin-bottom: 0px !important;font-weight: bold;font-size: 17px;margin-right: 10px;"> Brands </label>--}}
{{--            <select class="form-control" style="width: 40%;" id="select_brand_id">--}}
{{--                <option value="">Select brand</option>--}}
{{--                @foreach(Auth::user()->brands as $brands)--}}
{{--                    <option> {{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $brands->name))) }} </option>--}}
{{--                    <option value="{{$brands->id}}" {!! $brands->id == \Illuminate\Support\Facades\Request::get('brand_id') ? 'selected' : '' !!}>{{$brands->name}}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}

        <?php } ?>

    </div>
    <div style="margin: auto"></div>
    <div class="header-part-right">
        <!-- Full screen toggle -->
        <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i>
        <!-- Grid menu Dropdown -->
        <!-- Notificaiton -->
        <div class="dropdown">
            @php
                $k = 0;
                $notification = auth()->user()->unreadnotifications()
                //for qa manager accounts
                ->when(in_array(auth()->id(), [3839, 3838, 3837]), function ($q) {
                    return $q->where('type', 'App\Notifications\MessageNotification');
                })
                ->where('type', '!=', 'App\Notifications\MessageNotification')
                ->latest()->take(10)->get();
            @endphp
            <div class="badge-top-container" role="button" id="dropdownNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="badge badge-primary">{{count($notification)}}</span>
                <i class="i-Bell text-muted header-icon"></i>
            </div>
            <!-- Lead Notification dropdown -->
            <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none" aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
                @foreach($notification as $notifications)
                    @if($notifications->type == 'App\Notifications\LeadNotification')
                    <a href="{{ route('admin.client.shownotification', ['client' => $notifications->data['id'], 'id' => $notifications->id] ) }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                    @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                    <a href="" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                    @elseif($notifications->type == 'App\Notifications\MessageNotification')
                    <a href="{{ route('manager.message.show', ['id' => $notifications->data['id'], 'name' => $notifications->data['name']]) }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                    @else
                    <a href="" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                    @endif
                        <div class="notification-icon">
                            @if($notifications->type == 'App\Notifications\LeadNotification')
                                <i class="i-Checked-User text-primary mr-1"></i>
                            @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                                <i class="i-Money-Bag text-success mr-1"></i>
                            @else
                                <i class="i-Blinklist text-info mr-1"></i>
                            @endif
                        </div>
                        <div class="notification-details flex-grow-1">
                            <p class="m-0 d-flex align-items-center">
                                <span class="lead-heading">{{$notifications->data['text']}}</span>
                                <span class="flex-grow-1"></span>
                                <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                            </p>
                            <p class="text-small text-muted m-0">Name: {{$notifications->data['name']}}</p>
                        </div>
                    </a>
                    @if($loop->last)

                    @endif
                    @php
                        $k++;
                    @endphp
                @endforeach
                <a href="{{ route('notification.all.read') }}" class="dropdown-item d-flex">
                    <div class="notification-icon">
                        <i class="i-Check text-primary mr-1"></i>
                    </div>
                    <div class="notification-details flex-grow-1">
                        <p class="m-0 d-flex align-items-center">
                            <span class="lead-heading">Mark All As Read</span>
                            <span class="flex-grow-1"></span>
                        </p>
                    </div>
                </a>
                <a href="{{ route('manager.my-notifications') }}" class="dropdown-item d-flex">
                    <div class="notification-icon">
                        <i class="i-Check text-primary mr-1"></i>
                    </div>
                    <div class="notification-details flex-grow-1">
                        <p class="m-0 d-flex align-items-center">
                            <span class="lead-heading">View all</span>
                            <span class="flex-grow-1"></span>
                        </p>
                    </div>
                </a>
            </div>
        </div>

        <div class="dropdown">
            @php
                $k = 0;
                $notification = auth()->user()->unreadnotifications()
                //for qa manager accounts
                ->when(in_array(auth()->id(), [3839, 3838, 3837]), function ($q) {
                    return $q->where('type', 'App\Notifications\MessageNotification');
                })
                ->where('type', '=', 'App\Notifications\MessageNotification')
                ->latest()->take(10)->get();
            @endphp
            <div class="badge-top-container" role="button" id="dropdownNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="badge badge-primary">{{count($notification)}}</span>
                <i class="i-Speach-Bubble-3 text-muted header-icon"></i>
            </div>
            <!-- Lead Notification dropdown -->
            <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none" aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
                @foreach($notification as $notifications)
                    @if($notifications->type == 'App\Notifications\LeadNotification')
                        <a href="{{ route('admin.client.shownotification', ['client' => $notifications->data['id'], 'id' => $notifications->id] ) }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                            @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                                <a href="" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                                    @elseif($notifications->type == 'App\Notifications\MessageNotification')
                                        <a href="{{ route('manager.message.show', ['id' => $notifications->data['id'], 'name' => $notifications->data['name']]) }}" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                                            @else
                                                <a href="" class="unread_notification_nav dropdown-item d-flex" data-id="{{$notifications->id}}">
                                                    @endif
                                                    <div class="notification-icon">
                                                        @if($notifications->type == 'App\Notifications\LeadNotification')
                                                            <i class="i-Checked-User text-primary mr-1"></i>
                                                        @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                                                            <i class="i-Money-Bag text-success mr-1"></i>
                                                        @else
                                                            <i class="i-Blinklist text-info mr-1"></i>
                                                        @endif
                                                    </div>
                                                    <div class="notification-details flex-grow-1">
                                                        <p class="m-0 d-flex align-items-center">
                                                            <span class="lead-heading">{{$notifications->data['text']}}</span>
                                                            <span class="flex-grow-1"></span>
                                                            <span class="text-small text-muted ml-3">{{ $notifications->created_at->diffForHumans() }}</span>
                                                        </p>
                                                        <p class="text-small text-muted m-0">Name: {{$notifications->data['name']}}</p>
                                                    </div>
                                                </a>
                                                @if($loop->last)

                                                @endif
                                                @php
                                                    $k++;
                                                @endphp
                                                @endforeach
                                                <a href="{{ route('notification.all.read') }}" class="dropdown-item d-flex">
                                                    <div class="notification-icon">
                                                        <i class="i-Check text-primary mr-1"></i>
                                                    </div>
                                                    <div class="notification-details flex-grow-1">
                                                        <p class="m-0 d-flex align-items-center">
                                                            <span class="lead-heading">Mark All As Read</span>
                                                            <span class="flex-grow-1"></span>
                                                        </p>
                                                    </div>
                                                </a>
                                                <a href="{{ route('manager.my-notifications') }}" class="dropdown-item d-flex">
                                                    <div class="notification-icon">
                                                        <i class="i-Check text-primary mr-1"></i>
                                                    </div>
                                                    <div class="notification-details flex-grow-1">
                                                        <p class="m-0 d-flex align-items-center">
                                                            <span class="lead-heading">View all</span>
                                                            <span class="flex-grow-1"></span>
                                                        </p>
                                                    </div>
                                                </a>
            </div>
        </div>
        <!-- Notificaiton End -->
        <!-- User avatar dropdown -->
        <div class="dropdown">
            <div class="user col align-self-end">
                @if(Auth::user()->image != '')
                <img src="{{ asset(Auth::user()->image) }}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @else
                <img src="{{ asset('global/img/user.png') }}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @endif
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <i class="i-Lock-User mr-1"></i> {{ Auth::user()->name }} {{ Auth::user()->last_name }}
                    </div>
                    <!--<a class="dropdown-item" href="{{ route('manager.edit.profile') }}">Edit Profile</a>-->
                    <!--<a class="dropdown-item" href="{{ route('manager.change.password') }}">Change Password</a>-->
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">Sign out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="side-content-wrap">
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar="" data-suppress-scroll-x="true">
        <ul class="navigation-left">
            <li class="nav-item {{ (request()->routeIs('salemanager.dashboard'))? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ url('manager/dashboard') }}">
                    <i class="nav-icon i-Bar-Chart"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('brands.dashboard.v3')) || (request()->routeIs('brands.detail')) || (request()->routeIs('clients.detail')) || (request()->routeIs('projects.detail'))? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('brands.dashboard.v3') }}">
                    <i class="nav-icon i-Medal-2"></i>
                    <span class="nav-text">Brands</span>
                    <span class="badge badge-success">
                        <b>NEW</b>
                    </span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('revenue')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('revenue') }}">
                    <i class="nav-icon fas fa-dollar-sign"></i>
                    <span class="nav-text">Revenue</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->routeIs('manager.message') || request()->routeIs('manager.message.show') ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.message') }}">
                    <i class="nav-icon i-Speach-Bubble-3"></i>
                    <span class="nav-text">Messages</span>
{{--                    @if(in_array(auth()->id(), [ 2260, 3837, 3839, 3838 ]))--}}
                        <span class="counter">0</span>
{{--                    @else--}}
{{--                        <span class="counter">{{ get_unread_notification_count_for_buh() }}</span>--}}
{{--                    @endif--}}
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->routeIs('manager.notification') || request()->routeIs('manager.notification') ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.notification') }}">
                    <i class="nav-icon i-Bell"></i>
                    <span class="nav-text">Notificaiton</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ ( request()->routeIs('salemanager.client.index') || request()->routeIs('salemanager.client.create') || request()->routeIs('manager.generate.payment') || request()->routeIs('manager.client.edit')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('salemanager.client.index') }}">
                    <i class="nav-icon i-Add-User"></i>
                    <span class="nav-text">Clients</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.invoice')) || (request()->routeIs('manager.single.invoice')) || (request()->routeIs('manager.invoice.edit')) || (request()->routeIs('manager.link')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.invoice') }}">
                    <i class="nav-icon i-Credit-Card"></i>
                    <span class="nav-text">Invoices</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.refund.cb')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.refund.cb') }}">
                    <i class="nav-icon i-Credit-Card text-danger"></i>
                    <span class="nav-text text-danger">Refund/CB</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.sales.sheet')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.sales.sheet') }}">
                    <i class="nav-icon i-Credit-Card text-success"></i>
                    <span class="nav-text text-success">Sales Sheet</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.admin-invoice.index')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.admin-invoice.index') }}">
                    <i class="nav-icon i-Credit-Card"></i>
                    <span class="nav-text">Admin Invoices</span>
                </a>
                <div class="triangle"></div>
            </li>


            <li class="nav-item {{ (request()->routeIs('manager.lead.index')) ? 'active' : '' }}">
                <a class="nav-item-hold text-warning" href="{{ route('manager.lead.index') }}">
                    <i class="nav-icon i-Administrator"></i>
                    <span class="nav-text">Leads</span>
                </a>
                <div class="triangle"></div>
            </li>

            <li class="nav-item {{ (request()->routeIs('manager.brief.pending')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.brief.pending') }}">
                    <i class="nav-icon i-Folder-Close"></i>
                    <span class="nav-text">Brief Pending</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.pending.project')) || (request()->routeIs('manager.pending.project.details') ) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.pending.project') }}">
                    <i class="nav-icon i-Folder-Loading"></i>
                    <span class="nav-text">Pending Projects</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.project.index') ) || (request()->routeIs('manager.project.show') ) || (request()->routeIs('manager.project.edit') ) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.project.index') }}">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">Projects</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ ( request()->routeIs('manager.user.sales') ) || ( request()->routeIs('manager.user.sales.edit') ) || ( request()->routeIs('manager.user.sales.create') )  ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.user.sales') }}">
                    <i class="nav-icon i-Administrator"></i>
                    <span class="nav-text">Sale Agent</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('manager.task.index') ) || (request()->routeIs('manager.task.show') ) || (request()->routeIs('manager.task.edit') ) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('manager.task.index') }}">
                    <i class="nav-icon i-Receipt-4"></i>
                    <span class="nav-text">Tasks</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('tutorials')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('tutorials') }}">
                    <i class="nav-icon fas fa-play"></i>
                    <span class="nav-text">Tutorials</span>
                </a>
                <div class="triangle"></div>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay"></div>
</div>
