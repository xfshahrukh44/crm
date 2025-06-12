@extends('v2.layouts.app')

@section('title', 'Create lead')

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
        <section class="brief-pg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brief-info">
                            <h2 class="mt-4">Lead Form</h2>
                            <form action="{{route('v2.leads.store')}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>First name *</label>
                                            <input type="text" class="form-control" name="name" value="{{old('name') ?? ''}}" required>
                                            @error('name')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Last name *</label>
                                            <input type="text" class="form-control" name="last_name" value="{{old('last_name') ?? ''}}" required>
                                            @error('last_name')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" name="email" id="email" value="{{old('email') ?? ''}}" required>
                                            @error('email')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Contact</label>
                                            <input type="text" class="form-control" name="contact" value="{{old('contact') ?? ''}}">
                                            @error('contact')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Service *</label>
                                            <select class="form-control select2" name="service[]" id="service" multiple required>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {!! in_array($service->id, (old('service') ?? [])) ? 'selected' : '' !!}>{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('service')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Brand *</label>
                                            <select class="form-control select2" name="brand" id="brand" required>
                                                <option value="">Select brand *</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}" {!! old('brand') == $brand->id ? 'selected' : '' !!}>{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('brand')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Select status *</label>
                                            <select name="status" class="form-control" required>
                                                <option value="Closed" {!! old('status') == 'Closed' ? 'selected' : '' !!}>Closed</option>
                                                <option value="On Discussion" {!! old('status') == 'On Discussion' ? 'selected' : '' !!}>On Discussion</option>
                                            </select>
                                            @error('status')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Assign to  *</label>
                                            <select name="user_id" class="form-control select2" required>
                                                @foreach($front_agents as $front_agent)
                                                    <option value="{{ $front_agent->id }}" {!! old('user_id') == $front_agent->id ? 'selected' : '' !!}>{{ $front_agent->name . ' ' . $front_agent->last_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
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
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(() => {

        });
    </script>
@endsection
