<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="google-site-verification" content="ztvhppa4266dY22ykOfFHz9Q7KMt_Mth3-UI6VWWwcU" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} - @yield('title')</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
              integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
              integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
              crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <link rel="stylesheet" type="text/css" href="{{asset('v2/css/style.css')}}">
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
        </style>

        @yield('css')
    </head>

    <body>
        {{--header--}}
        <header>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="custom-navbar">
                            <nav class="navbar navbar-expand-lg navbar-light ">
                                <a class="navbar-brand" href="#"> <img src="{{asset('v2/images/logo.png')}}" class="img-fluid"> </a>
                                <button class="navbar-toggler" type="button" data-toggle="collapse"
                                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                        aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>

                                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                                    <div class="serach-name">
                                        <div class="dropdown user-name">
                                            <div class="user col align-self-end">
                                                <img src="{{auth()->user()->image ? asset(auth()->user()->image) : asset('images/avatar.png')}}" width="50" style="border-radius: 25px;">
                                                <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="auth-name">{{auth()->user()->name}}</span>
                                                    <i class="fa-solid fa-caret-down"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown"
                                                     x-placement="bottom-end"
                                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">

                                                    <a class="dropdown-item" href="{{route('v2.profile')}}">
                                                        Profile
                                                    </a>

                                                    <a class="dropdown-item" href="https://designcrm.net/logout"
                                                       onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();"
                                                    >
                                                        Sign out
                                                    </a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>

                                                </div>
                                            </div>
                                        </div>


                                        <div class="serach">
                                            <input class="form-control mr-sm-2" type="search" placeholder="Search"
                                                   aria-label="Search">
                                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i
                                                    class="fa-solid fa-magnifying-glass"></i></button>
                                        </div>

                                        <div class="notification">
                                            <a href="javascript:;"><i class="fa-solid fa-bell"></i></a>
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
            {{--side menu--}}
            <div class="side-menu">

                <div class="search-container">
                    <input type="text" class="form-control search-input"
                           placeholder="Search...">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <ul style="max-height: 85%; overflow-y: scroll; scrollbar-width: none; -ms-overflow-style: none;">
                    @if(in_array($user_role_id, [2, 6, 4, 0]))
                        <li>
                            <a href="{{route('v2.dashboard')}}" class="{{ request()->routeIs('v2.dashboard') ? 'active' : '' }}">
                                Dashboard
                            </a>
                        </li>
                    @endif

{{--                    @if(in_array($user_role_id, [2]))--}}
{{--                        <li>--}}
{{--                            <a href="#" class="">--}}
{{--                                Brands dashboard--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}

                    @if(in_array($user_role_id, [2, 6]))
                        <li>
                            <a href="{{route('v2.revenue')}}" class="{{ request()->routeIs('v2.revenue') ? 'active' : '' }}">
                                Revenue
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2]))
                        <li>
                            <a href="{{route('v2.merchants')}}" class="{{ request()->routeIs('v2.merchants') || request()->routeIs('v2.merchants.create') ? 'active' : '' }}">
                                Merchants
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2,6]))
                        <li>
                            <a href="{{ route('v2.messages') }}" class="{{ request()->routeIs('v2.messages') ? 'active' : '' }}">
                                Messages

