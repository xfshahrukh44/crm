@extends('layouts.app-admin')
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Edit Brand (ID: {{$data->id}})</h1>
    <ul>
        <li><a href="{{route('brand.index')}}">Brand</a></li>
        <li>Edit Brand</li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Category Form</div>
                <form class="form" action="{{route('brand.update',$data->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')  
                    <div class="row">
                        <div class="col-12">
                            <img src="{{ asset($data->logo) }}" width="100">
                            <hr>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="name">Name <span>*</span></label>
                            <input type="text" id="name" class="form-control" placeholder="Name" value="{{old('name', $data->name)}}" name="name" required="required">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="url">Url <span>*</span></label>
                            <input type="text" id="url" class="form-control" placeholder="Url" name="url" required="required" value="{{old('url', $data->url)}}">
                        </div>
                        <div class="col-md-4">
                            <label for="status">Select Status <span>*</span></label>
                            <select name="status" id="status" class="form-control" >
                                <option value="1" {{($data->status == 1) ? 'selected' : ''}}>Active</option>
                                <option value="0" {{($data->status == 0) ? 'selected' : ''}}>Deactive</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="status">Logo</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="phone">Phone <span>*</span></label>
                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" required="required" value="{{$data->phone}}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="phone_tel">Phone Tel <span>*</span></label>
                            <input type="text" id="phone_tel" class="form-control" placeholder="Phone Tel" name="phone_tel" required="required" value="{{$data->phone_tel}}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="email">Email <span>*</span></label>
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email" required="required" value="{{$data->email}}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="address">Address <span>*</span></label>
                            <input type="text" id="address" class="form-control" placeholder="Address" name="address" required="required" value="{{$data->address}}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="address_link">Address Link<span>*</span></label>
                            <input type="text" id="address_link" class="form-control" placeholder="Address Link" name="address_link" required="required" value="{{$data->address_link}}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="sign">Amount Sign<span>*</span></label>
                            <select name="sign" id="sign" class="form-control" required>
                                <option value="$">$ - Dollar</option>
                                <option value="£">£ - Euro</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Update Brand</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
@endsection