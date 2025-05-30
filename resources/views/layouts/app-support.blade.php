<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="ztvhppa4266dY22ykOfFHz9Q7KMt_Mth3-UI6VWWwcU" />
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('global/img/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('global/img/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('global/img/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('global/img/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('global/img/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('global/img/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('global/img/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('global/img/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('global/img/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('global/img/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('global/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('global/img/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('global/img/favicon-16x16.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('global/img/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/lite-purple.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/perfect-scrollbar.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/toastr.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('newglobal/css/sweetalert2.min.css') }}" rel="stylesheet" />
{{--    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.3/dist/sweetalert2.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <link href="{{ asset('global/css/qa.css') }}" rel="stylesheet" /><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
    <style>
        .select2-container .select2-selection--single{
            height: 34px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            color: #444;
            line-height: 31px;
            background-color: #f8f9fa;
        }
        .select2-container--default .select2-selection--single {
            background-color: transparent;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        a.brands-list {
            margin-left: 20px;
        }

        a.brands-list {
            font-size: 18px;
            font-weight: bold;
            color: #0076c2;
        }

        a.brands-list span {
            border-right: 2px solid rgba(102, 51, 153, 0.1);
            padding-right: 10px;
            margin-right: 9px;
        }
        .ui-autocomplete-category {
            font-weight: bold;
            padding: .2em .4em;
            margin: .8em 0 .2em;
            line-height: 2.5;
        }
    </style>
    @livewireStyles
</head>
<body class="text-left">
    <div class="app-admin-wrap layout-sidebar-large">
        @include('inc.support-nav')
        <div class="main-content-wrap sidenav-open d-flex flex-column">
            <div class="main-content">
                @yield('content')
            </div>
            <div class="flex-grow-1"></div>
            <div class="app-footer">
                <div class="footer-bottom border-top pt-3 d-flex flex-column flex-sm-row align-items-center">
                    <span class="flex-grow-1"></span>
                    <div class="d-flex align-items-center">
                        <img class="logo" src="{{ asset('global/img/sidebarlogo.png') }}" alt="">
                        <div>
                            <p class="m-0">&copy; <?php echo date("Y"); ?> {{ config('app.name') }}</p>
                            <p class="m-0">All rights reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('newglobal/js/jquery-3.3.1.min.js') }}"></script>
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
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <script>
        $(document).ready(() => {
            $('.unread_notification_nav').on('click', function (e) {
                e.preventDefault();
                $(this).removeClass('unread_notification');

                $.ajax({
                    url: "{{route('clear-notification')}}",
                    method: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        notification_id: $(this).data('id')
                    },
                    success: (data) => {
                        console.log(data);
                        window.location.href = $(this).prop('href');
                    },
                });
            });
        });
    </script>

    <script>
        @if(session()->has('success'))
        toastr.success("{{session()->get('success')}}");
        @endif
        @if(session()->has('error'))
        toastr.error("{{session()->get('error')}}");
        @endif
    </script>
    <script>
        $(document).ready(() => {
            //global vars
            let auth_id = parseInt('{{auth()->user()->id}}');

            // Enable Pusher logging - don't include this in production
            Pusher.logToConsole = true;

            var pusher = new Pusher('7d1bc788fe2aaa7a2ea5', {
                cluster: 'ap2'
            });

            var channel = pusher.subscribe('message-channel');
            channel.bind('new-message', function(data) {
                if (data.for_ids && data.for_ids.includes(auth_id)) {
                    swal({
                        icon: 'info',
                        title: data.text,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: "Open messages",
                    }).then((result) => {
                        if (result && data.redirect_url) {
                            window.location.href = data.redirect_url
                        }
                    });
                }
                // alert(JSON.stringify(data));
                console.log(data);
            });
        });
    </script>
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
        $('#search-bar').on('input', function () {
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
                url: '{{route("fetch-search-bar-content")}}',
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
            if ($(this).data('type') == 'Brands') {
                let redirect_url = "{{route('redirect-to-livewire', ['page' => 'temp'])}}";
                redirect_url = redirect_url.replaceAll('temp', 'brands_detail-'+$(this).data('id'));
                window.location.href = redirect_url;
            } else if ($(this).data('type') == 'Clients') {
                let redirect_url = "{{route('redirect-to-livewire', ['page' => 'temp'])}}";
                redirect_url = redirect_url.replaceAll('temp', 'clients_detail-'+$(this).data('id'));
                window.location.href = redirect_url;
            }
        });

        $('body').on('keyup', '.ui-menu-item', function (e) {
            if ($(this).data('type') == 'Brands') {
                let redirect_url = "{{route('redirect-to-livewire', ['page' => 'temp'])}}";
                redirect_url = redirect_url.replaceAll('temp', 'brands_detail-'+$(this).data('id'));
                window.location.href = redirect_url;
            } else if ($(this).data('type') == 'Clients') {
                let redirect_url = "{{route('redirect-to-livewire', ['page' => 'temp'])}}";
                redirect_url = redirect_url.replaceAll('temp', 'clients_detail-'+$(this).data('id'));
                window.location.href = redirect_url;
            }
        });
    </script>
    @yield('script')

    @stack('scripts')
    @if(session()->has('success'))
    <script>
        var timerInterval;
        swal({
            type: 'success',
            title: 'Success!',
            text: "{{ session()->get('success') }}",
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-lg btn-success',
            timer: 2000
        });
    </script>
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
    <script>
        if($('#zero_configuration_table').length != 0){
            $('#zero_configuration_table').DataTable({
                order: [[0, "desc"]],
                responsive: true,
            });
        }

        if($('.select2').length != 0){
            $('.select2').select2();
        }
    </script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        setInterval(() => {
            $.ajax({
                type:'POST',
                url:"{{ url('keep-alive') }}",
                success:function(data){
                    if(data.ok == false){
                        document.getElementById('logout-form').submit();
                    }
                }
            });
        }, 1200000)
    </script>
    @if(\Illuminate\Support\Facades\Route::is('brands.dashboard.v3'))
        @livewireScripts
        @include('livewire.scripts.listeners')
    @endif
</body>
</html>
