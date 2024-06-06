    <div class="main-header">
    <div class="logo">
        <img src="{{ asset('global/img/sidebarlogo.png') }}" alt="{{ config('app.name') }}">
    </div>
    <div class="menu-toggle">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="d-flex align-items-center">
        <!-- / Mega menu -->
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <i class="search-icon text-muted i-Magnifi-Glass1"></i>
        </div>
        <!--<a href="javascript:;" class="brands-list" style="margin-top: 3px;">-->
        <!--    @foreach(Auth::user()->brands as $brands)-->
        <!--    <span>{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $brands->name))) }}</span>-->
        <!--    @endforeach -->
        <!--</a>-->
        
         &nbsp;&nbsp;&nbsp;&nbsp;
        <label style="margin-bottom: 0px !important;font-weight: bold;font-size: 17px;margin-right: 10px;"> Brands </label>
        <select class="form-control" style="width: 50%;">
            @foreach(Auth::user()->brands as $brands)
            <option> {{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $brands->name))) }} </option>
            @endforeach
        </select>
        
    </div>
    <div style="margin: auto"></div>
    <div class="header-part-right">
        <!-- Full screen toggle -->
        <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i>
        <!-- Grid menu Dropdown -->
        <!-- Notificaiton -->
        <div class="dropdown">
            <div class="badge-top-container" role="button" id="dropdownNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="badge badge-primary">{{count(auth()->user()->unreadNotifications)}}</span>
                <i class="i-Bell text-muted header-icon"></i>
            </div>
            <!-- Lead Notification dropdown -->
            <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none" aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
                @php
                $k = 0;
                @endphp
                @foreach(auth()->user()->unreadnotifications as $notifications)
                @if($notifications->type == 'App\Notifications\LeadNotification')
                <a href="{{ route('admin.client.shownotification', ['client' => $notifications->data['id'], 'id' => $notifications->id] ) }}" class="dropdown-item d-flex">
                @elseif($notifications->type == 'App\Notifications\PaymentNotification')
                <a href="" class="dropdown-item d-flex">
                @else
                <a href="" class="dropdown-item d-flex">
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
                        <i class="i-Lock-User mr-1"></i> {{ Auth::user()->name }}
                    </div>
                    <!--<a class="dropdown-item" href="{{ route('sale.edit.profile') }}">Edit Profile</a>-->
                    <!--<a class="dropdown-item" href="{{ route('sale.change.password') }}">Change Password</a>-->
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
            <li class="nav-item {{ (request()->routeIs('home'))? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ url('home') }}">
                    <i class="nav-icon i-Bar-Chart"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('brands.dashboard'))? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('brands.dashboard') }}">
                    <i class="nav-icon i-Medal-2"></i>
                    <span class="nav-text">Brands</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ ( request()->routeIs('client.index') || request()->routeIs('client.edit') || request()->routeIs('client.generate.payment') || request()->routeIs('client.create')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('client.index') }}">
                    <i class="nav-icon i-Add-User"></i>
                    <span class="nav-text">Clients</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('sale.invoice')) || (request()->routeIs('sale.single.invoice') || request()->routeIs('sale.link')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('sale.invoice') }}">
                    <i class="nav-icon i-Credit-Card"></i>
                    <span class="nav-text">Invoices</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('sale.brief.pending')) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('sale.brief.pending') }}">
                    <i class="nav-icon i-Folder-Close"></i>
                    <span class="nav-text">Brief Pending</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ (request()->routeIs('sale.project') || request()->routeIs('sale.form') || request()->routeIs('sale.task.show') ) ? 'active' : '' }}">
                <a class="nav-item-hold" href="{{ route('sale.project') }}">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">All Projects</span>
                </a>
                <div class="triangle"></div>
            </li>

        </ul>
    </div>
    <div class="sidebar-overlay"></div>
</div>