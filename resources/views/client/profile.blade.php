@extends('client.layouts.app')
@section('title', 'Profile')

@section('css')
    <style>
        #preview_image {
            border-radius: 100%;
            max-width: 300px;
            max-height: 300px;
            border: 5px solid #01abea;
            min-width: 300px;
            min-height: 300px;
            object-fit: cover;
        }
    </style>
@endsection

@section('content')
    <section class="profile-section">
        <div class="container bg-colored">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="profile-left-parent doted-border">
                        <p class="heading-3">
                            Profile Picture
                        </p>
                        <figure class="large-profile">
                            <img src="{{asset(auth()->user()->image ?? 'images/avatar.png')}}" class="img-fluid" id="preview_image" alt="">
                        </figure>
                        <span class="upload-profile-pic">
                    <label for="file-upload" class="custom-file-upload d-flex align-items-center">
                        <figure class="mx-2">
                            <img src="{{asset('images/upload-icon.png')}}" class="img-fluid" alt="">
                        </figure>
                        <br>
                        <p>
                            Click to upload
                        </p>
                    </label>
                    <form action="{{route('client.update.profile.picture')}}" method="POST" id="form_import" enctype="multipart/form-data" hidden>
                        @csrf
                        <input id="file-upload" type="file" name="profile_picture" accept=".jpeg, .jpg, .png, .webp">
                    </form>
                    </span>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="profile-right-parent">
                        <p class="heading-3">
                            Profile Details
                        </p>
                        <div class="parent-profile-details">
                            <form action="{{route('client.update.profile')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="exampleFormControlInput1"
                                                   placeholder="Jason" value="{{auth()->user()->name}}" name="name">
                                            @error('name')
                                                <span class="text-danger" style="font-size: 12px;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="exampleFormControlInput1"
{{--                                                   placeholder="Jasonmartin@gmail.com" value="{{auth()->user()->email}}" name="email">--}}
                                                   placeholder="Jasonmartin@gmail.com" value="{{auth()->user()->email}}" disabled readonly>
                                            @error('email')
                                                <span class="text-danger" style="font-size: 12px;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="exampleFormControlInput1"
                                                   placeholder="987 654 321" value="{{auth()->user()->contact}}" name="contact">
                                            @error('contact')
                                                <span class="text-danger" style="font-size: 12px;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="exampleFormControlInput1"
                                                   placeholder="Martin" value="{{auth()->user()->last_name}}" name="last_name">
                                            @error('last_name')
                                                <span class="text-danger" style="font-size: 12px;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Alternative Email</label>
                                            <input type="email" class="form-control" id="exampleFormControlInput1"
                                                   placeholder="altemail@domain.com" value="{{auth()->user()->alternate_email}}" name="alternate_email">
                                            @error('alternate_email')
                                                <span class="text-danger" style="font-size: 12px;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6>Change password</h6>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New password</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                            @error('password')
                                                <span class="text-danger" style="font-size: 12px;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6>&nbsp;</h6>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm password</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="profile-details-save-btn">
                                            <button class="btn custom-btn blue">
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const rand = () =>
            Array.from({ length: 10 }, () => Math.floor(Math.random() * 100));

        // let data = rand();
        const checkingData = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, .0, 0.0];
        const savingsData = [300.27, 500.27, 150.27, 430.27, 170.27, 287.27, 100.27, 287.27, 500.27, 245.27];

        // function addData(chart, data) {
        //   chart.data.datasets.forEach(dataset => {
        //     let data = dataset.data;
        //     const first = data.shift();
        //     data.push(first);
        //     dataset.data = data;
        //   });

        //   chart.update();
        // }

        var ctx = document.getElementById("myChart").getContext("2d");
        var myChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["In progress", "On Hold", "Completed"],
                datasets: [{
                    axis: 'y',
                    // data: [65, 59, 80], // Match the data length to labels
                    data: JSON.parse('{{json_encode(get_clients_service_status_data())}}'), // Match the data length to labels
                    backgroundColor: [
                        'rgba(173, 216, 230, 0.5)', // Proper RGBA format
                        'rgba(0, 0, 139, 0.5)',
                        'rgba(40, 167, 69, 0.5)',
                    ],
                    borderColor: [
                        'rgb(173, 216, 230)',
                        'rgb(0, 0, 139)',
                        'rgb(40, 167, 69)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bars,
                barThickness: 40,
                animation: {
                    duration: 250
                },
                plugins: {
                    legend: {
                        display: false // Removes the top-center label
                    },
                    title: {
                        display: false,
                        text: "Service progress" // Chart title
                    }
                }
            }
        });

    </script>

    <script>
        $(document).ready(function () {
            var chatContainer = $('.conversions');
            chatContainer.scrollTop(chatContainer.prop('scrollHeight'));

            // $('#file-upload').on('change', function () {
            //     toastr.success('File attached');
            // });

            $('.form').on('submit', function (e) {
                $('#loading-screen').show();
            });


            $('#file-upload').on('change', function () {
                $('#form_import').submit();
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.text-area p').forEach((p) => {
                if (!p.textContent.trim()) {
                    p.remove();
                }
            });

            document.querySelectorAll('.text-area br').forEach((br) => {
                if (!br.textContent.trim()) {
                    br.remove();
                }
            });
        });
    </script>
@endsection
