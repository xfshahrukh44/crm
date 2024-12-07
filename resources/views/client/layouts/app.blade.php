<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">

    <title>Design crm | @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- fonts  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
          integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/responsive.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/toastr.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/sweetalert2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .introjs-tooltip-header {
            padding: 0px;
        }

        .introjs-tooltiptext {
            padding-top: 0px;
            padding-bottom: 0px;
            font-size: 18px;
        }

        .introjs-bullets {
            padding: 0px;
        }

        a.introjs-button {
            padding: 2%;
        }
    </style>

    @yield('css')

</head>

<body>

<header>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid p-0">
                        <a class="navbar-brand" href="index.php">
                            <img src="{{asset('images/sidebarlogo.png')}}" class="img-fluid" alt="">
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center custom-menu">
                                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
                                @if(session()->has('coming-from-admin'))
                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
                                    <span>
                                        <a href="{{route('admin.back_to_admin')}}" class="btn btn-info">
                                            <i class="fas fa-arrow-left"></i>
                                            Back to admin
                                        </a>
                                    </span>
                                @endif
                                <li class="nav-item" data-step='2'>
                                    <a class="nav-link" aria-current="page" href="{{route('client.fetch.messages')}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#0076c2"
                                             class="bi bi-chat-left" viewBox="0 0 16 16">
                                            <path
                                                d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                        </svg>
                                        <span>
                                            Message
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item" data-step='1'>
                                    <a class="nav-link" href="{{route('client.brief')}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#0076c2"
                                             class="bi bi-card-text" viewBox="0 0 16 16">
                                            <path
                                                d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                            <path
                                                d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
                                        </svg>
                                        <span>
                                            Brief
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('client.invoice')}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#0076c2"
                                             class="bi bi-credit-card" viewBox="0 0 16 16">
                                            <path
                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                                            <path
                                                d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                                        </svg>
                                        <span>
                                            Invoices
                                        </span>
                                    </a>
                                </li>
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link" href="login.php">--}}
{{--                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#0076c2"--}}
{{--                                             class="bi bi-box-arrow-right" viewBox="0 0 16 16">--}}
{{--                                            <path fill-rule="evenodd"--}}
{{--                                                  d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>--}}
{{--                                            <path fill-rule="evenodd"--}}
{{--                                                  d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>--}}
{{--                                        </svg>--}}
{{--                                        <span>--}}
{{--                                            Logout--}}
{{--                                        </span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
                            </ul>
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                                <div class="profile-detail">
                                    <a href="#">
                                        <img src="{{asset(auth()->user()->image ?? 'images/avatar.png')}}" class="img-fluid" alt="">
                                    </a>
                                </div>
                                <li class="nav-item dropdown profile-drop-down">
                                    <a class="nav-link dropdown-toggle" href="setup-profile.php" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        {{auth()->user()->name . ' ' . auth()->user()->last_name}}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Profile</a></li>
{{--                                        <li><a class="dropdown-item" href="#">Another action</a></li>--}}
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="#"
                                               onclick="event.preventDefault();
                                               document.getElementById('logout-form').submit();"
                                            >Logout</a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <form class="d-flex" role="search">
                                <div class="parent-search-bar">
                                    <input class="form-control me-2" type="search" placeholder="Search"
                                           aria-label="Search">
                                    <button>
                                        <img src="{{asset('images/search-bar.png')}}" class="img-fluid" alt="">
                                    </button>
                                </div>

                            </form>
                            <button class="bell-icon" type="submit">
                                <img src="{{asset('images/bell-icon.png')}}" class="img-fluid" alt="">
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>


    @yield('content')


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
<script>
    var selector = '.main-id-date p';

    $(selector).on('click', function () {
        $(selector).removeClass('active');
        $(this).addClass('active');
    });
</script>

<script>
    // Wait for the DOM to load
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('redirectButton');

        if (button) {
            button.addEventListener('click', function () {
                const currentPath = window.location.pathname;

                // Check if we're on 'index.php' (or root page)
                if (currentPath === '/index.php' || currentPath === '/' || currentPath.endsWith('/index.php')) {
                    // Redirect to 'messages.php'
                    window.location.href = "messages.php";
                }
                // Check if we're on 'messages.php'
                else if (currentPath === '/messages.php' || currentPath.endsWith('/messages.php')) {
                    // Redirect to 'index.php'
                    window.location.href = "index.php";
                }
            });
        }
    });
</script>


</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>

<script>
    const rand = () =>
        Array.from({length: 10}, () => Math.floor(Math.random() * 100));

    // let data = rand();
    const checkingData = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, .0, 0.0];
    const savingsData = [300.27, 500.27, 150.27, 430.27, 170.27, 287.27, 100.27, 287.27, 500.27, 245.27];

    // function addData(chart, data) {
    //   chart.data.datasets.forEach(dataset => {
    //     let data = dataset.data;
    //     const first = data.shift();
    //     data.push(first);
    //     dataset.data = data;
    //   });

    //   chart.update();
    // }


</script>

{{--<script src="{{ asset('newglobal/js/jquery-3.3.1.min.js') }}"></script>--}}
<script src="{{ asset('newglobal/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('newglobal/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('newglobal/js/script.min.js') }}"></script>
<script src="{{ asset('newglobal/js/sidebar.large.script.min.js') }}"></script>
<script src="{{ asset('newglobal/js/echarts.min.js') }}"></script>
<script src="{{ asset('newglobal/js/echart.options.min.js') }}"></script>
<script src="{{ asset('newglobal/js/datatables.min.js') }}"></script>
<script src="{{ asset('newglobal/js/toastr.min.js') }}"></script>
<script src="{{ asset('newglobal/js/select2.min.js') }}"></script>
<script src="{{ asset('newglobal/js/Chart.min.js') }}"></script>
<script src="{{ asset('newglobal/js/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    introJs().setOption("dontShowAgain", true).start();
</script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    $(document).ready(() => {
        //global vars
        let auth_id = parseInt('{{auth()->user()->id}}');

        // Enable Pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('7d1bc788fe2aaa7a2ea5', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('message-channel-for-client-user-' + auth_id.toString());
        channel.bind('new-message', function(data) {
            swal({
                icon: 'info',
                title: data.text,
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonText: "View message",
            }).then((result) => {
                if (result && data.redirect_url) {
                    window.location.href = data.redirect_url
                }
            });

            console.log(data);
        });
    });
</script>
@yield('script')

{{--@stack('scripts')--}}
@if(session()->has('success'))
    <script>
        var timerInterval;
        swal({
            type: 'success',
            title: 'Success!',
            text: '{{ session()->get("success") }}',
            timer: 2000,
            showCancelButton: false,
            showConfirmButton: false
        });
    </script>
    <!-- <script>
        toastr.success("", "{{ session()->get('success') }}", {
            timeOut: "50000"
        });
    </script> -->
@endif
<script>
    @if(count($errors) > 0)
    @foreach($errors->all() as $error)
    toastr.error("{{ $error }}", {
        timeOut: "50000"
    });
    @endforeach
    @endif
</script>
