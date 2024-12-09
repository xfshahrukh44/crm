@extends('layouts.app-admin')

@section('content')
<div class="breadcrumb row">
    <div class="col-md-8">
        <h1>Admin Invoice List</h1>
        <ul>
            <li><a href="#">Admin Invoice</a></li>
            <li>Admin Invoice List</li>
        </ul>
    </div>
    <div class="col-md-4 text-right">
        <a href="{{ route('admin.admin-invoice.create') }}" class="btn btn-primary">Create Admin Invoice</a>
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
                                    <td>{{$admin_invoice->id}}</td>
                                    <td>{{$admin_invoice->client_id ?? 'N/A'}}</td>
                                    <td>{{$admin_invoice->client_name ?? 'N/A'}}</td>
                                    <td>{{$admin_invoice->client_email ?? 'N/A'}}</td>
                                    <td>{{$admin_invoice->client_phone ?? 'N/A'}}</td>
                                    <td>
                                        @php
                                            $service_list = explode(',', $admin_invoice->service);
                                        @endphp
                                        @for($i = 0; $i < count($service_list); $i++)
                                            @if($service_list[$i])
                                                @php
                                                    $service_list_name = '';
                                                    $var_check = $admin_invoice->services($service_list[$i]);
                                                    $words = $var_check ? explode(" ", $var_check->name) : [];
                                                @endphp
                                                @for($j = 0; $j < count($words); $j++)
                                                    @php
                                                        $service_list_name .= $words[$j][0];
                                                    @endphp
                                                @endfor
                                                <span class="btn btn-info btn-sm mb-1">{{ $service_list_name }}</span>
                                            @endif
                                        @endfor
                                    </td>
                                    <td>{{ $admin_invoice->currency_show->sign }}{{ $admin_invoice->amount }}</td>
                                    <td>{{ $admin_invoice->currency_show->sign }}{{ $admin_invoice->recurring }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary mb-1">{{ date('g:i a', strtotime($admin_invoice->created_at)) }}</button>
                                        <button class="btn btn-sm btn-secondary">{{ date('d M, Y', strtotime($admin_invoice->created_at)) }}</button>
                                    </td>
                                    <td>{{ $admin_invoice->sale->name ?? '' }} {{ $admin_invoice->sale->last_name ?? '' }}</td>
                                    <td>{{$admin_invoice->sale_upsell ?? 'N/A'}}</td>
                                    <td>{{ $admin_invoice->transfer->name ?? '' }} {{ $admin_invoice->transfer->last_name ?? '' }}</td>
                                    <td>{{$admin_invoice->department ?? 'N/A'}}</td>
                                    <td><span class="btn btn-primary btn-sm">{{ $admin_invoice->brands->name ?? 'N/a' }}</span></td>
                                    <td>{{$admin_invoice->type ?? 'N/A'}}</td>
                                    <td><span class="btn btn-primary btn-sm">{{ $admin_invoice->merchant->name ?? 'N/a' }}</span></td>
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
        });
    </script>

@endpush
