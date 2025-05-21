<?php include "include/header.php" ?>
</head>

<body>
    <?php include "include/menu.php" ?>
    
    <div class="main-side-menu">
    <?php include "include/side-menu.php" ?>    
<div class="for-slider-main-banner">


    <section class="profile-pg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="profile-info">
                        <form action="">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="profile-img">
                                        <h6>Profile Picture</h6>
                                        <img src="images/profile_img.png" class="img-fluid" alt="">
                                        <div class="file-upload">
                                            <input name="file1" type="file" class="dropify" data-height="100" />
                                            <span>Or</span>
                                            <div class="browsw-img">
                                                <input type="file" name="file" id="file" class="inputfile"
                                                    data-multiple-caption="{count} files selected" multiple />
                                                <label for="file"><span>Browse File</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="profile-details">
                                        <h4>Profile Details</h4>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <input type="text" name="" class="form-control" placeholder="Jason"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="" class="form-control"
                                                        placeholder="Jasonmartin@gmail.com" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="number" name="" class="form-control"
                                                        placeholder="987 654 321" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <input type="text" name="" class="form-control" placeholder="Martin"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="" class="form-control"
                                                        placeholder="Business Email" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Business Phone</label>
                                                    <input type="number" name="" class="form-control"
                                                        placeholder="987 654 321" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <input type="text" name="" class="form-control" placeholder=""
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="brief-bttn">
                                                    <button class="btn brief-btn" type="submit">Submit Form</button>
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

</div>
</div>
    <?php include "include/footer.php" ?>