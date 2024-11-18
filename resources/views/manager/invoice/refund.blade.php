@extends('layouts.app-manager')

@section('content')
<div class="breadcrumb">
    <h1>Refund / CB</h1>
{{--    <ul>--}}
{{--        <li><a href="#">Clients</a></li>--}}
{{--        <li>Payment Link for {{$user->name}} {{$user->last_name}}</li>--}}
{{--    </ul>--}}
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Refund / CB Form</div>
                <form class="form" action="{{route('manager.refund.cb.submit')}}" method="POST" enctype="multipart/form-data">
                    @csrf
{{--                    <input type="hidden" name="client_id" value="{{$user->id}}">--}}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-3 form-group mb-3">
                                <label for="invoice_number">Invoice number</label>
                                <input type="text" id="invoice_number" class="form-control" placeholder="Enter Invoice Number" name="invoice_number" min="1" required>
                            </div>

                            <div class="col-md-3 form-group mb-3">
                                <label for="refunded_cb">Refunded CB</label>
                                <input step=".01" type="number" id="refunded_cb" class="form-control" value="0.00" placeholder="Refunded CB" name="refunded_cb" min="1" required>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="refund_cb_date">Refunded CB date</label>
                                <input type="date" id="refund_cb_date" class="form-control" placeholder="Refunded CB date" name="refund_cb_date" required>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">Invoice Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice number</th>
                            <th>Package Name</th>
                            <th>User</th>
                            <th>Agent</th>
                            <th>Brand</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Refund/CB</th>
                            <th>Refund/CB Date</th>
                            <th>Date</th>
                            <th>Create Login</th>
                            <th>Active</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($refund_cb_invoices as $datas)
                            <tr>
                                <td>
                                    <span class="btn btn-sm btn-dark">{{ $datas->id }}</span>
                                </td>
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
                                <td>{{ $datas->name }}<br>{{ $datas->email }}</td>
                                <td>{{ $datas->sale->name ?? '' }} {{ $datas->sale->last_name ?? '' }}</td>
                                <td><span class="btn btn-primary btn-sm">{{ $datas->brands->name }}</span></td>
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
                                                    $service_list_name .= $words[$j][0];
                                                @endphp
                                            @endfor
                                            <span class="btn btn-info btn-sm mb-1">{{ $service_list_name }}</span>
                                        @endif
                                    @endfor
                                </td>
                                <td>
                                    {{--                                    <span class="btn btn-{{ App\Models\Invoice::STATUS_COLOR[$datas->payment_status] }} btn-sm">--}}
                                    <span class="">
                                        {{ App\Models\Invoice::PAYMENT_STATUS[$datas->payment_status] }}
                                    </span>
                                </td>
                                <td>{{ $datas->currency_show->sign }}{{ $datas->amount }}</td>
                                <td class="text-danger">${{$datas->refunded_cb}}</td>
                                <td class="text-danger">{{\Carbon\Carbon::parse($datas->refund_cb_date)->format('d F, Y')}}</td>
                                <td>
                                    <button class="btn btn-sm btn-secondary mb-1">{{ date('g:i a', strtotime($datas->created_at)) }}</button>
                                    <button class="btn btn-sm btn-secondary">{{ date('d M, Y', strtotime($datas->created_at)) }}</button>
                                </td>
                                <td>
                                    @if($datas->payment_status == 2)
                                        <a href="javascript:;" class="btn btn-{{ $datas->client->user == null ? 'primary' : 'success' }} btn-sm auth-create" data-id="{{ $datas->client->id }}" data-auth="{{ $datas->client->user == null ? 0 : 1 }}" data-password="{{ $datas->client->user == null ? '' : $datas->client->user->password }}">{{ $datas->client->user == null ? 'Click Here' : 'Reset Pass' }}</a>
                                    @else
                                        <span class="btn btn-info btn-sm">Payment Pending</span>
                                    @endif

                                </td>
                                <td>
                                    <div>
                                        @if($datas->payment_status == 1)
                                            <a href="{{ route('manager.invoice.edit', $datas->id) }}" class="mb-2 btn btn-primary btn-icon btn-sm mr-1">
                                                <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                <span class="ul-btn__text">Edit</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('manager.link', $datas->id) }}" class="btn btn-info btn-icon btn-sm">
                                            <span class="ul-btn__icon"><i class="i-Eye-Visible"></i></span>
                                            <span class="ul-btn__text">View</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    {{ $refund_cb_invoices->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // $( "#brand" ).change(function() {
    //     if($(this).val() != ''){
    //         $.getJSON("{{ url('service-list') }}/"+ $(this).val(), function(jsonData){
    //             console.log(jsonData);
    //             select = '';
    //             $.each(jsonData, function(i,data)
    //             {
    //                 select +='<option value="'+data.id+'">'+data.name+'</option>';
    //             });
    //             $("#service").html(select);
    //         });
    //     }else{
    //         $("#service").html('');
    //     }
    // });
</script>
@endpush
