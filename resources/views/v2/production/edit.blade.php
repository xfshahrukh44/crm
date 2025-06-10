@extends('v2.layouts.app')

@section('title', 'Edit production')

@section('css')

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
                                    <h2 class="mt-4">Production Form</h2>
                                    @php
                                        $categories = \Illuminate\Support\Facades\DB::table('create_categories')->get();
                                    @endphp
                                    <form action="{{route('v2.users.production.update', $user->id)}}" method="POST">
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
                                                <div class="form-group">
                                                    <label>Select Role *</label>
                                                    <select class="form-control select2" name="is_employee" id="is_employee" required>
                                                        <option value="">Select Role</option>
                                                        <option value="1" {!! old('is_employee') == '1' || $user->is_employee == '1' ? 'selected' : '' !!}>Team Lead</option>
                                                        <option value="5" {!! old('is_employee') == '5' || $user->is_employee == '5' ? 'selected' : '' !!}>Member</option>
                                                    </select>
                                                    @error('is_employee')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                @php
                                                    $user_categories = $user->category->pluck('id')->toArray() ?? [];
                                                @endphp
                                                <div class="form-group">
                                                    <label>Select categories *</label>
                                                    <select class="form-control select2" name="category_id[]" id="category_id" multiple required>
                                                        @foreach($categories as $category)
                                                            <option value="{{$category->id}}" {{ in_array($category->id, (old('category_id') ?? [])) || in_array($category->id, $user_categories) ? 'selected' : ''}}>{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
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
