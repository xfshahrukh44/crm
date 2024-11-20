@extends('layouts.app-manager')
@section('title', 'Sales Sheet')
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Sales Sheet</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('manager.sales.sheet') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 form-group mb-3">
                            <label for="brand">Brands</label>
                            <select class="form-control select2" name="brand" id="brand">
                                @php
                                    $get_brands = \App\Models\Brand::whereIn('id', auth()->user()->brand_list())->get();
                                @endphp
                                <option value="">Select brand</option>
                                @foreach($get_brands as $brand)
                                    <option value="{{$brand->id}}" {!! request()->get('brand') == $brand->id ? 'selected' : '' !!}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="agent">Agents</label>
                            <select class="form-control select2" name="agent" id="agent">
                                @php
                                    $agent_ids = \Illuminate\Support\Facades\DB::table('brand_users')
                                                    ->whereIn('brand_id', auth()->user()->brand_list())
                                                    ->pluck('user_id');
                                    $agents = \App\Models\User::whereIn('is_employee', [4, 6])->whereIn('id', $agent_ids)->get();
                                @endphp
                                <option value="">Select agent</option>
                                @foreach($agents as $agent)
                                    <option value="{{$agent->id}}" {!! request()->get('agent') == $agent->id ? 'selected' : '' !!}>{{ $agent->name . ' ' . $agent->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="month">Month</label>
                            <select class="form-control select2" name="month" id="month">
                                <option value="">Select month</option>
                                <option value="1" {!! request()->get('month') == '1' ? 'selected' : '' !!}>January</option>
                                <option value="2" {!! request()->get('month') == '2' ? 'selected' : '' !!}>February</option>
                                <option value="3" {!! request()->get('month') == '3' ? 'selected' : '' !!}>March</option>
                                <option value="4" {!! request()->get('month') == '4' ? 'selected' : '' !!}>April</option>
                                <option value="5" {!! request()->get('month') == '5' ? 'selected' : '' !!}>May</option>
                                <option value="6" {!! request()->get('month') == '6' ? 'selected' : '' !!}>June</option>
                                <option value="7" {!! request()->get('month') == '7' ? 'selected' : '' !!}>July</option>
                                <option value="8" {!! request()->get('month') == '8' ? 'selected' : '' !!}>August</option>
                                <option value="9" {!! request()->get('month') == '9' ? 'selected' : '' !!}>September</option>
                                <option value="10" {!! request()->get('month') == '10' ? 'selected' : '' !!}>October</option>
                                <option value="11" {!! request()->get('month') == '11' ? 'selected' : '' !!}>November</option>
                                <option value="12" {!! request()->get('month') == '12' ? 'selected' : '' !!}>December</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="merchant">Merchants</label>
                            <select class="form-control select2" name="merchant" id="merchant">
                                @php
                                    $get_merchants = \App\Models\Merchant::get();
                                @endphp
                                <option value="">Select merchant</option>
                                @foreach($get_merchants as $merchant)
                                    <option value="{{$merchant->id}}" {!! request()->get('merchant') == $merchant->id ? 'selected' : '' !!}>{{ $merchant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div class="text-right">
                                <button class="btn btn-primary">Search Result</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                @if(request()->has('month'))
                    <h4 class="card-title mb-3">Sales Sheet for {{get_month_name(request()->get('month'))}}</h4>
                @endif
                <div class="table-responsive">
                    <div class="row pt-2" style="border: 1px solid #e6e6e6;">
                        <div class="col text-center">
                            <h6 class="text-info">Amount: ${{$amount}}</h6>
                        </div>
                        <div class="col text-center">
                            <h6 class="text-danger">Refund/CB: ${{$refund}}</h6>
                        </div>
                        <div class="col text-center">
                            <h6 class="text-success">Net: ${{$net}}</h6>
                        </div>
                    </div>
                    <table class="display table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SR No</th>
                                <th>Client ID</th>
                                <th>Client Name</th>
                                <th>Email</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Recurring</th>
                                <th>Date</th>
                                <th>Salesperson</th>
                                <th>Sale/Upsell</th>
                                <th>Brand</th>
                                <th>Merchant</th>
                                <th>Invoice number</th>
                                <th>Refund/CB</th>
                                <th>Refund/CB Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 0;
                            @endphp
                            @foreach($data as $datas)
                                @php
                                    $key += 1;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="btn btn-sm btn-dark">{{ $key }}</span>
                                    </td>
                                    <td>{{$datas->client->id}}</td>
                                    <td>{{$datas->client->name}}</td>
                                    <td>{{$datas->client->email}}</td>
                                    <td>
                                        @php
                                            $service_list = explode(',', $datas->service);
                                        @endphp
                                        @for($i = 0; $i < count($service_list); $i++)
                                            @if($service_list[$i])
                                                @php
                                                    $service_list_name = '';
                                                    $var_check = $datas->services($service_list[$i]);
                                                    $words = $var_check ? explode(" ", $var_check->name) : [];
                                                @endphp
                                                @for($j = 0; $j < count($words); $j++)
                                                    @php
                                                        $service_list_name .= ' ' . $words[$j];
                                                    @endphp
                                                @endfor
                                                <span class="btn btn-info btn-sm mb-1">{{ $service_list_name }}</span>
                                            @endif
                                        @endfor
                                    </td>
                                    <td>{{ $datas->currency_show->sign }}{{ $datas->amount }}</td>
                                    <td>
                                        @if(!is_null($datas->recurring))
                                            {{ $datas->currency_show->sign }}{{ $datas->recurring }}
                                        @endif
                                    </td><td>
                                        <button class="btn btn-sm btn-secondary mb-1">{{ date('g:i a', strtotime($datas->created_at)) }}</button>
                                        <button class="btn btn-sm btn-secondary">{{ date('d M, Y', strtotime($datas->created_at)) }}</button>
                                    </td>
                                    <td>{{ $datas->sale->name ?? '' }} {{ $datas->sale->last_name ?? '' }}</td>
                                    <td>{{ $datas->sale->sale_or_upsell ?? '' }}</td>
                                    <td><span class="btn btn-primary btn-sm">{{ $datas->brands->name }}</span></td>
                                    @php
                                        $merchant = \App\Models\Merchant::find($datas->merchant_id);
                                    @endphp
                                    <td>{{ $merchant->name ?? '' }}</td>
                                    <td>
                                        <span class="btn btn-sm btn-dark">#{{ $datas->invoice_number }}</span>
                                    </td>
                                    <td class="text-danger">
                                        @if(!is_null($datas->refunded_cb))
                                            {{ $datas->currency_show->sign }}{{ $datas->refunded_cb }}
                                        @endif
                                    </td>
                                    <td class="text-danger">
                                        @if(!is_null($datas->refund_cb_date))
                                            {{\Carbon\Carbon::parse($datas->refund_cb_date)->format('d F, Y')}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
{{--                    {{ $data->links("pagination::bootstrap-4") }}--}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('form select').on('change', function () {
            $(this).parent().parent().parent().submit();
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
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

    $('.auth-create').on('click', function () {
        var auth = $(this).data('auth');
        var id = $(this).data('id');
        var pass = generatePassword();
        if(auth == 0){
            swal({
                title: "Enter Password",
                input: "text",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Enter Password",
                inputValue: pass
                }).then(function (inputValue) {
                if (inputValue === false){
                    return swal({
                        title:"Field cannot be empty",
                        text: "Password Not Inserted/Updated because it is Empty",
                        type:"danger"
                    })
                }
                if (inputValue === "") {
                    return swal({
                        title:"Field cannot be empty",
                        text: "Password Not Inserted/Updated because it is Empty",
                        type:"danger"
                    })
                }
                $.ajax({
                    type:'POST',
                    url: "{{ route('manager.client.createauth') }}",
                    data: {id: id, pass:inputValue},
                    success:function(data) {
                        if(data.success == true){
                            swal("Auth Created", "Password is : " + inputValue, "success");
                        }else{
                            return swal({
                                title:"Error",
                                text: "There is an Error, Plase Contact Administrator",
                                type:"danger"
                            })
                        }
                    }
                });
            });
        }else{
            swal({
                title: "Enter Password",
                input: "text",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Enter Password",
                inputValue: pass
                }).then(function (inputValue) {
                if (inputValue === false){
                    return swal({
                        title:"Field cannot be empty",
                        text: "Password Not Inserted/Updated because it is Empty",
                        type:"danger"
                    })
                }
                if (inputValue === "") {
                    return swal({
                        title:"Field cannot be empty",
                        text: "Password Not Inserted/Updated because it is Empty",
                        type:"danger"
                    })
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type:'POST',
                    url: "{{ route('manager.client.updateauth') }}",
                    data: {id: id, pass:inputValue},
                    success:function(data) {
                        if(data.success == true){
                            swal("Auth Created", "Password is : " + inputValue, "success");
                        }else{
                            return swal({
                                title:"Error",
                                text: "There is an Error, Plase Contact Administrator",
                                type:"danger"
                            })
                        }
                    }
                });
            });
        }
    });
</script>
@endpush
