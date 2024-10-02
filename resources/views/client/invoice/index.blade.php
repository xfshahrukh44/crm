@extends('layouts.app-client')
@section('title', 'Invoices')
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Invoices</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('client.invoice') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 form-group mb-3">
                            <label for="package">Search Package</label>
                            <input type="text" class="form-control" id="package" name="package" value="{{ Request::get('package') }}">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="invoice">Search Invoice#</label>
                            <input type="text" class="form-control" id="invoice" name="invoice" value="{{ Request::get('invoice') }}">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="user">Select User</label>
                            <select class="form-control select2" name="user" id="user">
                                <option value="0">Any</option>
                                @foreach(Auth()->user()->getClient as $getClient)
                                <option value="{{ $getClient->id }}" {{ Request::get('user') == $getClient->id ? 'selected' : '' }}>{{ $getClient->name }} {{ $getClient->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="status">Select Status</label>
                            <select class="form-control select2" name="status" id="status">
                                <option value="0" {{ Request::get('status') == 0 ? 'selected' : '' }}>Any</option>
                                <option value="2" {{ Request::get('status') == 2 ? 'selected' : '' }}>Paid</option>
                                <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>Unpaid</option>
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
                <h4 class="card-title mb-3">Invoice Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Package Name</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Payment Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                            <tr>
                                <td>
                                    <span class="btn btn-sm btn-dark">#{{ $datas->invoice_number }}</span>
                                </td>
                                <td>
                                    @if($datas->package == 0)
                                    {{ $datas->custom_package }}
                                    @else
                                    {{ $datas->package }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                    $service_list = explode(',', $datas->service);
                                    @endphp
                                    @for($i = 0; $i < count($service_list); $i++)
                                    <span class="btn btn-info btn-sm mb-1">{{ $datas->services($service_list[$i])->name }}</span>
                                    @endfor
                                </td>
                                <td>
{{--                                    <span class="btn btn-{{ App\Models\Invoice::STATUS_COLOR[$datas->payment_status] }} btn-sm">--}}
                                    <span class="">
                                        {{ App\Models\Invoice::PAYMENT_STATUS[$datas->payment_status] }}
                                    </span>
                                </td>
                                <td>{{ $datas->currency_show->sign }}{{ $datas->amount }}</td>
                                <td>
                                    <div class="d-flex">
                                        @if($datas->payment_status == 1)
                                            @if($datas->stripe_invoice_url)
                                                <a target="_blank" href="{{$datas->stripe_invoice_url}}" class="btn btn-primary btn-icon btn-sm mr-1">
                                                    <span class="ul-btn__text"><b>PAY</b></span>
                                                </a>
                                            @endif
                                            @if($datas->is_authorize)
                                                <a href="{{route('client.pay.with.authorize', $datas->id)}}" class="btn btn-warning btn-icon btn-sm mr-1">
                                                    <span class="ul-btn__text"><b>PAY</b></span>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
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

</script>
@endpush