<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{asset('images/favicon-96x96.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-16x16.png')}}">

    <title>Design crm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- fonts  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
          integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/responsive.css')}}">

</head>
<body>


<section class="sign-in-sec">
    <div class="container-fluid p-0">
        <div class="row align-items-center justify-content-center sign-up-row">
            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 p-0 right-content">
                <div class="sign-in-left sign-up-left">
                    <div class="logo-container">
                        <img src="{{asset('images/main-logo.png')}}" class="img-fluid" alt="">
                    </div>
                    <div class="log-in-content">
                        <h1>Verify account</h1>
                        <p>Please enter verification code to continue.</p>
                    </div>
                    <div class="log-in-foam">
                        <form class="form-horizontal form-simple"  action="{{ route('verify.code') }}" method="post">
                            @csrf
                            <div class="cridentials">
                                <label for="exampleInputEmail1" class="form-label">Verification code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" placeholder="Enter code" value="{{ old('code') }}" autofocus
                                       id="exampleInputEmail1" aria-describedby="emailHelp">
                                <span class="icons">
                                    <img src="{{asset('images/icon-1.png')}}" class="img-fluid" alt="">
                                </span>
                                @if (\Session::has('code'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{!! \Session::get('code') !!}</strong>
                                    </span>
                                @endif
                            </div>

                            <button type="submit" class="btn custom-btn-login">Submit</button>


                            <div class="Need-Help">
                                <p>
                                    Need Help?
                                    <span>
                                <a href="#">Contact Us</a>
                            </span>
                                </p>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 p-0 slider-main-colum" id="hide-on-mobile">
                <div class="slider-main" style="background-image: url('{{asset("images/crm-main.jpg")}}') !important;">
                </div>
            </div>
        </div>
    </div>
</section>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
<script>
    var selector = '.main-id-date p';

    $(selector).on('click', function () {
        $(selector).removeClass('active');
        $(this).addClass('active');
    });
</script>

<script>
    // Wait for the DOM to load
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('redirectButton');

        if (button) {
            button.addEventListener('click', function() {
                const currentPath = window.location.pathname;

                // Check if we're on 'index.php' (or root page)
                if (currentPath === '/index.php' || currentPath === '/' || currentPath.endsWith('/index.php')) {
                    // Redirect to 'messages.php'
                    window.location.href = "messages.php";
                }
                // Check if we're on 'messages.php'
                else if (currentPath === '/messages.php' || currentPath.endsWith('/messages.php')) {
                    // Redirect to 'index.php'
                    window.location.href = "index.php";
                }
            });
        }
    });
</script>


</body>

</html>

