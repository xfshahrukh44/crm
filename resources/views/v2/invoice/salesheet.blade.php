@extends('v2.layouts.app')

@section('title', 'Sales Sheet')

@section('css')
    <style>
        #zero_configuration_table td {
            word-break: break-all;
            max-width: 300px; /* adjust as needed */
            white-space: normal;
        }

        /*#zero_configuration_table th,*/
        /*#zero_configuration_table td {*/
        /*    vertical-align: middle;*/
        /*}*/
    </style>

    <style>
        .client-actions-box {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 100;
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="list-0f">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-0f-head for-invoice-listing table-responsive">
                            <div class="row text-left pr-3 pb-2">
                                <div class="col-md-12 m-auto d-flex justify-content-start pt-2">
                                    <h1 style="font-weight: 100;">Sales Sheet</h1>
                                </div>
                                {{--                                        <div class="col-md-6 m-auto d-flex justify-content-end">--}}
                                {{--                                            <a href="{{route('v2.invoices.create')}}" class="btn btn-sm btn-success">--}}
                                {{--                                                <i class="fas fa-plus"></i>--}}
                                {{--                                                Create--}}
                                {{--                                            </a>--}}
                                {{--                                        </div>--}}
                            </div>

                            <br>

                            <form class="search-invoice" action="{{route('v2.invoices.sales.sheet')}}" method="GET">
                                <select name="brand" id="brand" class="select2">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                                <select name="agent" id="agent" class="select2">
                                    <option value="">Select agent</option>
                                    @foreach($sale_agents as $agent)
                                        <option value="{{$agent->id}}" {{ request()->get('agent') ==  $agent->id ? 'selected' : ' '}}>{{$agent->name . ' ' . $agent->last_name}}</option>
                                    @endforeach
                                </select>
                                <select name="merchant" id="merchant" class="select2">
                                    <option value="">Select merchant</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{$merchant->id}}" {{ request()->get('merchant') ==  $merchant->id ? 'selected' : ' '}}>{{$merchant->name . ' ' . $merchant->last_name}}</option>
                                    @endforeach
                                </select>
                                <select name="month" id="month" class="select2">
                                    <option value="">Select month</option>
                                    <option value="1" {{ request()->get('month') ==  '1' ? 'selected' : ' '}}>January</option>
                                    <option value="2" {{ request()->get('month') ==  '2' ? 'selected' : ' '}}>February</option>
                                    <option value="3" {{ request()->get('month') ==  '3' ? 'selected' : ' '}}>March</option>
                                    <option value="4" {{ request()->get('month') ==  '4' ? 'selected' : ' '}}>April</option>
                                    <option value="5" {{ request()->get('month') ==  '5' ? 'selected' : ' '}}>May</option>
                                    <option value="6" {{ request()->get('month') ==  '6' ? 'selected' : ' '}}>June</option>
                                    <option value="7" {{ request()->get('month') ==  '7' ? 'selected' : ' '}}>July</option>
                                    <option value="8" {{ request()->get('month') ==  '8' ? 'selected' : ' '}}>August</option>
                                    <option value="9" {{ request()->get('month') ==  '9' ? 'selected' : ' '}}>September</option>
                                    <option value="10" {{ request()->get('month') ==  '10' ? 'selected' : ' '}}>October</option>
                                    <option value="11" {{ request()->get('month') ==  '11' ? 'selected' : ' '}}>November</option>
                                    <option value="12" {{ request()->get('month') ==  '12' ? 'selected' : ' '}}>December</option>
                                </select>
                                <select name="year" id="year" class="select2">
                                    <option value="">Select year</option>
                                    <option value="2019" {{ request()->get('year') ==  '2019' ? 'selected' : ' '}}>2019</option>
                                    <option value="2020" {{ request()->get('year') ==  '2020' ? 'selected' : ' '}}>2020</option>
                                    <option value="2021" {{ request()->get('year') ==  '2021' ? 'selected' : ' '}}>2021</option>
                                    <option value="2022" {{ request()->get('year') ==  '2022' ? 'selected' : ' '}}>2022</option>
                                    <option value="2023" {{ request()->get('year') ==  '2023' ? 'selected' : ' '}}>2023</option>
                                    <option value="2024" {{ request()->get('year') ==  '2024' ? 'selected' : ' '}}>2024</option>
                                    <option value="2025" {{ request()->get('year') ==  '2025' ? 'selected' : ' '}}>2025</option>
                                </select>

                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>

                            <table id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Client ID</th>
                                <th>Client Name</th>
                                <th>Contact</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Recurring</th>
                                <th>Date</th>
                                <th>Agent</th>
                                <th>Sale/Upsell</th>
                                <th>Brand</th>
                                <th>Merchant</th>
                                <th>Refund/CB</th>
                                <th>Refund/CB Date</th>

                                </thead>
                                <tbody>
                                <div class="row m-auto">
                                    <div class="col-4 text-center text-primary">
                                        Amount: ${{number_format($amount)}}
                                    </div>
                                    <div class="col-4 text-center text-danger">
                                        Refund/CB: ${{number_format($refund)}}
                                    </div>
                                    <div class="col-4 text-center text-success">
                                        Net: ${{number_format($net)}}
                                    </div>
                                </div>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            {{ $invoice->id }}
                                        </td>
                                        <td>
                                            {{ $invoice->client?->id }}
                                        </td>
                                        <td>
                                            {{ $invoice->client?->name ?? '' }} {{ $invoice->client?->last_name ?? '' }}
                                            <br>
                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark p-2 text-white btn_click_to_view">
                                                <i class="fas fa-eye"></i>
                                                View email
                                            </a>
                                            <span class="content_click_to_view" hidden>
                                                            {{ $invoice->client?->email }}
                                                        </span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark p-2 text-white btn_click_to_view">
                                                <i class="fas fa-eye"></i>
                                                View phone
                                            </a>
                                            <span class="content_click_to_view" hidden>
                                                            {{ $invoice->client?->contact }}
                                                        </span>
                                        </td>
                                        <td>
                                            @php
                                                $service_list = explode(',', $invoice->service);
                                            @endphp
                                            @for($i = 0; $i < count($service_list); $i++)
                                                @if($service_list[$i])
                                                    @php
                                                        $service_list_name = '';
                                                        $var_check = $invoice->services($service_list[$i]);
                                                        $words = $var_check ? explode(" ", $var_check->name) : [];
                                                    @endphp
                                                    @for($j = 0; $j < count($words); $j++)
                                                        @php
                                                            $service_list_name .= $words[$j][0];
                                                        @endphp
                                                    @endfor
                                                    <span class="badge badge-info badge-sm p-2 mb-1">{{ $service_list_name }}</span>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>{{ $invoice->currency_show->sign }}{{ $invoice->amount }}</td>
                                        <td>${{ $invoice->recurring }}</td>
                                        <td>
                                            {{ date('d M y', strtotime($invoice->created_at)) }}
                                        </td>
                                        <td>{{ $invoice->sale->name ?? '' }} {{ $invoice->sale->last_name ?? '' }}</td>
                                        <td>{{ $invoice->sale_or_upsell ?? '' }}</td>
                                        <td>
                                                        <span class="badge badge-primary badge-sm p-2">
                                                            {{ $invoice->brands?->name }}
                                                        </span>
                                        </td>
                                        @php
                                            $merchant = \App\Models\Merchant::find($invoice->merchant_id);
                                        @endphp
                                        <td>{{ $merchant->name ?? '' }}</td>
                                        <td class="text-danger">${{$invoice->refunded_cb ?? '0.00'}}</td>
                                        <td class="text-danger">{{!is_null($invoice->refund_cb_date) ? \Carbon\Carbon::parse($invoice->refund_cb_date)->format('d M y') : 'N/A'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $invoices->appends(request()->query())->links() }}
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {

        });
    </script>
@endsection
