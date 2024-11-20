<div>
    <div class="breadcrumb">
        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">
            <i class="fas fa-arrow-left"></i>
        </a>
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
                    <form class="form" action="#" wire:submit.prevent="client_payment_save" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" wire:model="client_payment_create_client_id">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label for="name">First Name <span>*</span></label>
                                    <input type="text" id="name" class="form-control" placeholder="Name" wire:model="client_payment_create_name">
                                    @error('client_payment_create_name') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="email">Email <span>*</span></label>
                                    <input type="email" id="email" class="form-control" placeholder="Email" wire:model="client_payment_create_email">
                                    @error('client_payment_create_email') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="contact">Contact</label>
                                    <input type="text" id="contact" class="form-control" placeholder="Contact" wire:model="client_payment_create_contact">
                                    @error('client_payment_create_contact') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="brand">Brand Name <span>*</span></label>
                                    <select wire:model="client_payment_create_brand" id="brand" class="form-control">
{{--                                        <option value="">Select Brand</option>--}}
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_payment_create_brand') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="service">Service <span>*</span></label>
                                    <select wire:model="client_payment_create_service" id="service" class="form-control" multiple>
                                        <option value="">Select service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_payment_create_service') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="package">Package <span>*</span></label>
                                    <select wire:model="client_payment_create_package" id="package" class="form-control">
                                        <option value="">Select package</option>
                                        <option value="0">Custom Package</option>
                                    </select>
                                    @error('client_payment_create_package') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3 form-group mb-3">
                                    <label for="createform">Create form of Service <span>*</span></label>
                                    <select wire:model="client_payment_create_createform" id="createform" class="form-control">
                                        <option value="">Select Option</option>
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                    </select>
                                    @error('client_payment_create_createform') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3 form-group mb-3">
                                    <label for="custom_package">Name for a Custom Package</label>
                                    <input type="text" id="custom_package" class="form-control" value="" placeholder="Custom Package Name" wire:model="client_payment_create_custom_package">
                                </div>
                                <div class="col-md-2 form-group mb-3">
                                    <label for="currency">Currency<span>*</span></label>
                                    <select wire:model="client_payment_create_currency" id="currency" class="form-control select2">
                                        <option value="">Select Currency</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->name}} - {{$currency->short_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('client_payment_create_currency') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2 form-group mb-3">
                                    <label for="amount">Amount<span>*</span></label>
                                    <input step=".01" type="number" id="amount" class="form-control" value="" placeholder="Amount" wire:model="client_payment_create_amount" min="0">
                                    @error('client_payment_create_amount') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2 form-group mb-3">
                                    <label for="payment_type">Payment Type<span>*</span></label>
                                    <select class="form-control" wire:model="client_payment_create_payment_type" id="payment_type">
                                        <option value="">Select payment type</option>
                                        <option value="0" selected>One-Time Charge</option>
                                        <!-- <option value="1">Three-Time Charge</option> -->
                                    </select>
                                    @error('client_payment_create_payment_type') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3 form-group mb-3">
                                    <label for="merchant">Merchant<span>*</span></label>
                                    <select wire:model="client_payment_create_merchant" id="merchant" class="form-control">
                                        <option value="">Select Merchant</option>
                                        @foreach($merchant as $merchants)
                                            <option value="{{ $merchants->id }}" selected>{{ $merchants->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_payment_create_merchant') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3 form-group mb-3">
                                    <label for="sendemail">Send Email to Customer<span>*</span></label>
                                    <select wire:model="client_payment_create_sendemail" id="sendemail" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    @error('client_payment_create_sendemail') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12 form-group mb-3">
                                    <label for="discription">Description</label>
                                    <textarea wire:model="client_payment_create_discription" id="" cols="30" rows="6" class="form-control"></textarea>
                                    @error('client_payment_create_discription') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label for="sales_agent_id">Specify agent <span>(Optional)</span></label>
                                    <select wire:model="client_payment_create_sales_agent_id" id="sales_agent_id" class="form-control select2">
                                        <option value="">Select sales agent</option>
                                        @foreach($sale_agents as $sale_agent)
                                            <option value="{{ $sale_agent->id }}">{{$sale_agent->name}} {{$sale_agent->last_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('client_payment_create_sales_agent_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label for="recurring">Recurring amount</label>
                                    <input step=".01" type="number" id="recurring" class="form-control" value="0.00" placeholder="Recurring amount" wire:model="client_payment_create_recurring">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label for="sale_or_upsell">Sale/Upsell</label>
                                    <select wire:model="client_payment_create_sale_or_upsell" id="sale_or_upsell" class="form-control">
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
</div>
