<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="ztvhppa4266dY22ykOfFHz9Q7KMt_Mth3-UI6VWWwcU" />
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <div class="main-content-wrap d-flex flex-column">
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
                            <img style="max-width: 300px !important; padding: 10px 10px; border-radius: 10px;" src="{{asset($brand->logo)}}" alt="">
                        </div>
                        <div class="col-md-12">
                            @if($invoice->payment_status == 2)
                                <div class="col-md-12 text-center">
                                    <h1>
                                        <i class="fa fa-check-double text-success"></i>
                                    </h1>
                                </div>
                                <div class="col-md-12 text-center">
                                    <h1 class="text-success">
                                        <b>
                                            THANK YOU!
                                        </b>
                                    </h1>
                                </div>
                                <div class="col-md-12 text-center">
                                    <h4>Invoice #{{$invoice->invoice_number}} has been paid.</h4>
                                </div>
                            @else
{{--                                <div class="col-md-12 text-center">--}}
{{--                                    @dump($token)--}}
{{--                                </div>--}}
{{--                                @if($token !== '')--}}
{{--                                    <form id="paymentForm" method="POST" action="https://accept.authorize.net/payment/payment">--}}
{{--                                        <input type="hidden" name="token" value="{{ $token }}">--}}
{{--                                        <button type="submit">Submit</button>--}}
{{--                                    </form>--}}
{{--                                @endif--}}
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

