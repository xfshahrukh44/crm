@extends('layouts.app-manager')
@section('title', 'Sales Sheet')
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Sales Sheet</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

{{--<div class="row mb-4">--}}
{{--    <div class="col-md-12">--}}
{{--        <div class="card text-left">--}}
{{--            <div class="card-body">--}}
{{--                <form action="{{ route('manager.invoice') }}" method="GET">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-3 form-group mb-3">--}}
{{--                            <label for="package">Search Package</label>--}}
{{--                            <input type="text" class="form-control" id="package" name="package" value="{{ Request::get('package') }}">--}}
{{--                        </div>--}}
{{--                        <div class="col-md-3 form-group mb-3">--}}
{{--                            <label for="invoice">Search Invoice#</label>--}}
{{--                            <input type="text" class="form-control" id="invoice" name="invoice" value="{{ Request::get('invoice') }}">--}}
{{--                        </div>--}}
{{--                        <div class="col-md-3 form-group mb-3">--}}
{{--                            <label for="user">Search Name or Email</label>--}}
{{--                            <input type="text" class="form-control" id="user" name="user" value="{{ Request::get('user') }}">--}}
{{--                        </div>--}}
{{--                        <div class="col-md-3 form-group mb-3">--}}
{{--                            <label for="status">Select Status</label>--}}
{{--                            <select class="form-control select2" name="status" id="status">--}}
{{--                                <option value="0" {{ Request::get('status') == 0 ? 'selected' : '' }}>Any</option>--}}
{{--                                <option value="2" {{ Request::get('status') == 2 ? 'selected' : '' }}>Paid</option>--}}
{{--                                <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>Unpaid</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-12">--}}
{{--                            <div class="text-right">--}}
{{--                                <button class="btn btn-primary">Search Result</button>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="row">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">Sales Sheet</h4>
                <div class="table-responsive">
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
                            @foreach($data as $datas)
                            <tr>
                                <td>
                                    <span class="btn btn-sm btn-dark">{{ $datas->id }}</span>
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
                    {{ $data->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
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
