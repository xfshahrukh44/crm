<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="google-site-verification" content="ztvhppa4266dY22ykOfFHz9Q7KMt_Mth3-UI6VWWwcU" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{asset('images/favicon-96x96.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-16x16.png')}}">

    <title>{{ config('app.name') }} - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
          integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" type="text/css" href="{{asset('v2/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('v2/css/responsive.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">

    <link href="{{ asset('newglobal/css/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/toastr.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/sweetalert2.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('newglobal/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        span.select2-selection.select2-selection--single {
            border-radius: 20px !important;
            height: 35px !important;
            border: 1px solid #ced4da !important;
        }
        span.select2-selection.select2-selection--multiple {
            border-radius: 20px !important;
            /*height: 35px !important;*/
            border: 1px solid #ced4da !important;
        }

        .select2-selection__rendered {
            margin-top: 0.5% !important;
        }

        span.select2-selection__arrow {
            right: 2% !important;
            top: 14% !important;
        }

        .notification .dropdown-menu.dropdown-menu-right.notification-dropdown.rtl-ps-none {
            overflow-y: scroll;
            height: auto;
            max-height: 300px;
            border-radius: 5px;
            box-shadow: 0 0 5px 1px #0000003b;
            top: 30px;
        }

        .notification .dropdown-menu.dropdown-menu-right.notification-dropdown.rtl-ps-none .notification-icon {
            background: #eee;
            height: 100%;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .notification .dropdown-menu.dropdown-menu-right.notification-dropdown.rtl-ps-none a {
            display: flex;
            align-items: center;
            padding: 0;
            height: 72px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 0;
            background: transparent;
        }

        .notification .dropdown-menu.dropdown-menu-right.notification-dropdown.rtl-ps-none {
            padding: 0;
        }

        .notification .dropdown-menu.dropdown-menu-right.notification-dropdown.rtl-ps-none .notification-icon i {
            font-size: 18px;
        }

        div#dropdownNotification {
            position: relative;
            z-index: 0;
        }

        div#dropdownNotification .badge-top-container {
            position: absolute;
            z-index: 0;
            top: -15px;
            right: -10px;
        }

        div#dropdownNotification .notification-details.flex-grow-1 p {
            font-size: 12px;
        }
    </style>

    @yield('css')
</head>