{{--                                                        @if($invoice->discription)--}}
{{--                                                            <div class="col-md-12 form-group">--}}
{{--                                                                <label for=""><h5>Description</h5></label>--}}
{{--                                                                <p>{!! $invoice->discription !!}</p>--}}
{{--                                                            </div>--}}
{{--                                                        @endif--}}
                                                    </div>

                                                    <form action="{{route('client.pay.with.authorize.submit', $invoice->id)}}" method="POST">
                                                        @csrf
                                                        <div class="row mt-4">
                                                            <div class="col-md-12 form-group">
                                                                <label for="">Card number</label>
                                                                <input class="form-control" type="text" name="card_number" maxlength="16" required>
                                                            </div>
                                                            <div class="col-md-3 form-group">
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
                                                            <div class="col-md-3 form-group">
                                                                <label for="">Expiration year</label>
                                                                <input class="form-control" type="number" name="exp_year" min="{{\Carbon\Carbon::now()->format('Y')}}" value="{{\Carbon\Carbon::now()->format('Y')}}" required>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="">CVV</label>
                                                                <input class="form-control" type="number" name="cvv" required>
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <h4>Billing information</h4>
                                                            </div>
                                                            <div class="col-md-12 form-group">
                                                                <label for="address">Street Address</label>
                                                                <input class="form-control" type="text" name="address" required>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="country">Country</label>
                                                                <select class="form-control select2" name="country" id="country" required>
                                                                    <option value="">Select a Country</option>
                                                                    <option value="AF">Afghanistan</option>
                                                                    <option value="AL">Albania</option>
                                                                    <option value="DZ">Algeria</option>
                                                                    <option value="AS">American Samoa</option>
                                                                    <option value="AD">Andorra</option>
                                                                    <option value="AO">Angola</option>
                                                                    <option value="AI">Anguilla</option>
                                                                    <option value="AQ">Antarctica</option>
                                                                    <option value="AG">Antigua and Barbuda</option>
                                                                    <option value="AR">Argentina</option>
                                                                    <option value="AM">Armenia</option>
                                                                    <option value="AU">Australia</option>
                                                                    <option value="AT">Austria</option>
                                                                    <option value="AZ">Azerbaijan</option>
                                                                    <option value="BS">Bahamas</option>
                                                                    <option value="BH">Bahrain</option>
                                                                    <option value="BD">Bangladesh</option>
                                                                    <option value="BB">Barbados</option>
                                                                    <option value="BY">Belarus</option>
                                                                    <option value="BE">Belgium</option>
                                                                    <option value="BZ">Belize</option>
                                                                    <option value="BJ">Benin</option>
                                                                    <option value="BT">Bhutan</option>
                                                                    <option value="BO">Bolivia</option>
                                                                    <option value="BA">Bosnia and Herzegovina</option>
                                                                    <option value="BW">Botswana</option>
                                                                    <option value="BR">Brazil</option>
                                                                    <option value="BN">Brunei</option>
                                                                    <option value="BG">Bulgaria</option>
                                                                    <option value="BF">Burkina Faso</option>
                                                                    <option value="BI">Burundi</option>
                                                                    <option value="KH">Cambodia</option>
                                                                    <option value="CM">Cameroon</option>
                                                                    <option value="CA">Canada</option>
                                                                    <option value="CF">Central African Republic</option>
                                                                    <option value="TD">Chad</option>
                                                                    <option value="CL">Chile</option>
                                                                    <option value="CN">China</option>
                                                                    <option value="CO">Colombia</option>
                                                                    <option value="KM">Comoros</option>
                                                                    <option value="CG">Congo</option>
                                                                    <option value="CR">Costa Rica</option>
                                                                    <option value="CI">Côte d'Ivoire</option>
                                                                    <option value="HR">Croatia</option>
                                                                    <option value="CU">Cuba</option>
                                                                    <option value="CY">Cyprus</option>
                                                                    <option value="CZ">Czech Republic</option>
                                                                    <option value="DK">Denmark</option>
                                                                    <option value="DJ">Djibouti</option>
                                                                    <option value="DM">Dominica</option>
                                                                    <option value="DO">Dominican Republic</option>
                                                                    <option value="EC">Ecuador</option>
                                                                    <option value="EG">Egypt</option>
                                                                    <option value="SV">El Salvador</option>
                                                                    <option value="GQ">Equatorial Guinea</option>
                                                                    <option value="ER">Eritrea</option>
                                                                    <option value="EE">Estonia</option>
                                                                    <option value="ET">Ethiopia</option>
                                                                    <option value="FJ">Fiji</option>
                                                                    <option value="FI">Finland</option>
                                                                    <option value="FR">France</option>
                                                                    <option value="GA">Gabon</option>
                                                                    <option value="GM">Gambia</option>
                                                                    <option value="GE">Georgia</option>
                                                                    <option value="DE">Germany</option>
                                                                    <option value="GH">Ghana</option>
                                                                    <option value="GR">Greece</option>
                                                                    <option value="GT">Guatemala</option>
                                                                    <option value="GN">Guinea</option>
                                                                    <option value="HT">Haiti</option>
                                                                    <option value="HN">Honduras</option>
                                                                    <option value="HU">Hungary</option>
                                                                    <option value="IS">Iceland</option>
                                                                    <option value="IN">India</option>
                                                                    <option value="ID">Indonesia</option>
                                                                    <option value="IR">Iran</option>
                                                                    <option value="IQ">Iraq</option>
                                                                    <option value="IE">Ireland</option>
                                                                    <option value="IT">Italy</option>
                                                                    <option value="JM">Jamaica</option>
                                                                    <option value="JP">Japan</option>
                                                                    <option value="JO">Jordan</option>
                                                                    <option value="KZ">Kazakhstan</option>
                                                                    <option value="KE">Kenya</option>
                                                                    <option value="KW">Kuwait</option>
                                                                    <option value="LA">Laos</option>
                                                                    <option value="LV">Latvia</option>
                                                                    <option value="LB">Lebanon</option>
                                                                    <option value="LY">Libya</option>
                                                                    <option value="LT">Lithuania</option>
                                                                    <option value="LU">Luxembourg</option>
                                                                    <option value="MG">Madagascar</option>
                                                                    <option value="MY">Malaysia</option>
                                                                    <option value="MV">Maldives</option>
                                                                    <option value="ML">Mali</option>
                                                                    <option value="MT">Malta</option>
                                                                    <option value="MX">Mexico</option>
                                                                    <option value="MA">Morocco</option>
                                                                    <option value="MZ">Mozambique</option>
                                                                    <option value="NA">Namibia</option>
                                                                    <option value="NP">Nepal</option>
                                                                    <option value="NL">Netherlands</option>
                                                                    <option value="NZ">New Zealand</option>
                                                                    <option value="NI">Nicaragua</option>
                                                                    <option value="NG">Nigeria</option>
                                                                    <option value="NO">Norway</option>
                                                                    <option value="OM">Oman</option>
                                                                    <option value="PK">Pakistan</option>
                                                                    <option value="PA">Panama</option>
                                                                    <option value="PE">Peru</option>
                                                                    <option value="PH">Philippines</option>
                                                                    <option value="PL">Poland</option>
                                                                    <option value="PT">Portugal</option>
                                                                    <option value="QA">Qatar</option>
                                                                    <option value="RO">Romania</option>
                                                                    <option value="RU">Russia</option>
                                                                    <option value="SA">Saudi Arabia</option>
                                                                    <option value="SN">Senegal</option>
                                                                    <option value="SG">Singapore</option>
                                                                    <option value="ZA">South Africa</option>
                                                                    <option value="ES">Spain</option>
                                                                    <option value="LK">Sri Lanka</option>
                                                                    <option value="SE">Sweden</option>
                                                                    <option value="CH">Switzerland</option>
                                                                    <option value="TH">Thailand</option>
                                                                    <option value="TN">Tunisia</option>
                                                                    <option value="TR">Turkey</option>
                                                                    <option value="UA">Ukraine</option>
                                                                    <option value="AE">United Arab Emirates</option>
                                                                    <option value="GB">United Kingdom</option>
                                                                    <option value="USA">United States</option>
                                                                    <option value="VN">Vietnam</option>
                                                                    <option value="YE">Yemen</option>
                                                                    <option value="ZM">Zambia</option>
                                                                    <option value="ZW">Zimbabwe</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="city">City</label>
                                                                <input class="form-control" type="text" name="city" required>
                                                            </div>
                                                            <div class="col-md-3 form-group" id="state_wrapper">
                                                                <label for="state">State (code)</label>
                                                                <div>
                                                                    <input class="form-control" type="text" name="state" id="state" required>
                                                                </div>
{{--                                                                <div>--}}
{{--                                                                    <select class="form-control" name="state" id="state" required>--}}
{{--                                                                        <option value="">Select a State</option>--}}
{{--                                                                        <option value="AL">Alabama</option>--}}
{{--                                                                        <option value="AK">Alaska</option>--}}
{{--                                                                        <option value="AZ">Arizona</option>--}}
{{--                                                                        <option value="AR">Arkansas</option>--}}
{{--                                                                        <option value="CA">California</option>--}}
{{--                                                                        <option value="CO">Colorado</option>--}}
{{--                                                                        <option value="CT">Connecticut</option>--}}
{{--                                                                        <option value="DE">Delaware</option>--}}
{{--                                                                        <option value="FL">Florida</option>--}}
{{--                                                                        <option value="GA">Georgia</option>--}}
{{--                                                                        <option value="HI">Hawaii</option>--}}
{{--                                                                        <option value="ID">Idaho</option>--}}
{{--                                                                        <option value="IL">Illinois</option>--}}
{{--                                                                        <option value="IN">Indiana</option>--}}
{{--                                                                        <option value="IA">Iowa</option>--}}
{{--                                                                        <option value="KS">Kansas</option>--}}
{{--                                                                        <option value="KY">Kentucky</option>--}}
{{--                                                                        <option value="LA">Louisiana</option>--}}
{{--                                                                        <option value="ME">Maine</option>--}}
{{--                                                                        <option value="MD">Maryland</option>--}}
{{--                                                                        <option value="MA">Massachusetts</option>--}}
{{--                                                                        <option value="MI">Michigan</option>--}}
{{--                                                                        <option value="MN">Minnesota</option>--}}
{{--                                                                        <option value="MS">Mississippi</option>--}}
{{--                                                                        <option value="MO">Missouri</option>--}}
{{--                                                                        <option value="MT">Montana</option>--}}
{{--                                                                        <option value="NE">Nebraska</option>--}}
{{--                                                                        <option value="NV">Nevada</option>--}}
{{--                                                                        <option value="NH">New Hampshire</option>--}}
{{--                                                                        <option value="NJ">New Jersey</option>--}}
{{--                                                                        <option value="NM">New Mexico</option>--}}
{{--                                                                        <option value="NY">New York</option>--}}
{{--                                                                        <option value="NC">North Carolina</option>--}}
{{--                                                                        <option value="ND">North Dakota</option>--}}
{{--                                                                        <option value="OH">Ohio</option>--}}
{{--                                                                        <option value="OK">Oklahoma</option>--}}
{{--                                                                        <option value="OR">Oregon</option>--}}
{{--                                                                        <option value="PA">Pennsylvania</option>--}}
{{--                                                                        <option value="RI">Rhode Island</option>--}}
{{--                                                                        <option value="SC">South Carolina</option>--}}
{{--                                                                        <option value="SD">South Dakota</option>--}}
{{--                                                                        <option value="TN">Tennessee</option>--}}
{{--                                                                        <option value="TX">Texas</option>--}}
{{--                                                                        <option value="UT">Utah</option>--}}
{{--                                                                        <option value="VT">Vermont</option>--}}
{{--                                                                        <option value="VA">Virginia</option>--}}
{{--                                                                        <option value="WA">Washington</option>--}}
{{--                                                                        <option value="WV">West Virginia</option>--}}
{{--                                                                        <option value="WI">Wisconsin</option>--}}
{{--                                                                        <option value="WY">Wyoming</option>--}}
{{--                                                                    </select>--}}
{{--                                                                </div>--}}
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="">Zip</label>
                                                                <input class="form-control" type="text" name="zip" required>
                                                            </div>
                                                            <div class="col-md-12 form-group mt-2">
                                                                <button id="button_submit" type="submit" class="btn btn-primary btn-block">
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
                            @endif
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

    @if($token !== '')
        <script>
            var token = "{{$token}}"; // Get this from the backend
            document.getElementById("paymentFrame").src = "https://accept.authorize.net/payment/payment?token=" + token;
        </script>
    @endif

    <script>
        $('.select2').select2();

        $('#country').on('change', function () {
            if ($(this).val() == 'USA') {
                $('#state_wrapper').html(`<label for="state">State (code)</label>
                                    <div>
                                        <select class="form-control" name="state" id="state" required>
                                            <option value="">Select a State</option>
                                            <option value="AL">Alabama</option>
                                            <option value="AK">Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="DE">Delaware</option>
                                            <option value="DC">District of Columbia</option>
                                            <option value="FL">Florida</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI">Hawaii</option>
                                            <option value="ID">Idaho</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="IA">Iowa</option>
                                            <option value="KS">Kansas</option>
                                            <option value="KY">Kentucky</option>
                                            <option value="LA">Louisiana</option>
                                            <option value="ME">Maine</option>
                                            <option value="MD">Maryland</option>
                                            <option value="MA">Massachusetts</option>
                                            <option value="MI">Michigan</option>
                                            <option value="MN">Minnesota</option>
                                            <option value="MS">Mississippi</option>
                                            <option value="MO">Missouri</option>
                                            <option value="MT">Montana</option>
                                            <option value="NE">Nebraska</option>
                                            <option value="NV">Nevada</option>
                                            <option value="NH">New Hampshire</option>
                                            <option value="NJ">New Jersey</option>
                                            <option value="NM">New Mexico</option>
                                            <option value="NY">New York</option>
                                            <option value="NC">North Carolina</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                            <option value="TX">Texas</option>
                                            <option value="UT">Utah</option>
                                            <option value="VT">Vermont</option>
                                            <option value="VA">Virginia</option>
                                            <option value="WA">Washington</option>
                                            <option value="WV">West Virginia</option>
                                            <option value="WI">Wisconsin</option>
                                            <option value="WY">Wyoming</option>
                                        </select>
                                    </div>`);
                $('#state').select2();
            } else if ($(this).val() == 'CA') {
                $('#state_wrapper').html(`<label for="state">Province/Territory (code)</label>
                            <div>
                                <select class="form-control" name="state" id="state" required>
                                    <option value="">Select a Province/Territory</option>
                                    <option value="AB">Alberta</option>
                                    <option value="BC">British Columbia</option>
                                    <option value="MB">Manitoba</option>
                                    <option value="NB">New Brunswick</option>
                                    <option value="NL">Newfoundland and Labrador</option>
                                    <option value="NS">Nova Scotia</option>
                                    <option value="NT">Northwest Territories</option>
                                    <option value="NU">Nunavut</option>
                                    <option value="ON">Ontario</option>
                                    <option value="PE">Prince Edward Island</option>
                                    <option value="QC">Quebec</option>
                                    <option value="SK">Saskatchewan</option>
                                    <option value="YT">Yukon</option>
                                </select>
                            </div>`);
                $('#state').select2();
            } else {
                $('#state_wrapper').html(`<label for="state">State (code)</label>
                                    <div>
                                        <input class="form-control" type="text" name="state" id="state" required>
                                    </div>`);
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            $('form').on('submit', function () {
                $(this).find('#button_submit').text('Please wait');
                $(this).find('#button_submit').prop('disabled', true);
            });
        });
    </script>
</body>
