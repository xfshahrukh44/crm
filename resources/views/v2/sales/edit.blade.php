@extends('v2.layouts.app')

@section('title', 'Edit sales')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brief-pg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brief-info">
                            <h2 class="mt-4">Sales Form</h2>
                            <form action="{{route('v2.users.sales.update', $user->id)}}" method="POST">
                                @csrf
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
                                            <label>Email Address *</label>
                                            <input type="email" class="form-control" name="email" id="email" value="{{old('email') ?? $user->email}}" required>
                                            @error('email')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        @php
                                            $user_brands = $user->brand_list() ?? [];
                                        @endphp
                                        <div class="form-group">
                                            <label>Brand Name *</label>
                                            <select class="form-control select2" name="brand_id[]" id="brand_id" multiple required>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}" {{ in_array($brand->id, (old('brand_id') ?? [])) || in_array($brand->id, $user_brands) ? 'selected' : ''}}>{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Contact Number</label>
                                            <input type="text" class="form-control" name="contact" id="contact" value="{{old('contact') ?? $user->contact}}">
                                            @error('contact')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Select Status *</label>
                                            <select class="form-control select2" name="status" id="status" required>
                                                <option value="1" {{ old('status') ==  "1" || $user->status ==  "1" ? 'selected' : ' '}}>Active</option>
                                                <option value="0" {{ old('status') ==  "0" || $user->status ==  "0" ? 'selected' : ' '}}>Deactive</option>
                                            </select>
                                            @error('status')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Password *</label>
                                            <input type="text" class="form-control" name="password" id="password">
                                            @error('password')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Select Role *</label>
                                            <select class="form-control select2" name="is_employee" id="is_employee" required>
                                                <option value="">Select Role</option>
                                                <option value="4" {!! old('is_employee') == "4" || $user->is_employee == "4" ? 'selected' : '' !!}>Customer Support (PROJECT MANAGER)</option>
                                                <option value="0" {!! old('is_employee') == "0" || $user->is_employee == "0" ? 'selected' : '' !!}>Sale Agent (FRONT SALES)</option>
                                                <option value="6" {!! old('is_employee') == "6" || $user->is_employee == "6" ? 'selected' : '' !!}>Sales Manager (BUH)</option>
                                                <option value="8" {!! old('is_employee') == "8" || $user->is_employee == "8" ? 'selected' : '' !!}>Support Head (PM HEAD)</option>
                                                <option value="9" {!! old('is_employee') == "9" || $user->is_employee == "9" ? 'selected' : '' !!}>PPC</option>
                                            </select>
                                            @error('is_employee')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Is Upsell ?*</label>
                                            <select class="form-control select2" name="is_upsell" id="is_upsell" required>
                                                <option value="0" {!! old('is_upsell') == 0 || $user->is_upsell == 0 ? 'selected' : '' !!}>No</option>
                                                <option value="1" {!! old('is_upsell') == 1 || $user->is_upsell == 1 ? 'selected' : '' !!}>Yes</option>
                                            </select>
                                            @error('is_upsell')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    @if(v2_acl([2]))
                                        @php
                                            $merchants = get_my_merchants();
                                            $selected_merchants = explode(',', $user->merchant);
                                        @endphp
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Merchants</label>
                                                <select class="form-control select2" name="merchant[]" id="merchant" multiple>
                                                    @foreach($merchants as $merchant)
                                                        <option value="{{$merchant->id}}" {{ in_array($merchant->id, (old('merchant') ?? [])) || in_array($merchant->id, ($selected_merchants ?? [])) ? 'selected' : ''}}>{{$merchant->id}}. {{$merchant->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('merchant')
                                                <label class="text-danger">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
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
    <script>
        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }
        $(document).ready(() => {
            $('#password').val(generatePassword());
        });
    </script>
@endsection
