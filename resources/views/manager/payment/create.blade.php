@extends('layouts.app-manager')

@section('content')
<div class="breadcrumb">
    <h1>Payment Link - {{$user->name}} {{$user->last_name}}</h1>
    <ul>
        <li><a href="#">Clients</a></li>
        <li>Payment Link for {{$user->name}} {{$user->last_name}}</li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Payment Details Form</div>
                <form class="form" action="{{route('manager.invoice.create')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="align-items-center mb-2" style="display: flex; font-size: 15px;">
                        <input type="hidden" name="is_closing_payment" id="input_is_closing_payment_hidden" value="0">
                        <input type="checkbox" id="input_is_closing_payment">
                        <label class="m-0 pl-1" for="input_is_closing_payment" style="cursor: pointer;">
                            <b>Is recurring payment</b>
                        </label>
                    </div>
                    <input type="hidden" name="client_id" value="{{$user->id}}">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="name">First Name <span>*</span></label>
                                <input type="text" id="name" class="form-control" value="{{ $user->name }} {{ $user->last_name }}" placeholder="Name" name="name" required="required">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="email">Email <span>*</span></label>
                                <input type="email" id="email" class="form-control" value="{{ $user->email }}" placeholder="Email" name="email" required="required">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="contact">Contact</label>
                                <input type="text" id="contact" class="form-control" value="{{ $user->contact }}" placeholder="Contact" name="contact">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="brand">Brand Name <span>*</span></label>
                                <select name="brand" id="brand" class="form-control select2" required>
                                    <option value="">Select Brand</option>
                                    @foreach($brand as $brands)
                                    <option value="{{ $brands->id }}" {{ $brands->id == $user->brand_id ? 'selected' : '' }}>{{ $brands->name }} - {{ $brands->url }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="service">Service <span>*</span></label>
                                <select name="service[]" id="service" class="form-control select2" required multiple>
                                    @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>
                                    <b>Select which forms the client will see</b>
                                </label>
                                <div class="row form-group" id="show_service_form_checkboxes">

                                </div>
                                <div id="tickboxes_wrapper">

                                </div>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="package">Package <span>*</span></label>
                                <select name="package" id="package" class="form-control" required>
                                    <option value="">Select Package</option>
                                    <option value="0" selected>Custom Package</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="createform">Create form of Service <span>*</span></label>
                                <select name="createform" id="createform" class="form-control" required>
                                    <option value="">Select Option</option>
                                    <option value="1">YES</option>
                                    <option value="0">NO</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="custom_package">Name for a Custom Package</label>
                                <input type="text" id="custom_package" class="form-control" value="" placeholder="Custom Package Name" name="custom_package">
                            </div>
                            <div class="col-md-2 form-group mb-3">
                                <label for="currency">Currency<span>*</span></label>
                                <select name="currency" id="currency" class="form-control select2" required>
                                    <option value="">Select Currency</option>
                                    @foreach($currencies as $currency)
                                    <option value="{{$currency->id}}">{{$currency->name}} - {{$currency->short_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group mb-3">
                                <label for="amount">Amount<span>*</span></label>
                                <input step=".01" type="number" id="amount" class="form-control" value="" placeholder="Amount" name="amount" required min="0">
                            </div>
                            <div class="col-md-2 form-group mb-3">
                                <label for="payment_type">Payment Type<span>*</span></label>
                                <select class="form-control" name="payment_type" id="payment_type">
                                    <option value="0" selected>One-Time Charge</option>
                                    <!-- <option value="1">Three-Time Charge</option> -->
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="merchant">Merchant<span>*</span></label>
                                <select name="merchant" id="merchant" class="form-control" required>
                                    <!-- <option value="">Select Merchant</option> -->
                                    @foreach(get_my_merchants() as $merchants)
                                    <option value="{{ $merchants->id }}" selected>{{ $merchants->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="sendemail">Send Email to Customer<span>*</span></label>
                                <select name="sendemail" id="sendemail" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="discription">Description</label>
                                <textarea name="discription" id="" cols="30" rows="6" class="form-control"></textarea>
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="sales_agent_id">Transfer by</label>
                                <select name="sales_agent_id" id="sales_agent_id" class="form-control select2">
                                    <option value="">Select sales agent</option>
                                    @foreach($sale_agents as $sale_agent)
                                        <option value="{{ $sale_agent->id }}">{{$sale_agent->name}} {{$sale_agent->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="recurring">Recurring amount</label>
                                <input step=".01" type="number" id="recurring" class="form-control" value="0.00" placeholder="Recurring amount" name="recurring">
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="sale_or_upsell">Sale/Upsell</label>
                                <select name="sale_or_upsell" id="sale_or_upsell" class="form-control select2">
                                    <option value="Sale" selected>Sale</option>
                                    <option value="Upsell">Upsell</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Create Invoice</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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
                $('#show_service_form_checkboxes').append(`<div class="col-md-12">
                                                                <input type="checkbox" data-id="`+service_id+`" data-name="`+service_map[service_id]+`" class="service_tickbox" value="`+service_id+`" checked>
                                                                <label for="service_`+service_id+`">`+service_map[service_id]+`</label>
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
@endpush