{{--                                &nbsp;--}}
{{--                                <small>--}}
{{--                                    <span class="badge badge-pill badge-sm bg-warning">--}}
{{--                                        <i class="fas fa-triangle-exclamation"></i>--}}
{{--                                        &nbsp;--}}
{{--                                        Under Construction--}}
{{--                                    </span>--}}
{{--                                </small>--}}
{{--                                Author:--}}
{{--                                YetiBoy--}}
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 4, 0]))
                        <li>
                            <a href="{{route('v2.clients')}}" class="{{ request()->routeIs('v2.clients') || request()->routeIs('v2.clients.create') || request()->routeIs('v2.clients.edit') || request()->routeIs('v2.clients.show') ? 'active' : '' }}">
                                Clients
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 4, 0]))
                        <li>
                            <a href="{{route('v2.invoices')}}" class="{{ request()->routeIs('v2.invoices') || request()->routeIs('v2.invoices.create') || request()->routeIs('v2.invoices.edit') || request()->routeIs('v2.invoices.show') ? 'active' : '' }}">
                                Invoices
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6]))
                        <li>
                            <a href="{{route('v2.invoices.refund.cb')}}" class="{{ request()->routeIs('v2.invoices.refund.cb') ? 'active' : '' }}">
                                Refund/CB
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6]))
                        <li>
                            <a href="{{route('v2.invoices.sales.sheet')}}" class="{{ request()->routeIs('v2.invoices.sales.sheet') ? 'active' : '' }}">
                                Sales sheet
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6]))
                        <li>
                            <a href="{{route('v2.invoices.admin.invoices')}}" class="{{ request()->routeIs('v2.invoices.admin.invoices') ? 'active' : '' }}">
                                Admin invoices
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 0]))
                        <li>
                            <a href="{{route('v2.leads')}}" class="{{ request()->routeIs('v2.leads') || request()->routeIs('v2.leads.create') || request()->routeIs('v2.leads.edit') || request()->routeIs('v2.leads.show') ? 'active' : '' }}">
                                Leads
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 4]) && !user_is_cs())
                        <li>
                            <a href="{{route('v2.briefs.pending')}}" class="{{ request()->routeIs('v2.briefs.pending') ? 'active' : '' }}">
                                Briefs pending
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 4]) && !user_is_cs())
                        <li>
                            <a href="{{route('v2.pending.projects')}}" class="{{ request()->routeIs('v2.pending.projects') ? 'active' : '' }}">
                                Pending projects
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 4]))
                        <li>
                            <a href="{{route('v2.projects')}}" class="{{ request()->routeIs('v2.projects') ? 'active' : '' }}">
                                Projects
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6, 4]))
                        <li>
                            <a href="{{route('v2.tasks')}}" class="{{ request()->routeIs('v2.tasks') || request()->routeIs('v2.tasks.create') || request()->routeIs('v2.tasks.edit') || request()->routeIs('v2.tasks.show') ? 'active' : '' }}">
                                Tasks
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2]))
                        <li>
                            <a href="{{route('v2.services')}}" class="{{ request()->routeIs('v2.services') || request()->routeIs('v2.services.create') || request()->routeIs('v2.services.edit') || request()->routeIs('v2.services.show') ? 'active' : '' }}">
                                Services
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2]))
                        <li>
                            <a href="{{route('v2.brands')}}" class="{{ request()->routeIs('v2.brands') || request()->routeIs('v2.brands.create') || request()->routeIs('v2.brands.edit') || request()->routeIs('v2.brands.show') ? 'active' : '' }}">
                                Brands
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2]))
                        <li>
                            <a href="{{route('v2.users.production')}}" class="{{ request()->routeIs('v2.users.production') || request()->routeIs('v2.users.production.create') || request()->routeIs('v2.users.production.edit') || request()->routeIs('v2.users.production.show') ? 'active' : '' }}">
                                Production
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2, 6]))
                        <li>
                            <a href="{{route('v2.users.sales')}}" class="{{ request()->routeIs('v2.users.sales') || request()->routeIs('v2.users.sales.create') || request()->routeIs('v2.users.sales.edit') || request()->routeIs('v2.users.sales.show') ? 'active' : '' }}">
                                Sale Agent
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2]))
                        <li>
                            <a href="{{route('v2.users.qa')}}" class="{{ request()->routeIs('v2.users.qa') || request()->routeIs('v2.users.qa.create') || request()->routeIs('v2.users.qa.edit') || request()->routeIs('v2.users.qa.show') ? 'active' : '' }}">
                                QA
                            </a>
                        </li>
                    @endif

                    @if(in_array($user_role_id, [2]))
                        <li>
{{--                            <a href="{{route('v2.users.qa')}}" class="{{ request()->routeIs('v2.users.qa') || request()->routeIs('v2.users.qa.create') || request()->routeIs('v2.users.qa.edit') || request()->routeIs('v2.users.qa.show') ? 'active' : '' }}">--}}
                            <a href="#" class="">
                                Tutorials
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{--main content--}}
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

{{--        <script type="text/javascript">--}}
{{--            const progress = document.querySelector('.progress-done');--}}

{{--            progress.style.width = progress.getAttribute('data-done') + '%';--}}
{{--            progress.style.opacity = 1;--}}


{{--        </script>--}}

        @yield('script')
    </body>

</html>