<body>
{{--header--}}
<header>
    @if(session()->has('v2-coming-from-admin'))
        <div class="container-fluid p-0">
            <div class="row m-0 py-1 text-center justify-content-center text-white" style="background-color: #059bd4;">
                Admin navigation
                <a href="{{route('v2.admin.back_to_admin')}}" class="badge badge-sm bg-danger text-white px-2 ml-2 d-flex align-items-center">
                    <small>
                        {{--                                <i class="fas fa-arrow-left mr-1"></i>--}}
                        <b>Exit</b>
                    </small>
                </a>
            </div>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="custom-navbar">
                    <nav class="navbar navbar-expand-lg navbar-light ">
                        <a class="navbar-brand" href="{{route('v2.dashboard')}}"> <img src="{{asset('v2/images/logo.png')}}" class="img-fluid"> </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                                <div class="serach-name">


                                    <div class="serach">
                                        <input class="form-control mr-sm-2" type="search" placeholder="Search" id="search-bar"
                                            aria-label="Search">
                                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i
                                                class="fa-solid fa-magnifying-glass"></i></button>
                                    </div>

                                    <div class="dropdown user-name">
                                        <div class="user col align-self-end px-0">
                                            <a href="javascript:;" alt="" data-toggle="dropdown"
                                               aria-haspopup="true" aria-expanded="false">
                                                <img src="{{ auth()->user()->image ? asset(auth()->user()->image) : asset('images/avatar.png') }}"
                                                     width="50"
                                                     style="border-radius: 25px; object-fit: cover; width: 40px; height: 40px;">
                                                {{--                                                <span class="auth-name">{{ auth()->user()->name }}</span>--}}
                                                {{--                                                <i class="fa-solid fa-caret-down"></i>--}}
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 aria-labelledby="userDropdown" x-placement="bottom-end"
                                                 style="position: absolute; will-change: transform; top: 6px; right: 79%; transform: translate3d(41px, 36px, 0px);">

                                                <span class="auth-name ml-4">{{ auth()->user()->name }}</span>
                                                <a class="dropdown-item mt-0" href="javascript:void(0);">
                                                    <span class="badge badge-sm bg-dark text-white m-auto">{{get_role_badge_text()}}</span>
                                                </a>
                                                <a class="dropdown-item" href="{{ route('v2.profile') }}">
                                                    Profile
                                                </a>

                                                <a class="dropdown-item" href="https://designcrm.net/logout"
                                                   onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                                    Sign out
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                      style="display: none;">
                                                    @csrf
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <div class="notification" role="button" id="dropdownNotification"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <a href="javascript:;"><i class="fa-solid fa-bell"></i></a>

                                            <!-- Lead Notification dropdown -->
                                            @include('v2.notifications_nav')
                                        </div>
                                    </div>
                                    <div class="open-side-menu">
                                        <i class="fa-solid fa-bars"></i>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <div class="main-side-menu">
        {{-- side menu --}}
        <div class="side-menu">

            <div class="search-container">
                {{--                    <input type="text" class="form-control search-input" --}}
                {{--                           placeholder="Search..."> --}}
                {{--                    <i class="fas fa-search search-icon"></i> --}}

            @if(session()->has('v2-coming-from-admin'))
                <br />
            @endif
        </div>
        <ul style="max-height: 87%; overflow-y: scroll; scrollbar-width: none; -ms-overflow-style: none;">
            @if(in_array($user_role_id, [2, 6, 4, 0, 1, 5]))
                <li>
                    <a href="{{route('v2.dashboard')}}" class="{{ request()->routeIs('v2.dashboard') ? 'active' : '' }}">
                        <i class="mr-2 fas fa-home"></i>Dashboard
                    </a>
                </li>
            @endif

                {{--                    @if (in_array($user_role_id, [2])) --}}
                {{--                        <li> --}}
                {{--                            <a href="#" class=""> --}}
                {{--                                Brands dashboard --}}
                {{--                            </a> --}}
                {{--                        </li> --}}
                {{--                    @endif --}}

                @if (in_array($user_role_id, [2, 6]))
                    <li>
                        <a href="{{ route('v2.revenue') }}"
                            class="{{ request()->routeIs('v2.revenue') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-chart-line"></i>Revenue
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2]))
                    <li>
                        <a href="{{ route('v2.merchants') }}"
                            class="{{ request()->routeIs('v2.merchants') || request()->routeIs('v2.merchants.create') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-store"></i>Merchants
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [0, 2, 4, 6]))
                    <li>
                        <a href="{{ route('v2.messages') }}" class="{{ request()->routeIs('v2.messages') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-envelope"></i> Messages
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [6, 4, 0, 1, 5]))
                    <li>
                        <a href="{{ route('v2.notifications') }}" class="{{ request()->routeIs('v2.notifications') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-bell"></i> Notifications
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 4, 0]))
                    <li>
                        <a href="{{ route('v2.clients') }}" class="{{ request()->routeIs('v2.clients*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-user-friends"></i> Clients
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 4, 0]))
                    <li>
                        <a href="{{ route('v2.invoices') }}" class="{{ request()->routeIs('v2.invoices') || request()->routeIs('v2.invoices.create')|| request()->routeIs('v2.invoices.show') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-file-invoice-dollar"></i> Invoices
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6]))
                    <li>
                        <a href="{{ route('v2.invoices.refund.cb') }}" class="{{ request()->routeIs('v2.invoices.refund.cb') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-undo-alt"></i> Refund/CB
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6]))
                    <li>
                        <a href="{{ route('v2.invoices.sales.sheet') }}" class="{{ request()->routeIs('v2.invoices.sales.sheet') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-chart-bar"></i> Sales sheet
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6]))
                    <li>
                        <a href="{{ route('v2.invoices.admin.invoices') }}" class="{{ request()->routeIs('v2.invoices.admin.invoices') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-briefcase"></i> Admin invoices
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 0]))
                    <li>
                        <a href="{{ route('v2.leads') }}" class="{{ request()->routeIs('v2.leads*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-lightbulb"></i> Leads
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 4]) && !user_is_cs())
                    <li>
                        <a href="{{ route('v2.briefs.pending') }}" class="{{ request()->routeIs('v2.briefs.pending') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-file-alt"></i> Briefs pending
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 4]) && !user_is_cs())
                    <li>
                        <a href="{{ route('v2.pending.projects') }}" class="{{ request()->routeIs('v2.pending.projects') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-spinner"></i> Pending projects
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 4]))
                    <li>
                        <a href="{{ route('v2.projects') }}" class="{{ request()->routeIs('v2.projects') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-project-diagram"></i> Projects
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6, 4, 1]))
                    <li>
                        <a href="{{ route('v2.tasks') }}" class="{{ request()->routeIs('v2.tasks*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-tasks"></i> Tasks
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [1, 5]))
                    <li>
                        <a href="{{ route('v2.subtasks') }}" class="{{ request()->routeIs('v2.subtasks*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-list-ul"></i> Sub tasks
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2]))
                    <li>
                        <a href="{{ route('v2.services') }}" class="{{ request()->routeIs('v2.services*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-concierge-bell"></i> Services
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2]))
                    <li>
                        <a href="{{ route('v2.brands') }}" class="{{ request()->routeIs('v2.brands*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-tags"></i> Brands
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2]))
                    <li>
                        <a href="{{ route('v2.users.production') }}" class="{{ request()->routeIs('v2.users.production*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-industry"></i> Production
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2, 6]))
                    <li>
                        <a href="{{ route('v2.users.sales') }}" class="{{ request()->routeIs('v2.users.sales*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-user-tie"></i> Sale Agent
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2]))
                    <li>
                        <a href="{{ route('v2.users.qa') }}" class="{{ request()->routeIs('v2.users.qa*') ? 'active' : '' }}">
                            <i class="mr-2 fas fa-search"></i> QA
                        </a>
                    </li>
                @endif

                @if (in_array($user_role_id, [2]))
                    <li>
                        <a href="#" class="">
                            <i class="mr-2 fas fa-graduation-cap"></i> Tutorials
                        </a>
                    </li>
                @endif
        </ul>
    </div>

        {{-- main content --}}
        @yield('content')
    </div>


