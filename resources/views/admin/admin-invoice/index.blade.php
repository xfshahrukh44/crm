@extends('layouts.app-admin')

@section('content')
<div class="breadcrumb row">
    <div class="col-md-10">
        <h1>Admin Invoice List</h1>
        <ul>
            <li><a href="#">Admin Invoice</a></li>
            <li>Admin Invoice List</li>
        </ul>
    </div>
{{--    <div class="col-md-2 text-right">--}}
{{--        <a href="{{ route('admin.admin-invoice.create') }}" class="btn btn-primary btn-block">Create</a>--}}
{{--    </div>--}}
    <div class="col-md-2 text-right">
        <a href="#" class="btn btn-success btn-block" id="btn_import">
            <i class="fas fa-file-excel"></i>
            IMPORT
        </a>
        <span id="error-message" style="color: red; display: none;">Only .xlsx files are allowed!</span>
        <form action="{{route('admin.admin-invoice.import')}}" method="POST" id="form_import" enctype="multipart/form-data" hidden>
            @csrf
            <input name="file" type="file" id="input_file" accept=".xlsx, .xls">
        </form>
    </div>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">Admin Invoice Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered" id="index_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr No</th>
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
                            </tr>
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
                                        <button class="btn btn-sm btn-secondary mb-1">{{ date('g:i a', strtotime($admin_invoice->date)) }}</button>
                                        <button class="btn btn-sm btn-secondary">{{ date('d M, Y', strtotime($admin_invoice->date)) }}</button>
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
                                        <button class="btn btn-sm btn-secondary mb-1">{{ date('g:i a', strtotime($admin_invoice->refund_cb_date)) }}</button>
                                        <button class="btn btn-sm btn-secondary">{{ date('d M, Y', strtotime($admin_invoice->refund_cb_date)) }}</button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            if($('#index_table').length != 0){
                $('#index_table').DataTable({
                    order: [[0, "desc"]]
                });
            }

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

@endpush
