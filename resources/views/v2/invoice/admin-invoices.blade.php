@extends('v2.layouts.app')

@section('title', 'Admin Invoices')

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
                                    <h1 style="font-weight: 100;">Admin Invoices</h1>
                                </div>
                            </div>

                            <br>

                            <form class="search-invoice" action="{{route('v2.invoices.admin.invoices')}}" method="GET">
                                <select name="brand_name" id="brand_name" class="select2">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->name}}" {{ request()->get('brand_name') ==  $brand->name ? 'selected' : ' '}}>{{$brand->name}}</option>
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

                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>

                                @if(v2_acl([2]))
                                    <a href="#" class="btn bg-success btn-block" id="btn_import">
                                        <i class="fas fa-file-excel"></i>
                                        IMPORT
                                    </a>
                                    <span id="error-message" style="color: red; display: none;">Only .xlsx files are allowed!</span>
                                @endif
                            </form>

                            <form id="form_import" action="{{route('admin.admin-invoice.import')}}" method="POST" id="form_import" enctype="multipart/form-data" hidden>
                                @csrf
                                <input name="file" type="file" id="input_file" accept=".xlsx, .xls">
                            </form>

                            <table class="table-striped" id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Client Id</th>
                                <th>Client Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Recurring</th>
                                <th>Date</th>
                                <th>Saleperson</th>
                                <th>Sale/Upsell</th>
                                <th>Transfer By</th>
                                <th>Department</th>
                                <th>Brand</th>
                                <th>Type</th>
                                <th>Merchant</th>
                                <th>Payment Id</th>
                                <th>Invoice No#</th>
                                <th>Refunded/CB</th>
                                <th>Refund/ CB Date</th>

                                </thead>
                                <tbody>
                                @foreach($admin_invoices as $admin_invoice)
                                    <tr>
                                        <td>{{$admin_invoice->sr_no}}</td>
                                        <td>{{$admin_invoice->client_id ?? 'N/A'}}</td>
                                        <td>{{$admin_invoice->client_name ?? 'N/A'}}</td>
                                        <td>{{$admin_invoice->client_email ?? 'N/A'}}</td>
                                        <td>{{$admin_invoice->client_phone ?? 'N/A'}}</td>
                                        <td>{{$admin_invoice->service_name ?? 'N/A'}}</td>
                                        <td>{{ $admin_invoice->currency_show->sign }}{{ $admin_invoice->amount }}</td>
                                        <td>{{ $admin_invoice->currency_show->sign }}{{ $admin_invoice->recurring }}</td>
                                        <td>
                                            <a class="badge badge-sm badge-secondary p-2 mb-1">{{ date('g:i a', strtotime($admin_invoice->date)) }}</a>
                                            <a class="badge badge-sm badge-secondary p-2">{{ date('d M, Y', strtotime($admin_invoice->date)) }}</a>
                                        </td>
                                        <td>{{ $admin_invoice->sales_person_name ?? '' }}</td>
                                        <td>{{$admin_invoice->sale_upsell ?? 'N/A'}}</td>
                                        <td>{{ $admin_invoice->transfer_by_name ?? '' }}</td>
                                        <td>{{$admin_invoice->department ?? 'N/A'}}</td>
                                        <td>{{ $admin_invoice->brand_name ?? 'N/A' }}</td>
                                        <td>{{$admin_invoice->type ?? 'N/A'}}</td>
                                        <td>{{ $admin_invoice->merchant_name ?? 'N/A' }}</td>
                                        <td>{{$admin_invoice->payment_id ?? 'N/A'}}</td>
                                        <td><span class="btn btn-primary btn-sm">#{{ $admin_invoice->invoice_number }}</span></td>
                                        <td>{{ $admin_invoice->currency_show->sign }}{{ $admin_invoice->refund_cb }}</td>
                                        <td>
                                            <a class="badge badge-sm badge-secondary p-2 mb-1">{{ date('g:i a', strtotime($admin_invoice->refund_cb_date)) }}</a>
                                            <a class="badge badge-sm badge-secondary p-2">{{ date('d M, Y', strtotime($admin_invoice->refund_cb_date)) }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $admin_invoices->appends(request()->query())->links() }}
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
            $('#btn_import').on('click', function () {
                $('#input_file').click();
            });

            $('#input_file').on('change', function () {
                const fileInput = $(this)[0];
                const file = fileInput.files[0];
                const errorMessage = $('#error-message');

                if (file) {
                    const fileName = file.name;
                    const extension = fileName.split('.').pop().toLowerCase();

                    if (extension !== 'xlsx') {
                        errorMessage.show();
                        $(this).val(''); // Clear the input
                    } else {
                        errorMessage.hide();
                        $('#form_import').submit();
                    }
                }
            });
        });
    </script>
@endsection
