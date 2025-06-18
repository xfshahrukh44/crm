@extends('v2.layouts.app')

@section('title', 'Profile')

@section('css')
    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />

    <style>
        /*.cropper-container {*/
        /*    width: 100% !important;*/
        /*    height: 100% !important;*/
        /*}*/
    </style>
@endsection

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
                                            <img src="{{auth()->user()->image ? asset(auth()->user()->image) : asset('images/avatar.png')}}" class="img-fluid" alt="" style="border: 6px solid #059bd4; border-radius: 200px;">
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
                                                        background-color: #059bd4;
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

    <!-- Notes Modal -->
    <div class="modal fade" id="modal_crop_image" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Crop image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-center d-flex justify-content-center">
                            <img id="imagePreview" style="max-width:100%;" />
                        </div>

                        <div class="col-md-12">
                            <input type="range" id="zoomSlider" min="1" max="3" step="0.01" value="1" hidden />
                            <div style="margin-top:10px; text-align:right;">
                                <a class="badge badge-sm bg-primary text-white p-2" href="javascript:void(0);" id="cropBtn">Crop & Upload</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{--            <div class="modal-footer">--}}
                {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                {{--            </div>--}}
            </div>
        </div>
    </div>

{{--    <!-- Modal for cropping -->--}}
{{--    <div id="cropperModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:#000000c2; z-index:9999; justify-content:center; align-items:center;">--}}
{{--        <div style="background:white; padding:20px; border-radius:10px; max-width:500px; width:100%;">--}}
{{--            <img id="imagePreview" style="max-width:100%;" />--}}
{{--            <input type="range" id="zoomSlider" min="1" max="3" step="0.01" value="1" />--}}
{{--            <div style="margin-top:10px; text-align:right;">--}}
{{--                <a class="badge badge-sm bg-primary text-white p-2" href="javascript:void(0);" id="cropBtn">Crop & Upload</a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection

@section('script')
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        $(document).ready(function () {
            let cropper;

            $('#input_pfp').on('change', function (e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (event) {
                    $('#imagePreview').attr('src', event.target.result);
                    $('#modal_crop_image').modal('show');

                    // $('#cropperModal').css('display', 'flex');

                    if (cropper) cropper.destroy();
                    cropper = new Cropper(document.getElementById('imagePreview'), {
                        aspectRatio: 1,
                        viewMode: 1,
                        zoomable: true,
                        scalable: false,
                        rotatable: false,
                        cropBoxResizable: false,
                        minContainerWidth: 300,
                        minContainerHeight: 300
                    });

                    // $('#zoomSlider').on('input', function () {
                    //     cropper.zoomTo(parseFloat(this.value));
                    // });
                };
                reader.readAsDataURL(file);
            });

            $('#cropBtn').on('click', function () {
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300
                });

                canvas.toBlob(function (blob) {
                    const file = new File([blob], 'cropped.png', { type: 'image/png' });

                    // Inject the file into the original file input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('input_pfp').files = dataTransfer.files;

                    // $('#input_pfp').val(blob);
                    $('#modal_crop_image').hide();
                    // $('#cropperModal').hide();
                    $('#form_pfp').submit();

                    // // Submit form via AJAX or attach to your existing form
                    // $.ajax({
                    //     url: $('#form_pfp').attr('action'),
                    //     method: 'POST',
                    //     data: formData,
                    //     processData: false,
                    //     contentType: false,
                    //     success: function () {
                    //         alert('Uploaded!');
                    //         $('#cropperModal').hide();
                    //     },
                    //     error: function () {
                    //         alert('Upload failed.');
                    //     }
                    // });
                });
            });
        });
    </script>
@endsection