{{--footer--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>

<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<script>
    $('.dropify').dropify();
</script>

<script src="{{ asset('newglobal/js/datatables.min.js') }}"></script>
<script src="{{ asset('newglobal/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('newglobal/js/select2.min.js') }}"></script>
{{--        <script>--}}
{{--            if($('#zero_configuration_table').length != 0){--}}
{{--                $('#zero_configuration_table').DataTable({--}}
{{--                    order: [[0, "asc"]],--}}
{{--                    responsive: true // or keep it true if you want resizing but no column hiding--}}
{{--                });--}}

{{--            }--}}
{{--        </script>--}}

<script src="{{ asset('newglobal/js/toastr.min.js') }}"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "timeOut": 0,
        "extendedTimeOut": 0
    };
</script>

@if(session()->has('success'))
    <script>
        toastr.success("{{session()->get('success')}}");
    </script>
@endif
@if(session()->has('error'))
    <script>
        toastr.error("{{session()->get('error')}}");
    </script>
@endif

<script>

    $(document).ready(function () {
        $('.select2').select2();

        $('.open-side-menu').on('click', function (e) {
            e.preventDefault(); // Prevent default action if it's a link or button
            $('.side-menu').toggleClass('active');
            $('.for-slider-main-banner').toggleClass('active');
        });

        let activeItem = $('li .active').get(0);
        if (activeItem) {
            activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    //disable submit button on form ::after
    $('body').on('submit', 'form', function (e) {
        let btn = $(this).find('button[type="submit"]');
        if (btn) {
            btn.prop('disabled', true).text('Please wait');
        }
    });
</script>

{{--Pusher--}}
@if(v2_acl([1, 4, 6]))
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        $(document).ready(() => {
            //global vars
            let auth_id = parseInt('{{auth()->id()}}');

            // Enable Pusher logging - don't include this in production
            Pusher.logToConsole = true;

            var pusher = new Pusher('7d1bc788fe2aaa7a2ea5', {
                cluster: 'ap2'
            });

            var channel = pusher.subscribe('v2-message-channel');
            channel.bind('v2-new-message', function(data) {
                if (data.for_ids && data.for_ids.includes(auth_id)) {
                    swal({
                        icon: 'info',
                        title: data.text,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: "Open",
                    }).then((result) => {
                        if (result && data.redirect_url) {
                            window.location.href = data.redirect_url
                        }
                    });
                }
            });

            @if(v2_acl([6]))
                var invoice_channel = pusher.subscribe('v2-buh-'+auth_id+'-invoice-channel');
                invoice_channel.bind('invoice-paid', function(data) {
                    swal({
                        icon: 'info',
                        title: 'Invoice #'+data.invoice.id+' ($'+data.invoice.amount+') has been marked as paid!',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: "View invoice",
                    }).then((result) => {
                        if (result && data.invoice.redirect_url) {
                            window.location.href = data.invoice.redirect_url
                        }
                    });
                });
            @endif
        });
    </script>
@endif

@if(v2_acl([2, 0, 4, 6]))
    @php
        $reminders = \Illuminate\Support\Facades\DB::table('reminders')
            ->where('user_id', auth()->id())
            ->where('ping_time', '<=', \Carbon\Carbon::now()->addHours(10))
            ->where('ping_time', '>=', \Carbon\Carbon::now())
            ->get();
    @endphp
    @if(count($reminders))
        <script>
            toastr.options = {
                "closeButton": true,
                "timeOut": 0,
                "extendedTimeOut": 0,
                "escapeHtml": false,
            };

            let user_reminders = @json($reminders);
            console.log('user_reminders', user_reminders);
            for (const reminder of user_reminders) {
                let targetTime = new Date(reminder.ping_time);
                let now = new Date();
                let delay = targetTime.getTime() - now.getTime();

                if (delay > 0) {
                    setTimeout(() => {
                        toastr.info(`<div>
                                            <h6>`+reminder.heading+`</h6><br>
                                            <p>`+reminder.text+`</p>
                                        </div>`, "Reminder");
                    }, delay);
                }
            }
        </script>
    @endif
@endif

{{--Autocomplete search with jQuery UI--}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
<script>
    $( function() {
        $.widget( "custom.catcomplete", $.ui.autocomplete, {
            _create: function() {
                this._super();
                this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
            },
            _renderMenu: function( ul, items ) {
                var that = this,
                    currentCategory = "";
                $.each( items, function( index, item ) {
                    var li;
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    li = that._renderItemData( ul, item );
                    if ( item.category ) {
                        li.attr( "aria-label", item.category + " : " + item.label );
                        li.attr( "data-type", item.category );
                        li.attr( "data-id", item.id );
                    }
                });
            }
        });
    } );
</script>
<script>
    var typingTimer;                // Timer identifier
    var doneTypingInterval = 100;   // Time in ms, 0.5 second for example

    // On keyup, start the countdown
    $('#search-bar').on('keyup', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    // On keydown, clear the countdown
    $('#search-bar').on('keydown', function () {
        clearTimeout(typingTimer);
    });

    // User is "finished typing," do something
    function doneTyping () {
        // Do something after user has stopped typing
        $.ajax({
            url: '{{route("v2.clients.search.bar")}}',
            method: 'GET',
            data: {
                query: $( "#search-bar" ).val(),
            },
            success: (data) => {
                data = JSON.parse(data);

                $( "#search-bar" ).catcomplete({
                    delay: 0,
                    source: data,
                    search: (e, item) => {
                        console.log(item);
                    }
                });

                $( "#search-bar" ).trigger('change');
            }
        });
    }

    $('body').on('click', '.ui-menu-item', function () {
        let redirect_url = "{{route('v2.clients.show', 'temp')}}";
        redirect_url = redirect_url.replaceAll('temp', $(this).data('id'));
        window.location.href = redirect_url;
    });

    $('.unread_notification_nav').on('click', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        let notificationId = $(this).data('id');
        window.location.href = url;
    });
</script>

@yield('script')
</body>

</html>
