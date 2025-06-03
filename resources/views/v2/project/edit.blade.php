@extends('v2.layouts.app')

@section('title', 'Edit client')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                @php
                    $brands = \Illuminate\Support\Facades\DB::table('brands')->get();
                @endphp
                <section class="brief-pg">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="brief-info">
                                    <h2 class="mt-4">Merchant Form</h2>
                                    <form action="{{route('v2.clients.update', $client->id)}}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>First name *</label>
                                                    <input type="text" class="form-control" name="name" value="{{old('name') ?? $client->name}}" required>
                                                    @error('name')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Last name *</label>
                                                    <input type="text" class="form-control" name="last_name" value="{{old('last_name') ?? $client->last_name}}" required>
                                                    @error('last_name')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Email *</label>
                                                    <input type="email" class="form-control" name="email" id="email" value="{{old('email') ?? $client->email}}" required>
                                                    @error('email')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Contact</label>
                                                    <input type="text" class="form-control" name="contact" value="{{old('contact') ?? $client->contact}}">
                                                    @error('contact')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Brand *</label>
                                                    <select class="form-control select2" name="brand_id" id="brand_id" required>
                                                        <option value="">Select brand *</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{$brand->id}}" {!! old('brand') == $brand->id || $client->brand_id == $brand->id ? 'selected' : '' !!}>{{$brand->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand_id')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Select status *</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" {!! old('status') == "1" || $client->status == "1" ? 'selected' : '' !!}>Active</option>
                                                        <option value="0" {!! old('status') == "0" || $client->status == "0" ? 'selected' : '' !!}>Deactive</option>
                                                    </select>
                                                    @error('status')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Select priority *</label>
                                                    <select name="priority" class="form-control" required>
                                                        <option value="1" {!! old('priority') == "1" || $client->priority == "1" ? 'selected' : '' !!} class="bg-danger text-white">HIGH</option>
                                                        <option value="2" {!! old('priority') == "2" || $client->priority == "2" ? 'selected' : '' !!} class="bg-warning text-black" selected="">MEDIUM</option>
                                                        <option value="3" {!! old('priority') == "3" || $client->priority == "3" ? 'selected' : '' !!} class="bg-info text-white">LOW</option>
                                                    </select>
                                                    @error('priority')
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
