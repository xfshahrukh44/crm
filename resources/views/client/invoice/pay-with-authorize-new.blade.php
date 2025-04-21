<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
          integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('css/invoice-style.css')}}">
    <link rel="stylesheet" href="{{asset('css/invoice-responsive.css')}}">
    <link href="{{ asset('newglobal/css/toastr.css') }}" rel="stylesheet" />

    <style>
        script#ze-snippet {
            opacity: 0 !important;
        }
    </style>

    <title>Checkout invoice</title>
</head>

<body>


<header>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <nav class="navbar">
                    <a class="navbar-brand"><img src="{{asset('images/crm-logo.png')}}" class="img-fluid" alt=""></a>
                    <form class="d-flex side-profile">
{{--                        <div class="profile-dropdown">--}}
{{--                            <a href="javascript:;" class="profile_dropdown"><img src="{{asset('images/profile.png')}}" class="img-fluid" alt="">--}}
{{--                                Jason Martin --}}
{{--                                <i class="fa-solid fa-caret-down"></i>--}}
{{--                            </a>--}}
{{--                            <ul class="profile_menu">--}}
{{--                                <li><a href="#">Lorem lipsum</a></li>--}}
{{--                                <li><a href="#">Lorem lipsum</a></li>--}}
{{--                                <li><a href="#">Lorem lipsum</a></li>--}}
{{--                            </ul>--}}
{{--                        </div>--}}
{{--                        <div class="search-here">--}}
{{--                            <input class="form-control me-2 " type="search" placeholder="Search here..."--}}
{{--                                   aria-label="Search">--}}
{{--                            <i class="fa-solid fa-magnifying-glass"></i>--}}
{{--                        </div>--}}
{{--                        <div class="notification-show">--}}
{{--                            <a href="#"><img src={{asset('images/notification.png')}}"" alt=""></a>--}}
{{--                        </div>--}}
                    </form>
                </nav>
            </div>
        </div>
    </div>
</header>


<section class="checkout-pg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="invoice-record">
                    <form action="{{route('client.pay.with.authorize.submit', $invoice->id)}}" method="POST" class="invoice-form">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                @if($invoice->payment_status == 2)
                                    <div class="row">
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
                                    </div>
                                @else
                                    <div class="main-invoice">
                                        <div class="summry-form">
                                            <div class="summry-field">
                                                @php
                                                    $brand = \App\Models\Brand::find($invoice->brand);
                                                @endphp
                                                <div class="col-12">
                                                    <div class="writer-invoice">
                                                        <div class="writer-logo">

                                                            <img src="{{asset($brand->logo)}}" class="img-fluid" alt="">
                                                        </div>
                                                        <div class="invoice-number">
                                                            <p>Invoice # {{$invoice->invoice_number}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="fill-summry">
                                                        <label>Summary</label>
                                                        <input type="text" class="form-control" placeholder="">
                                                    </div>
                                                    <div class="pakages-summry">
                                                        <ul class="custom-pakages">
                                                            <li>
                                                                <p>Package </p>
                                                                <span>{{ $invoice->package == 0 ? $invoice->custom_package : $invoice->package }}</span>
                                                            </li>
                                                            <li>
                                                                <p>Brand </p>
                                                                <span>{{$brand->name}}</span>
                                                            </li>
                                                            @php
                                                                $service_list = explode(',', $invoice->service);
                                                            @endphp
                                                            <li>
                                                                <p>Service(s)</p>
                                                                @for($i = 0; $i < count($service_list); $i++)
                                                                    <span>
                                                                        <button class="btn book-btn-market">
                                                                            {{ $invoice->services($service_list[$i])->name }}
                                                                        </button>
                                                                    </span>
                                                                @endfor
                                                            </li>
                                                        </ul>
                                                        <ul class="custom-pakages">
                                                            <li>
                                                                <p>Payment type </p>
                                                                <span>{{ $invoice->payment_type_show() }}</span>
                                                            </li>
                                                            <li>
                                                                <p>Amount</p>
                                                                <span>{{$invoice->currency_show->sign}} {{ $invoice->amount }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="card-payment">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label>Card number</label>
                                                                <input type="text" class="form-control" name="card_number" maxlength="16" required>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-12">
                                                                <label>Expiration month</label>
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
                                                            <div class="col-lg-4 col-md-4 col-12">
                                                                <label>Expiration year</label>
                                                                <input type="number" class="form-control" name="exp_year" min="{{\Carbon\Carbon::now()->format('Y')}}" value="{{\Carbon\Carbon::now()->format('Y')}}" required>
                                                            </div>
                                                            <div class="col-lg-2 col-md-2 col-12">
                                                                <label>CVV</label>
                                                                <input type="number" name="cvv" class="form-control" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="billings-info">
                                            <h3>Billing address</h3>
                                            <div class="card-payment">
                                                <div class="row">
{{--                                                        <div class="col-12">--}}
{{--                                                            <label>Expiration month</label>--}}
{{--                                                            <select name="exp_month" id="" class="form-control">--}}
{{--                                                                <option value="">Select month</option>--}}
{{--                                                                <option value="">1</option>--}}
{{--                                                                <option value="">2</option>--}}
{{--                                                                <option value="">3</option>--}}
{{--                                                                <option value="">4</option>--}}
{{--                                                                <option value="">5</option>--}}
{{--                                                                <option value="">6</option>--}}
{{--                                                                <option value="">7</option>--}}
{{--                                                                <option value="">8</option>--}}
{{--                                                                <option value="">9</option>--}}
{{--                                                                <option value="">10</option>--}}
{{--                                                                <option value="">11</option>--}}
{{--                                                                <option value="">12</option>--}}
{{--                                                            </select>--}}
{{--                                                        </div>--}}
                                                    <div class="col-lg-12 col-md-12 col-12">
                                                        <label>Street address</label>
                                                        <input class="form-control" type="text" name="address" required>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>Country</label>
                                                        <select name="country" id="country" class="form-control" required>
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
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>City</label>
                                                        <input type="text" name="city" class="form-control" required>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12" id="state_wrapper">
                                                        <label for="state">State (code)</label>
                                                        <input class="form-control" type="text" name="state" id="state" required>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>Zip Code</label>
                                                        <input type="text" name="zip" class="form-control">
                                                    </div>
                                                    <div class="billing-pay-btn">
                                                        <button type="submit" class="btn pay-btn" id="button_submit">{{$invoice->currency_show->sign}}{{ $invoice->amount }} Pay</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Optional JavaScript; choose one of the two! -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>


<script src="{{asset('js/invoice-script.js')}}"></script>
<script src="{{ asset('newglobal/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('newglobal/js/bootstrap.bundle.min.js') }}"></script>\
<script src="{{ asset('newglobal/js/toastr.min.js') }}"></script>

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
    // $('.select2').select2();

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
            // $('#state').select2();
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
            // $('#state').select2();
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

@php
    $brand_ids = \Illuminate\Support\Facades\DB::table('brand_users')->where('user_id', 33)->pluck('brand_id')->toArray();
@endphp
@if(in_array($invoice->brand, $brand_ids))
    <!--Start of Zendesk Widget script -->
    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=d2fd00f2-a024-4856-b629-6a518c082760"></script>
    <!--End of Zendesk Widget script -->
@endif

</body>

</html>
