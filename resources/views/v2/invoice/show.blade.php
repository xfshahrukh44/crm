@extends('v2.layouts.app')

@section('title', 'Invoice Detail')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="invoice-listed">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="invoice-porfile-detail">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="invoice-info">
                                                <div class="issued-invoice">
                                                    <div class="invoice-show">
                                                        <h5>Invoice #{{$invoice->invoice_number}}</h5>
                                                        <p>Issued on {{\Carbon\Carbon::parse($invoice->created_at)->format('jS F Y')}}</p>
                                                    </div>
                                                    <div class="view-invoice">
                                                        <img src="{{asset('v2/images/view-invoice.png')}}" class="img-fluid" alt="">
                                                        <img src="{{asset('v2/images/invoice-det.png')}}" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                                <div class="invoice-summary">
                                                    <h5>
                                                        Summary
                                                    </h5>
                                                </div>
                                                <div class="summry-info">
                                                    <ul>
                                                        <li><span>To</span>
                                                            <p>{{$invoice->client->name . ' ' . $invoice->client->last_name}} <span>{{$invoice->client->email}}</span></p>
                                                        </li>
                                                        <li><span>From</span>
                                                            <p>{{$invoice->brands->name ?? ''}} <span>{{$invoice->brands->email ?? ''}}</span></p>
                                                        </li>
{{--                                                        <li><span>Notes</span>--}}
{{--                                                            <h6>Thank you for your business</h6>--}}
{{--                                                        </li>--}}
                                                    </ul>
                                                </div>

                                                @php
                                                    $service_names = [];
                                                    $invoice_service_ids = explode(',', $invoice->service);
                                                    foreach (($invoice_service_ids) as $invoice_service_id) {
                                                        if ($service = \App\Models\Service::find($invoice_service_id)) {
                                                            $service_names []= $service->name;
                                                        }
                                                    }
                                                @endphp
                                                <div class="cost-down">
                                                    <h6>Services</h6>
                                                    <ul class="pb-2">
                                                        @if(count($service_names))
                                                            @foreach($service_names as $service_name)
                                                                <li>
                                                                    <h5>{{$service_name}}</h5>
                                                                </li>
                                                            @endforeach
                                                            <li>
                                                                <h5>Total Services <span>${{number_format($invoice->amount)}}</span></h5>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="paid-invoice">
                                                <div class="total-ammount">
                                                    <h4>${{number_format($invoice->amount)}}</h4>
{{--                                                    <div class="payment-dropdown">--}}
{{--                                                        <select name="" id="">--}}
{{--                                                            <option class="paid" value="paid">✅ Paid</option>--}}
{{--                                                            <option class="unpaid" value="unpaid">❌ Unpaid</option>--}}
{{--                                                        </select>--}}
{{--                                                    </div>--}}
                                                </div>


                                                <div class="invoice-detail-chart">
                                                    <div class="details-info">
                                                        <ul>
                                                            <li><img src="{{asset('v2/images/invoice-profile.png')}}" class="img-fluid" alt="">
                                                                <h6>{{($invoice->client->name ?? '') . ' ' . ($invoice->client->last_name ?? '')}}</h6>
                                                            </li>
                                                            <li><img src="{{asset('v2/images/date-profile.png')}}" class="img-fluid" alt="">
                                                                <h6>{{\Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}</h6>
                                                            </li>
                                                            @if(count($service_names))
                                                                @foreach($service_names as $service_name)
                                                                    <li><img src="{{asset('v2/images/coin.png')}}" class="img-fluid" alt="">
                                                                        <h6>{{$service_name}}</h6>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </div>
                                                    <div class="invoice-created">
                                                        <ul>
                                                            <li>
                                                                <p>Invoice created</p> <span>{{\Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}</span>
                                                            </li>
                                                            <li>
                                                                <p>Invoice Sent</p><span>{{\Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}</span>
                                                            </li>
                                                            @if($invoice->payment_status == '2')
                                                                <li>
                                                                    <p>Invoice Paid</p> <span>{{\Carbon\Carbon::parse($invoice->updated_at)->format('d F Y')}}</span>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                        @if($invoice->payment_status == '1')
                                                            <div class="invoice-button">
                                                                <button type="submit" class="btn invoice-paid"><img
                                                                        src="{{asset('images/error-icon.png')}}" class="img-fluid"
                                                                        alt="">Invoice Unpaid</button>
                                                            </div>
                                                        @elseif($invoice->payment_status == '2')
                                                            <div class="invoice-button">
                                                                <button type="submit" class="btn invoice-paid"><img
                                                                        src="{{asset('images/cheaq-btn.png')}}" class="img-fluid"
                                                                        alt="">Invoice Paid</button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="invoice-listed">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="invoice-porfile-detail">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="invoice-info">
                                                <div class="issued-invoice">
                                                    <div class="invoice-show">
                                                        <h5>Invoice 2024</h5>
                                                        <p>Issued on 24th November 2024</p>
                                                    </div>
                                                    <div class="view-invoice">
                                                        <img src="{{asset('v2/images/view-invoice.png')}}" class="img-fluid" alt="">
                                                        <img src="{{asset('v2/images/invoice-det.png')}}" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                                <div class="invoice-summary">
                                                    <h5>
                                                        Summary
                                                    </h5>
                                                </div>
                                                <div class="summry-info">
                                                    <ul>
                                                        <li><span>To</span>
                                                            <p>Malik Babar <span>Malikbabar@technifiedlabs.com</span></p>
                                                        </li>
                                                        <li><span>From</span>
                                                            <p>Shahid Hussain <span>Shahidhussain@technifiedlabs.com</span></p>
                                                        </li>
                                                        <li><span>Notes</span>
                                                            <h6>Thank you for your business</h6>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="cost-down">
                                                    <h6>Cost Breakdown</h6>
                                                    <ul>
                                                        <li>
                                                            <h5>Design Landing Page <span>$13,600.00</span></h5>
                                                        </li>
                                                        <li>
                                                            <h5>Development Landing Page <span>$2,500.00</span></h5>
                                                        </li>
                                                        <li>
                                                            <h5>Testing & Improvements <span>$550.00</span></h5>
                                                        </li>
                                                        <li>
                                                            <h5>Design Landing Page <span>$4,680.00</span></h5>
                                                            <h5 class="discountli">Discount (10%) <span>$465.00</span></h5>
                                                        </li>
                                                        <li>
                                                            <h5>Amount due <span>$4,185.00</span></h5>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="paid-invoice">
                                                <div class="total-ammount">
                                                    <h5>$4,185.00</h5>
                                                    <div class="payment-dropdown">
                                                        <select name="" id="">
                                                            <option class="paid" value="paid">✅ Paid</option>
                                                            <option class="unpaid" value="unpaid">❌ Unpaid</option>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="invoice-detail-chart">
                                                    <div class="details-info">
                                                        <ul>
                                                            <li><img src="{{asset('v2/images/invoice-profile.png')}}" class="img-fluid" alt="">
                                                                <h6>Malik Babar</h6>
                                                            </li>
                                                            <li><img src="{{asset('v2/images/date-profile.png')}}" class="img-fluid" alt="">
                                                                <h6>08 Nov 2024</h6>
                                                            </li>
                                                            <li><img src="{{asset('v2/images/coin.png')}}" class="img-fluid" alt="">
                                                                <h6>Pay by check or bank transfer</h6>
                                                            </li>
                                                            <li><img src="{{asset('v2/images/landing.png')}}" class="img-fluid" alt="">
                                                                <h6>Landing page design</h6>
                                                            </li>
                                                            <li><img src="{{asset('v2/images/link-icon.png')}}" class="img-fluid" alt="">
                                                                <h6>https://dribbble.com/shots/19265438-Invoocy-Fintech-Dashboard
                                                                </h6>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="invoice-created">
                                                        <ul>
                                                            <li>
                                                                <p>Invoice created</p> <span>08 Jun 2024</span>
                                                            </li>
                                                            <li>
                                                                <p>Invoice Sent</p><span>10 Jun 2024</span>
                                                            </li>
                                                            <li>
                                                                <p>Invoice Paid</p> <span>11 Jun 2024</span>
                                                            </li>
                                                        </ul>
                                                        <div class="invoice-button">
                                                            <button type="submit" class="btn invoice-paid"><img
                                                                    src="{{asset('v2/images/invoice-button-check.png')}}" class="img-fluid"
                                                                    alt="">Invoice
                                                                Paid</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
        @endswitch
    </div>
@endsection

@section('script')

@endsection
