<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
<!-- <title>{{ config('app.name', 'Kamay Backoffice') }}</title> -->
    <title>{{ config('app.name') }} - @yield('title')</title>
    <!-- Scripts -->
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        span.number {
            width: 15px;
            height: 15px;
            display: inline-block;
            font-size: 10px;
            border-radius: 50px;
            color: white;
            position: absolute;
            top: 22px;
            font-weight: bold;
            right: 35px;
            color: #7f231c;
            background-color: #fdd9d7;
            border-color: #fccac7;
        }

        .layout-sidebar-large .sidebar-left .navigation-left .nav-item .nav-item-hold {
            position: relative;
        }

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
</head>

<body class="text-left">
    <div class="app-admin-wrap layout-sidebar-large">
        <div class="main-content-wrap sidenav-open d-flex flex-column">
            <div class="main-content">
                <!-- Main Content -->
                <div class="breadcrumb">
                    <h1>Pay invoice - {{ $invoice->name }} </h1>
                </div>

                <div class="main-content">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            @php
                                $brand = \App\Models\Brand::find($invoice->brand);
                            @endphp
                            <img src="{{asset($brand->logo)}}" alt="">
                        </div>
                        <div class="col-md-12">
                            <ul class="nav nav-tabs justify-content-end mb-4" id="myTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="invoice-tab" data-toggle="tab" href="#invoice" role="tab" aria-controls="invoice" aria-selected="true">Invoice</a></li>
                            </ul>
                            <div class="card">
                                <div class="tab-content" id="myTabContent" style="padding-top: 100px; padding-bottom: 200px;">
                                    <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-3">
                                                <div class="row">
                                                    <div class="col-md-12 form-group text-center">
                                                        <h2>Invoice # {{$invoice->invoice_number}}</h2>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label for=""><h5>Package</h5></label>
                                                        <h6>{{ $invoice->package == 0 ? $invoice->custom_package : $invoice->package }}</h6>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label for=""><h5>Brand</h5></label>
                                                        <h6>{{$brand->name}}</h6>
                                                    </div>
                                                    @php
                                                        $service_list = explode(',', $invoice->service);
                                                    @endphp
                                                    <div class="col-md-4 form-group">
                                                        <label for=""><h5>Service(s)</h5></label>
                                                        <br>
                                                        @for($i = 0; $i < count($service_list); $i++)
                                                            <span class="btn btn-info btn-sm">{{ $invoice->services($service_list[$i])->name }}</span>
                                                        @endfor
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label for=""><h5>Payment type</h5></label>
                                                        <h6>{{ $invoice->payment_type_show() }}</h6>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label for=""><h5>Amount</h5></label>
                                                        <h6>{{$invoice->currency_show->sign}} {{ $invoice->amount }}</h6>
                                                    </div>
                                                </div>

                                                <form action="{{route('client.pay.with.authorize.submit', $invoice->id)}}" method="POST">
                                                    @csrf
                                                    <div class="row mt-4">
                                                        <div class="col-md-12 form-group">
                                                            <label for="">Card number</label>
                                                            <input class="form-control" type="text" name="card_number" required>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label for="">Expiration month</label>
                                                            <select class="form-control" name="exp_month" id="" required>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                                <option value="6">6</option>
                                                                <option value="7">7</option>
                                                                <option value="8">8</option>
                                                                <option value="9">9</option>
                                                                <option value="10">10</option>
                                                                <option value="11">11</option>
                                                                <option value="12">12</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label for="">Expiration year</label>
                                                            <input class="form-control" type="number" name="exp_year" min="{{\Carbon\Carbon::now()->format('Y')}}" value="{{\Carbon\Carbon::now()->format('Y')}}" required>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label for="">CVV</label>
                                                            <input class="form-control" type="number" name="cvv" required>
                                                        </div>
                                                        <div class="col-md-12 form-group mt-2">
                                                            <button type="submit" class="btn btn-primary btn-block">
                                                                Pay {{$invoice->currency_show->sign}}{{ $invoice->amount }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end of main-content -->
                </div>
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
</body>
