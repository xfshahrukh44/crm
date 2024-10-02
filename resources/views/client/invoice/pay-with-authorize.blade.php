@extends('layouts.app-support')
@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush
@section('content')
<!-- Main Content -->
<div class="breadcrumb">
    <h1>Pay invoice - {{ $invoice->name }} </h1>
</div>

<div class="main-content">
    <div class="row">
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

@endsection

@push('scripts')

@endpush