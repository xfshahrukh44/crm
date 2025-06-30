@extends('v2.layouts.app')

@section('title', 'Edit invoice')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.7.0/summernote.css" rel="stylesheet">
@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brief-pg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brief-info">
                            <h2 class="mt-4">Invoice form</h2>
                            <form action="{{route('v2.invoices.update', $invoice->id)}}" method="POST">
                                @csrf
{{--                                <div class="align-items-center mb-2" style="display: flex; font-size: 15px;">--}}
{{--                                    <input type="hidden" name="is_closing_payment" id="input_is_closing_payment_hidden" value="0">--}}
{{--                                    <input type="checkbox" id="input_is_closing_payment">--}}
{{--                                    <label class="m-0 pl-1" for="input_is_closing_payment" style="cursor: pointer;">--}}
{{--                                        <b>Is recurring payment</b>--}}
{{--                                    </label>--}}
{{--                                </div>--}}
                                <input type="hidden" name="client_id" value="{{$user->id}}">

                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>First name *</label>
                                            <input type="text" class="form-control" value="{{$user->name}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Last name *</label>
                                            <input type="text" class="form-control" value="{{$user->last_name}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" id="email" value="{{$user->email}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Contact</label>
                                            <input type="text" class="form-control" value="{{$user->contact}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Brand *</label>
                                            <select class="form-control select2" name="brand_id" id="brand_id" required>
                                                <option value="">Select brand *</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}" {!! old('brand') == $brand->id || $invoice->brand == $brand->id ? 'selected' : '' !!}>{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    {{--                                    <div class="col-4">--}}
                                    {{--                                        <div class="form-group">--}}
                                    {{--                                            <label>Brand Name*</label>--}}
                                    {{--                                            <select class="form-control select2" name="brand" id="brand" required>--}}
                                    {{--                                                <option value="">Select brand</option>--}}
                                    {{--                                                @foreach($brands as $brand)--}}
                                    {{--                                                    <option value="{{$brand->id}}" {{ (old('brand') == $brand->id || $user->brand_id == $brand->id) ? 'selected' : ' '}}>{{$brand->name}}</option>--}}
                                    {{--                                                @endforeach--}}
                                    {{--                                            </select>--}}
                                    {{--                                            @error('brand')--}}
                                    {{--                                            <label class="text-danger">{{ $message }}</label>--}}
                                    {{--                                            @enderror--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    <div class="col-12">
                                        @php
                                            $selected_services = explode(',', $invoice->service) ?? [];
                                        @endphp
                                        <div class="form-group">
                                            <label>Service *</label>
                                            <select class="form-control select2" name="service[]" id="service" required multiple>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {!! (in_array($service->id, (old('service') ?? $selected_services))) ? 'selected' : '' !!}>{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('service')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
{{--                                    <div class="col-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label>--}}
{{--                                                <b>Select which forms the client will see</b>--}}
{{--                                            </label>--}}
{{--                                            <div class="row form-group" id="show_service_form_checkboxes">--}}

{{--                                            </div>--}}
{{--                                            <div id="tickboxes_wrapper">--}}

{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    {{--                                            <div class="col-4" hidden>--}}
                                    {{--                                                <div class="form-group">--}}
                                    {{--                                                    <label>Package *</label>--}}
                                    {{--                                                    <select class="form-control" name="package" id="package">--}}
                                    {{--                                                        <option value="0" selected>Custom Package</option>--}}
                                    {{--                                                    </select>--}}
                                    {{--                                                </div>--}}
                                    {{--                                            </div>--}}
{{--                                    <div class="col-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label>Create form of Service *</label>--}}
{{--                                            <select class="form-control" name="createform" id="createform" required>--}}
{{--                                                <option value="">Select Option</option>--}}
{{--                                                <option value="1" {{ (old('createform') == '1') ? 'selected' : ''}}>YES</option>--}}
{{--                                                <option value="0" {{ (old('createform') == '0') ? 'selected' : ''}}>NO</option>--}}
{{--                                            </select>--}}
{{--                                            @error('createform')--}}
{{--                                            <label class="text-danger">{{ $message }}</label>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Name for a Custom Package *</label>
                                            <input type="text" class="form-control" name="custom_package" value="{{old('custom_package') ?? $invoice->custom_package}}" required>
                                            @error('custom_package')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    {{--                                            <div class="col-4" hidden>--}}
                                    {{--                                                <div class="form-group">--}}
                                    {{--                                                    <label>Currency *</label>--}}
                                    {{--                                                    <select class="form-control" name="currency" id="currency" required>--}}
                                    {{--                                                        <option value="1" selected>Select Currency</option>--}}
                                    {{--                                                    </select>--}}
                                    {{--                                                </div>--}}
                                    {{--                                            </div>--}}
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Amount *</label>
                                            <input step=".01" type="number" id="amount" class="form-control" value="{{old('amount') ?? $invoice->amount}}" placeholder="Amount" name="amount" required min="0">
                                            @error('amount')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    {{--                                            <div class="col-4" hidden>--}}
                                    {{--                                                <div class="form-group">--}}
                                    {{--                                                    <label>Payment Type *</label>--}}
                                    {{--                                                    <select class="form-control" name="payment_type" id="payment_type" required>--}}
                                    {{--                                                        <option value="0" selected>One-Time Charge</option>--}}
                                    {{--                                                    </select>--}}
                                    {{--                                                </div>--}}
                                    {{--                                            </div>--}}
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Merchant *</label>
                                            <select class="form-control" name="merchant" id="merchant" required>
                                                <option value="">Select Merchant</option>
                                                @foreach(get_my_merchants() as $merchant)
                                                    <option value="{{ $merchant->id }}" {{ (old('merchant') == $merchant->id || $invoice->merchant_id == $merchant->id) ? 'selected' : ''}}>{{ $merchant->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('merchant')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    {{--                                    <div class="col-4">--}}
                                    {{--                                        <div class="form-group">--}}
                                    {{--                                            <label>Send Email to Customer *</label>--}}
                                    {{--                                            <select class="form-control" name="sendemail" id="sendemail" required>--}}
                                    {{--                                                <option value="0" {{ (old('sendemail') == "0") ? 'selected' : ''}}>No</option>--}}
                                    {{--                                                <option value="1" {{ (old('sendemail') == "1") ? 'selected' : ''}}>Yes</option>--}}
                                    {{--                                            </select>--}}
                                    {{--                                            @error('sendemail')--}}
                                    {{--                                            <label class="text-danger">{{ $message }}</label>--}}
                                    {{--                                            @enderror--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="discription" id="description" cols="30" rows="4" class="form-control" style="height: unset !important;">

                                            </textarea>
                                            @error('discription')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
{{--                                    <div class="col-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label>Transfer by</label>--}}
{{--                                            <select class="form-control select2" name="sales_agent_id" id="sales_agent_id">--}}
{{--                                                <option value="">Select sales agent</option>--}}
{{--                                                @foreach($sale_agents as $sale_agent)--}}
{{--                                                    <option value="{{ $sale_agent->id }}" {{ (old('sales_agent_id') == $sale_agent->id) ? 'selected' : ''}}>{{$sale_agent->name}} {{$sale_agent->last_name}}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                            @error('sales_agent_id')--}}
{{--                                            <label class="text-danger">{{ $message }}</label>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label>Recurring amount</label>--}}
{{--                                            <input step=".01" type="number" id="recurring" class="form-control" value="0.00" placeholder="Recurring amount" name="recurring" {{old('recurring') ?? ''}}>--}}
{{--                                            @error('recurring')--}}
{{--                                            <label class="text-danger">{{ $message }}</label>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label>Sale/Upsell</label>--}}
{{--                                            <select class="form-control" name="sale_or_upsell" id="sale_or_upsell">--}}
{{--                                                <option value="Sale" selected>Sale</option>--}}
{{--                                                <option value="Upsell">Upsell</option>--}}
{{--                                            </select>--}}
{{--                                            @error('sale_or_upsell')--}}
{{--                                            <label class="text-danger">{{ $message }}</label>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

                                </div>
                                <div class="row m-auto">
                                    <div class="brief-bttn">
                                        <button class="btn brief-btn" type="submit">Submit Form</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.7.0/summernote.js"></script>
    <script>
        $(document).ready(function() {
            $('#description').summernote('code', "{!! $invoice->discription !!}");
        });
    </script>
@endsection
