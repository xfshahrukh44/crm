<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <title>Checkout invoice</title>
</head>

<body>


    <header>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <nav class="navbar">
                        <a class="navbar-brand"><img src="images/crm-logo.png" class="img-fluid" alt=""></a>
                        <form class="d-flex side-profile">
                            <div class="profile-dropdown">
                                <a href="javascript:;" class="profile_dropdown"><img src="images/profile.png"
                                        class="img-fluid" alt="">Jason
                                    Martin <i class="fa-solid fa-caret-down"></i></a>
                                <ul class="profile_menu">
                                    <li><a href="#">Lorem lipsum</a></li>
                                    <li><a href="#">Lorem lipsum</a></li>
                                    <li><a href="#">Lorem lipsum</a></li>
                                </ul>
                            </div>
                            <div class="search-here">
                                <input class="form-control me-2 " type="search" placeholder="Search here..."
                                    aria-label="Search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <div class="notification-show">
                                <a href="#"><img src="images./notification.png" alt=""></a>
                            </div>
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </header>


    <section class="checkout-pg">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="invoice-record">
                        <form action="" class="invoice-form">
                            <div class="row">
                                <div class="col-12">
                                    <div class="main-invoice">
                                        <div class="summry-form">
                                            <div class="summry-field">
                                                <div class="col-12">
                                                    <div class="writer-invoice">
                                                        <div class="writer-logo">
                                                            <img src="images/writer-logo.png" class="img-fluid" alt="">
                                                        </div>
                                                        <div class="invoice-number">
                                                            <p>Invoice # 2022-7056</p>
                                                        </div>
                                                    </div>
                                                    <div class="fill-summry">
                                                        <label>Summary</label>
                                                        <input type="text" class="form-control" placeholder="">
                                                    </div>
                                                    <div class="pakages-summry">
                                                        <ul class="custom-pakages">
                                                            <li>
                                                                <p>Pacakage </p>
                                                                <span>custom</span>
                                                            </li>
                                                            <li>
                                                                <p>Brand </p>
                                                                <span>Writers Publishing Lab</span>
                                                            </li>
                                                            <li>
                                                                <p>Service(s)</p>
                                                                <span><button class="btn book-btn-market">Book
                                                                        Marketing</button></span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custom-pakages">
                                                            <li>
                                                                <p>Payment type </p>
                                                                <span>One-Time Charge
                                                                </span>
                                                            </li>
                                                            <li>
                                                                <p>Amount</p>
                                                                <span>$ 799
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="card-payment">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label>Card number</label>
                                                                <input type="text" name="exp_month"
                                                                    class="form-control">
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-12">
                                                                <label>Expiration month</label>
                                                                <select name="exp_month" id="" class="form-control">
                                                                    <option value=""></option>
                                                                    <option value="">1</option>
                                                                    <option value="">2</option>
                                                                    <option value="">3</option>
                                                                    <option value="">4</option>
                                                                    <option value="">5</option>
                                                                    <option value="">6</option>
                                                                    <option value="">7</option>
                                                                    <option value="">8</option>
                                                                    <option value="">9</option>
                                                                    <option value="">10</option>
                                                                    <option value="">11</option>
                                                                    <option value="">12</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-12">
                                                                <label>Expiration year</label>
                                                                <input type="number" name="exp_year"
                                                                    class="form-control">
                                                            </div>
                                                            <div class="col-lg-2 col-md-2 col-12">
                                                                <label>CVV</label>
                                                                <input type="number" name="cvv" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="billings-info">
                                            <h3>Billing information</h3>
                                            <div class="card-payment">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label>Expiration month</label>
                                                        <select name="exp_month" id="" class="form-control">
                                                            <option value="">Select month</option>
                                                            <option value="">1</option>
                                                            <option value="">2</option>
                                                            <option value="">3</option>
                                                            <option value="">4</option>
                                                            <option value="">5</option>
                                                            <option value="">6</option>
                                                            <option value="">7</option>
                                                            <option value="">8</option>
                                                            <option value="">9</option>
                                                            <option value="">10</option>
                                                            <option value="">11</option>
                                                            <option value="">12</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>Country</label>
                                                        <select name="country" id="" class="form-control">
                                                            <option value="">Select country</option>
                                                            <option value="">Afghanistan</option>
                                                            <option value="">Albania</option>
                                                            <option value="">American Samoa</option>
                                                            <option value="">Austria</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>City</label>
                                                        <input type="text" name="city" class="form-control">
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>State</label>
                                                        <input type="text" name="state" class="form-control">
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <label>Zip Code</label>
                                                        <input type="number" name="zip" class="form-control">
                                                    </div>
                                                    <div class="billing-pay-btn">
                                                        <button type="submit" class="btn pay-btn">$799 Pay</button>
                                                    </div>
                                                </div>
                                            </div>
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

    <!-- Optional JavaScript; choose one of the two! -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>


    <script src="js/script.js"></script>

</body>

</html>