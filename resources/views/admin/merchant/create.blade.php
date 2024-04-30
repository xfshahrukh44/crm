@extends('layouts.app-admin')
@section('title', 'Create Merchant')
@section('content')

<div class="breadcrumb">
    <h1>Create Merchant</h1>
    <ul>
        <li><a href="#">Merchant</a></li>
        <li>Create Merchant</li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Merchant Form</div>
                <form class="form" action="{{route('admin.merchant.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf   
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="name">Name <span>*</span></label>
                                <input type="text" id="name" class="form-control" value="{{old('name')}}" placeholder="Name" name="name" required="required">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">                                
                                <label for="public_key">Public Key <span>*</span></label>
                                <input type="text" id="public_key" class="form-control" value="{{old('public_key')}}" placeholder="Public Key" name="public_key" required="required">
                                @error('public_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="secret_key">Secret Key <span>*</span></label>
                                <input type="text" id="secret_key" class="form-control" value="{{old('secret_key')}}" placeholder="Secret Key" name="secret_key" required="required">
                                @error('secret_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="secret_key">Login ID<span>(If Authorize.net Payment)</span></label>
                                <input type="text" id="login_id" class="form-control" value="{{old('login_id')}}" placeholder="Secret Key" name="login_id">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="is_authorized">Select Merchant Type <span>*</span></label>
                                <select name="is_authorized" id="is_authorized" class="form-control" >
                                    <option value="">Select Merchant Type</option>
                                    <option value="1">Stripe</option>
                                    <option value="2">Authorize.net</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="status">Select Status <span>*</span></label>
                                <select name="status" id="status" class="form-control" >
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit">Save Merchant</button>
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
   
@endpush