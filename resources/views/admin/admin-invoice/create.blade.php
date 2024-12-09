@extends('layouts.app-admin')
@section('content')
<div class="breadcrumb">
    <h1>Create Admin Invoice</h1>
    <ul>
        <li><a href="#">Admin Invoice</a></li>
        <li>Create Admin Invoice</li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Admin Invoice Form</div>
                <form class="form" action="{{route('admin.admin-invoice.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 form-group mb-3">
                            <label for="client_id">Client ID</label>
                            <input type="number" id="client_id" class="form-control" value="{{old('client_id')}}" placeholder="Client ID" name="client_id" required="required" min="1" step="1">
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="client_name">Client Name</label>
                            <input type="text" id="client_name" class="form-control" value="{{old('client_name')}}" placeholder="Client Name" name="client_name" required="required">
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="client_email">Client Email</label>
                            <input type="email" id="client_email" class="form-control" value="{{old('client_email')}}" placeholder="Client Email" name="client_email" required="required">
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="client_phone">Client Phone</label>
                            <input type="email" id="client_phone" class="form-control" value="{{old('client_phone')}}" placeholder="Client Phone" name="client_phone" required="required">
                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="brand_id">Brand</label>
                            <select name="brand_id" id="brand_id" class="form-control select2" required>
                                <option value="">Select Brand</option>
                                @foreach(\App\Models\Brand::where('status', 1)->get() as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }} - {{ $brand->url }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="service">Service</label>
                            <select name="service[]" id="service" class="form-control select2" required multiple="multiple">
                                @foreach(\App\Models\Service::all() as $service)
                                    <option value="{{$service->id}}">{{$service->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="department">Department</label>
                            <input type="text" id="department" class="form-control" value="{{old('department')}}" placeholder="Department" name="department" required="required">
                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="currency">Currency<span>*</span></label>
                            <select name="currency" id="currency" class="form-control select2" required>
                                <option value="">Select Currency</option>
                                @foreach(\App\Models\Currency::all() as $currency)
                                    <option value="{{$currency->id}}">{{$currency->name}} - {{$currency->short_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="amount">Amount</label>
                            <input value="0.00" step=".01" type="number" id="amount" class="form-control" placeholder="Amount" name="amount" required min="0">
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control select2">
                                <option value="Card" selected>Card</option>
                                <option value="Wire">Wire</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="sale_upsell">Sale/Upsell</label>
                            <select name="sale_upsell" id="sale_upsell" class="form-control select2">
                                <option value="Sale" selected>Sale</option>
                                <option value="Upsell">Upsell</option>
                            </select>
                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="merchant_id">Merchant</label>
                            <select name="merchant_id" id="merchant_id" class="form-control" required>
                                <!-- <option value="">Select Merchant</option> -->
                                @foreach(get_my_merchants() as $merchant)
                                    <option value="{{ $merchant->id }}" selected>{{ $merchant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_id">Payment ID</label>
                            <input type="text" id="payment_id" class="form-control" value="{{old('payment_id')}}" placeholder="Department" name="payment_id" required="required">
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="invoice_number">Invoice Number</label>
                            <input type="text" id="invoice_number" class="form-control" value="{{old('invoice_number')}}" placeholder="Department" name="invoice_number" required="required">
                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="sales_agent_id">Salesperson</label>
                            <select name="sales_agent_id" id="sales_agent_id" class="form-control select2">
                                <option value="">Select sales agent</option>
                                @foreach(\App\Models\User::whereIn('is_employee', [0, 4, 6])->get() as $sale_agent)
                                    <option value="{{ $sale_agent->id }}">{{$sale_agent->name}} {{$sale_agent->last_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="transfer_by_id">Transfer By</label>
                            <select name="transfer_by_id" id="transfer_by_id" class="form-control select2">
                                <option value="">Select sales agent</option>
                                @foreach(\App\Models\User::whereIn('is_employee', [0, 4, 6])->get() as $sale_agent)
                                    <option value="{{ $sale_agent->id }}">{{$sale_agent->name}} {{$sale_agent->last_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="recurring">Recurring</label>
                            <input value="0.00" step=".01" type="number" id="recurring" class="form-control" placeholder="Amount" name="recurring" required min="0">
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="refund_cb">Refund/CB</label>
                            <input value="0.00" step=".01" type="number" id="refund_cb" class="form-control" placeholder="Refund/CB" name="refund_cb" required min="0">
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="refund_cb_date">Refund/CB Date</label>
                            <input type="date" id="refund_cb_date" class="form-control" placeholder="Refund/CB Date" name="refund_cb_date" required min="0">
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Save Admin Invoice</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            $('#password').val(generatePassword());
        });
        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }
        function copyToClipboard() {
            var copyText = document.getElementById("password");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            M.toast({
                html: 'Password Copied'
            })
            // alert("Copied the text: " + copyText.value);
        }
        $('#contact').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
                event.preventDefault(); //stop character from entering input
            }
        });
    </script>
@endpush
