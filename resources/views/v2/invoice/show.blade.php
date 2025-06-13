@extends('v2.layouts.app')

@section('title', 'Invoice Detail')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
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
                                                    <li>
                                                        <a class="badge bg-warning btn_copy_authorize_link text-dark" data-url="{{route('client.pay.with.authorize', $invoice->id)}}" style="cursor: pointer;">
                                                            <i class="fas fa-copy"></i>
                                                            Payment link
                                                        </a>
                                                    </li>
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
    </div>
@endsection

@section('script')
    <script>
        function copy_authorize_link (url) {
            // Create a temporary textarea to hold the link
            var tempInput = document.createElement("textarea");
            tempInput.value = url; // Assign the link to the textarea
            document.body.appendChild(tempInput); // Append textarea to body (temporarily)

            tempInput.select(); // Select the text
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy"); // Copy to clipboard

            document.body.removeChild(tempInput); // Remove the temporary textarea

            toastr.success('Link copied to clipboard!');
        }

        $(document).ready(function () {
            //copy link
            $('.btn_copy_authorize_link').on('click', function () {
                copy_authorize_link($(this).data('url'));
            });
        });
    </script>
@endsection
