@extends('client.layouts.app')
@section('title', 'Invoices')

@section('css')

@endsection

@section('content')
    <section class="invoice-listing">
        <div class="container bg-colored">
            <form action="{{ route('client.invoice') }}" method="GET">
                <div class="row align-items-start invoice-listing-select-bar">
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="Search Package" name="package" value="{{ Request::get('package') }}" style="border-radius: 20px;">
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="Search Invoice#" name="invoice" value="{{ Request::get('invoice') }}" style="border-radius: 20px;">
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" aria-label="Default select example" name="status" id="status">
                            <option selected>Select Status</option>
                            <option value="0" {{ Request::get('status') == 0 ? 'selected' : '' }}>Any</option>
                            <option value="2" {{ Request::get('status') == 2 ? 'selected' : '' }}>Paid</option>
                            <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="profile-details-save-btn">
                            <button class="btn custom-btn blue">
                                Search Result
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row align-items-center">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr class="colored-table-row">
                            <th scope="col">ID</th>
                            <th scope="col">Package Name</th>
                            <th scope="col">Service</th>
                            <th scope="col">Status</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                                <tr>
                                    <td scope="row">
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
                                        <span class="{{$datas->payment_status == '2' ? 'status-paid' : ''}}" style="color: {!! $datas->payment_status == '2' ? 'green': 'red' !!};">
                                            {{ App\Models\Invoice::PAYMENT_STATUS[$datas->payment_status] }}
                                        </span>
                                    </td>
                                    <td >{{ $datas->currency_show->sign }}{{ $datas->amount }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @if($datas->payment_status == 1)
                                                @if($datas->stripe_invoice_url)
                                                    <a target="_blank" href="{{$datas->stripe_invoice_url}}" class="btn btn-primary btn-icon btn-sm mr-1">
                                                        <span class="ul-btn__text"><b>PAY NOW</b></span>
                                                    </a>
                                                @endif
                                                @if($datas->is_authorize)
                                                    <a href="{{route('client.pay.with.authorize', $datas->id)}}" class="btn btn-warning btn-icon btn-sm mr-1">
                                                        <span class="ul-btn__text"><b>PAY NOW</b></span>
                                                    </a>
                                                @endif
                                            @endif

                                            <a href="{{route('client.invoice-detail', $datas->id)}}" class="btn btn-info btn-sm" title="Invoice detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
    </section>
@endsection

@section('scripts')

@endsection
