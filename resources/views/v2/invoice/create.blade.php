@extends('v2.layouts.app')

@section('title', 'Create invoice')

@section('css')
    <style>
        span.select2-selection.select2-selection--multiple {
            border-radius: 20px !important;
            border: 1px solid #ced4da !important;
        }

        ul.select2-selection__rendered {
            margin-left: 1% !important;
            margin-top: 0px !important;
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="brief-pg">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="brief-info">
                                    <h2 class="mt-4">Invoice form</h2>
                                    <form action="{{route('v2.invoices.store')}}" method="POST">
                                        @csrf
                                        <div class="align-items-center mb-2" style="display: flex; font-size: 15px;">
                                            <input type="hidden" name="is_closing_payment" id="input_is_closing_payment_hidden" value="0">
                                            <input type="checkbox" id="input_is_closing_payment">
                                            <label class="m-0 pl-1" for="input_is_closing_payment" style="cursor: pointer;">
                                                <b>Is recurring payment</b>
                                            </label>
                                        </div>
                                        <input type="hidden" name="client_id" value="{{$user->id}}">

                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>First name *</label>
                                                    <input type="text" class="form-control" name="name" value="{{old('name') ?? $user->name}}" required>
                                                    @error('name')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Last name *</label>
                                                    <input type="text" class="form-control" name="last_name" value="{{old('last_name') ?? $user->last_name}}" required>
                                                    @error('last_name')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Email *</label>
                                                    <input type="email" class="form-control" name="email" id="email" value="{{old('email') ?? $user->email}}" required>
                                                    @error('email')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Contact</label>
                                                    <input type="text" class="form-control" name="contact" value="{{old('contact') ?? $user->contact}}">
                                                    @error('contact')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Brand Name*</label>
                                                    <select class="form-control select2" name="brand" id="brand" required>
                                                        <option value="">Select brand</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{$brand->id}}" {{ (old('brand') == $brand->id || $user->brand_id == $brand->id) ? 'selected' : ' '}}>{{$brand->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Service *</label>
                                                    <select class="form-control select2" name="service[]" id="service" required multiple>
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}" {!! (in_array($service->id, (old('service') ?? []))) ? 'selected' : '' !!}>{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('service')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>
                                                        <b>Select which forms the client will see</b>
                                                    </label>
                                                    <div class="row form-group" id="show_service_form_checkboxes">

                                                    </div>
                                                    <div id="tickboxes_wrapper">

                                                    </div>
                                                </div>
                                            </div>
{{--                                            <div class="col-4" hidden>--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label>Package *</label>--}}
{{--                                                    <select class="form-control" name="package" id="package">--}}
{{--                                                        <option value="0" selected>Custom Package</option>--}}
{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Create form of Service *</label>
                                                    <select class="form-control" name="createform" id="createform" required>
                                                        <option value="">Select Option</option>
                                                        <option value="1" {{ (old('createform') == '1') ? 'selected' : ''}}>YES</option>
                                                        <option value="0" {{ (old('createform') == '0') ? 'selected' : ''}}>NO</option>
                                                    </select>
                                                    @error('createform')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Name for a Custom Package *</label>
                                                    <input type="text" class="form-control" name="custom_package" value="{{old('custom_package') ?? ''}}" required>
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
                                                    <input step=".01" type="number" id="amount" class="form-control" value="{{old('amount') ?? ''}}" placeholder="Amount" name="amount" required min="0">
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
                                                            <option value="{{ $merchant->id }}" {{ (old('merchant') == $merchant->id) ? 'selected' : ''}}>{{ $merchant->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('merchant')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Send Email to Customer *</label>
                                                    <select class="form-control" name="sendemail" id="sendemail" required>
                                                        <option value="0" {{ (old('sendemail') == "0") ? 'selected' : ''}}>No</option>
                                                        <option value="1" {{ (old('sendemail') == "1") ? 'selected' : ''}}>Yes</option>
                                                    </select>
                                                    @error('sendemail')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="discription" id="" cols="30" rows="4" class="form-control" style="height: unset !important;">{{old('discription') ?? ''}}</textarea>
                                                    @error('discription')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Transfer by</label>
                                                    <select class="form-control select2" name="sales_agent_id" id="sales_agent_id">
                                                        <option value="">Select sales agent</option>
                                                        @foreach($sale_agents as $sale_agent)
                                                            <option value="{{ $sale_agent->id }}" {{ (old('sales_agent_id') == $sale_agent->id) ? 'selected' : ''}}>{{$sale_agent->name}} {{$sale_agent->last_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('sales_agent_id')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Recurring amount</label>
                                                    <input step=".01" type="number" id="recurring" class="form-control" value="0.00" placeholder="Recurring amount" name="recurring" {{old('recurring') ?? ''}}>
                                                    @error('recurring')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Sale/Upsell</label>
                                                    <select class="form-control" name="sale_or_upsell" id="sale_or_upsell">
                                                        <option value="Sale" selected>Sale</option>
                                                        <option value="Upsell">Upsell</option>
                                                    </select>
                                                    @error('sale_or_upsell')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>

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

                @break

            @default
                <section class="brief-pg">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="brief-info">
                                    <h4>Brief Pending Info</h4>
                                    <h2>Book Formatting & Publishing Brief Form</h2>
                                    <form action="">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>What is the title of the book? *</label>
                                                    <input type="text" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>What is the subtitle of the book?</label>
                                                    <input type="text" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Any additional contributors you would like to acknowledge? (e.g. Book
                                                        Illustrator, Editor, etc.) *</label>
                                                    <textarea id="textarea" rows="7" class="form-control"
                                                              required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="fromatting-">
                                                    <h2>Formatting Requirements</h2>
                                                    <div class="formate-select">
                                                        <label>Where do you want to? *</label>
                                                        <ul>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Amazon KDP</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/barnes.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Barnes & Noble</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/google-book.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Google Books</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/google-book2.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Google Books</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/spark.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Ingram Spark</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="formate-select">
                                                        <label>Which formats do you want your book to be formatted on? *</label>
                                                        <ul>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>e Book</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>e Book</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>e Book</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
        @endswitch
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let service_map = {};
            @foreach($services as $service)
                service_map['{{$service->id}}'] = '{{$service->name}}';
            @endforeach

            $('#input_is_closing_payment').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#input_is_closing_payment_hidden').val('1');

                    let client_service_ids = '{{$user->show_service_forms}}';
                    $('#service').val(client_service_ids.split(',')).trigger('change');
                    $('.service_tickbox').each((i, item) => {
                        $(item).is(':checked', false);
                    });
                    $('#show_service_form_checkboxes').prop('hidden', true);

                    $('#createform').val('0').trigger('change');
                    $('#createform').prop('disabled', true);
                } else {
                    $('#input_is_closing_payment_hidden').val('0');
                    $('#service').val([]).trigger('change');
                    $('#show_service_form_checkboxes').prop('hidden', false);

                    $('#createform').val('').trigger('change');
                    $('#createform').prop('disabled', false);
                }
            });

            $('#service').on('change', function () {
                $('#show_service_form_checkboxes').html('');
                $('#tickboxes_wrapper').html('');
                let service_ids = $(this).val();

                for (const service_id of service_ids) {
                    $('#show_service_form_checkboxes').append(`<div class="col-12">
                                                                    <input type="checkbox" id="service_`+service_id+`" data-id="`+service_id+`" data-name="`+service_map[service_id]+`" class="service_tickbox" value="`+service_id+`" checked>
                                                                    <label style="cursor:pointer;" for="service_`+service_id+`">`+service_map[service_id]+`</label>
                                                                </div>`);

                    $('#tickboxes_wrapper').append(`<input type="hidden" name="show_service_forms[on][]" value="`+service_id+`">`);
                }
            });

            $('body').on('change', '.service_tickbox', function () {
                $('#tickboxes_wrapper').html('');

                $('.service_tickbox').each((i, item) => {
                    if ($(item).is(':checked')) {
                        $('#tickboxes_wrapper').append(`<input type="hidden" name="show_service_forms[on][]" value="`+$(item).data('id')+`">`);
                    } else {
                        $('#tickboxes_wrapper').append(`<input type="hidden" name="show_service_forms[off][]" value="`+$(item).data('id')+`">`);
                    }
                });

            });
        });
    </script>
@endsection
