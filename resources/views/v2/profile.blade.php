@extends('v2.layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="for-slider-main-banner">
        <section class="profile-pg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="profile-info">
                            <form action="{{route('v2.update.pfp')}}" id="form_pfp" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="profile-img">
                                            <h6>Profile Picture</h6>
                                            <img src="{{auth()->user()->image ? asset(auth()->user()->image) : asset('images/avatar.png')}}" class="img-fluid" alt="">
                                            <div class="">
{{--                                                <input name="file1" type="file" class="drowpify" data-height="100" hidden />--}}
{{--                                                <span>Or</span>--}}
                                                <div class="browsw-img">
                                                    <input type="file" name="pfp" id="input_pfp" accept=".jpeg, .jpg, .png, .webp" hidden/>
                                                    <input type="file" id="file" class="inputfile" hidden/>
                                                    <label for="input_pfp" style="border-radius: 20px;
                                                        padding: 8px 20px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        margin: auto;
                                                        width: 40%;
                                                        margin-top: 20px;">
                                                        <span>Change</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="profile-details">
                                            <h4>Profile Details</h4>
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <div class="form-group">
                                                        <label>First Name</label>
                                                        <input type="text" name="" class="form-control" placeholder="Jason"
                                                               value="{{auth()->user()->name}}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Last Name</label>
                                                        <input type="text" name="" class="form-control" placeholder="Martin"
                                                               value="{{auth()->user()->last_name}}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input type="email" name="" class="form-control"
                                                               value="{{auth()->user()->email}}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Phone</label>
                                                        <input type="text" name="" class="form-control"
                                                               value="{{auth()->user()->contact}}" disabled>
                                                    </div>
                                                </div>
{{--                                                <div class="col-6">--}}
{{--                                                    <div class="form-group">--}}
{{--                                                        <label>Email</label>--}}
{{--                                                        <input type="email" name="" class="form-control"--}}
{{--                                                               placeholder="Business Email" required>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="form-group">--}}
{{--                                                        <label>Business Phone</label>--}}
{{--                                                        <input type="number" name="" class="form-control"--}}
{{--                                                               placeholder="987 654 321" required>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-lg-12">--}}
{{--                                                    <div class="form-group">--}}
{{--                                                        <label>Address</label>--}}
{{--                                                        <input type="text" name="" class="form-control" placeholder=""--}}
{{--                                                               required>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-lg-12">--}}
{{--                                                    <div class="brief-bttn">--}}
{{--                                                        <button class="btn brief-btn" type="submit">Submit Form</button>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
                                            </div>
                                        </div>
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
        $(document).ready(function () {
            $('#input_pfp').on('change', function () {
                $('#form_pfp').submit();
            });
        });
    </script>
@endsection
