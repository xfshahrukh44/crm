@extends('client.layouts.app')
@section('title', 'Invoices')

@section('css')

@endsection

@section('content')
    <section class="invoice-listing invoice">
        <div class="container bg-colored">
            <div class="row align-items-start invoice-listing-select-bar">
                <div class="col-lg-7">
                    <div class="invoice-header">
                        <div class="left-invoice-header">
                            <h2 class="heading-3">
                                Invoice #{{$invoice->invoice_number}}
                            </h2>
                            <p>
                                Issued on {{\Carbon\Carbon::parse($invoice->created_at)->format('jS F Y')}}
                            </p>
                        </div>
                        <div class="right-invoice-header">
                            <button>
                                <img src="{{asset('images/eye.png')}}" class="img-fluid" alt="">
                            </button>
                            <button>
                                <img src="{{asset('images/document-downloa.png')}}" class="img-fluid" alt="">
                            </button>
                        </div>
                    </div>
                    <div class="summary">
                        <h3 class="heading-4">
                            Summary
                        </h3>

                        <table class="table mail-table border-0">
                            <thead>
                            <tr class="">
                                <th scope="col">To</th>
                                <th scope="col">{{($invoice->client->name ?? '') . ' ' . ($invoice->client->last_name ?? '')}}</th>
                                <th scope="col">{{$invoice->client->email ?? ''}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row">From</th>
                                <td>{{$invoice->brands->name ?? ''}}</td>
                                <td>{{$invoice->brands->email ?? ''}}</td>
                            </tr>
{{--                            <tr>--}}
{{--                                <th scope="row">Notes</th>--}}
{{--                                <td colspan="2"><button class="btn pay-now">Thank you for your business</button></td>--}}
{{--                            </tr>--}}
                            </tbody>
                        </table>
                    </div>

                    <div class="border">

                    </div>

                    @php
                        $service_names = [];
                        $invoice_service_ids = explode(',', $invoice->service);
                        foreach ($invoice_service_ids as $invoice_service_id) {
                            if ($service = \App\Models\Service::find($invoice_service_id)) {
                                $service_names []= $service->name;
                            }
                        }
                    @endphp
                    @if(count($service_names))
                        <div class="cost-break-down-table">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr class="colored-table-row">
                                        <th scope="col">Services</th>
{{--                                        <th scope="col"></th>--}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($service_names as $service_name)
                                        <tr>
                                            <td>{{$service_name}}</td>
{{--                                            <td class="text-end">$13,600.00</td>--}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="col-lg-5">

                    <div class="paid">
                        <p> <span>{{$invoice->currency_show->sign}} {{ $invoice->amount }}</span></p>
                        <div class="paid-unpaid-drop-down">
                        <span class="tick-img">
                            @if($invoice->payment_status == '1')
                                <img src="{{asset('images/error-icon.png')}}" style="max-width: 14px;" alt="" class="img-fluid">
                            @elseif($invoice->payment_status == '2')
                                <img src="{{asset('images/tick-icon.png')}}" alt="" class="img-fluid">
                            @endif
                        </span>
                            <select class="form-select" aria-label=".form-select-lg example" readonly disabled>
                                <option {!! $invoice->payment_status == '2' ? 'selected' : '' !!}>Paid</option>
                                <option {!! $invoice->payment_status == '1' ? 'selected' : '' !!}>Unpaid</option>
                            </select>
                        </div>
                    </div>

                    <div class="main-id-card">

                        <div class="main-id-user">
                            <p><span class="icon"><i class="fa-regular fa-user"></i></span> <span class="text">{{($invoice->client->name ?? '') . ' ' . ($invoice->client->last_name ?? '')}}</span></p>
                            <p><span class="icon"><i class="fa-regular fa-calendar-days"></i></span> <span class="text">{{\Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}</span></p>
                            <p><span class="icon"><i class="fa-solid fa-dollar-sign"></i></span> <span class="text">{{$invoice->merchant->name ?? 'Wire'}}</span></p>
                            @if(count($service_names))
                                <p><span class="icon"><i class="fa-regular fa-folder"></i></span> <span class="text">{{implode(', ', $service_names) . '.'}}</span></p>
                            @endif
                            @if($invoice->is_authorize)
                                <p><span class="icon"><img src="{{asset('images/link-img.png')}}" class="img-fluid" alt=""></span>
                                    <span class="text">{{route('client.pay.with.authorize', $invoice->id)}}</span>
                                </p>
                            @endif
                        </div>

                        <div class="main-id-date">
                            <p>
                                <span class="detail"><i class="fa-regular fa-circle"></i> Invoice created</span>
                                <span class="date">{{\Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}</span>
                            </p>
                            <p class="main-border">
                                <span class="detail"><i class="fa-regular fa-circle"></i> Invoice Sent</span>
                                <span class="date">{{\Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}</span>
                            </p>
                            @if($invoice->payment_status == '2')
                                <p class="active">
                                    <span class="detail"><i class="fa-regular fa-circle"></i> Invoice Paid</span>
                                    <span class="date">{{\Carbon\Carbon::parse($invoice->updated_at)->format('d F Y')}}</span>
                                </p>
                            @endif
                        </div>


                        @if($invoice->payment_status == '1')
                            <div class="main-id-btn">
                                <button type="submit" class="btn submit-btn">
                                    <img src="{{asset('images/error-icon.png')}}" alt="" class="img-fluid" style="max-width: 20px;">
                                    Invoice Unpaid
                                </button>
                            </div>
                        @elseif($invoice->payment_status == '2')
                            <div class="main-id-btn">
                                <button type="submit" class="btn submit-btn">
                                    <img src="{{asset('images/cheaq-btn.png')}}" alt="" class="img-fluid">
                                    Invoice Paid
                                </button>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')

@endsection
