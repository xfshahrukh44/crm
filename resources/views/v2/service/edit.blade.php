@extends('v2.layouts.app')

@section('title', 'Edit service')

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
                                    <h2 class="mt-4">Service Form</h2>
                                    <form action="{{route('v2.services.update', $service->id)}}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Name *</label>
                                                    <input type="text" id="name" class="form-control" placeholder="Name" name="name" required value="{{old('name') ?? $service->name}}">
                                                    @error('name')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Select Form *</label>
                                                    <select name="form" id="form" class="form-control">
                                                        <option value="0" {{ old('form') == 0 || $service->form == 0 ? 'selected' : '' }}>No Form</option>
                                                        <option value="1" {{ old('form') == 1 || $service->form == 1 ? 'selected' : '' }}>Logo Form</option>
                                                        <option value="2" {{ old('form') == 2 || $service->form == 2 ? 'selected' : '' }}>Website Form</option>
                                                        <option value="3" {{ old('form') == 3 || $service->form == 3 ? 'selected' : '' }}>Social Media Marketing Form</option>
                                                        <option value="4" {{ old('form') == 4 || $service->form == 4 ? 'selected' : '' }}>Content Writing Form</option>
                                                        <option value="5" {{ old('form') == 5 || $service->form == 5 ? 'selected' : '' }}>Search Engine Optimization Form</option>
                                                        <option value="6" {{ old('form') == 6 || $service->form == 6 ? 'selected' : '' }}>Book Formatting & Publishing Form</option>
                                                        <option value="7" {{ old('form') == 7 || $service->form == 7 ? 'selected' : '' }}>Book Writing Form</option>
                                                        <option value="8" {{ old('form') == 8 || $service->form == 8 ? 'selected' : '' }}>Author Website Form</option>
                                                        <option value="9" {{ old('form') == 9 || $service->form == 9 ? 'selected' : '' }}>Editing & Proofreading Form</option>
                                                        <option value="10" {{ old('form') == 10 || $service->form == 10 ? 'selected' : '' }}>Book Cover Design</option>
                                                        <option value="11" {{ old('form') == 11 || $service->form == 11 ? 'selected' : '' }}>ISBN Form</option>
                                                        <option value="12" {{ old('form') == 12 || $service->form == 12 ? 'selected' : '' }}>Book Printing Form</option>
                                                        <option value="13" {{ old('form') == 13 || $service->form == 13 ? 'selected' : '' }}>SEO Form</option>
                                                        <option value="14" {{ old('form') == 14 || $service->form == 14 ? 'selected' : '' }}>Book Marketing Form</option>
                                                        <option value="15" {{ old('form') == 15 || $service->form == 15 ? 'selected' : '' }}>Social Media Marketing Form (NEW)</option>
                                                        <option value="16" {{ old('form') == 16 || $service->form == 16 ? 'selected' : '' }}>Press Release Form</option>
                                                    </select>
                                                    @error('form')
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
