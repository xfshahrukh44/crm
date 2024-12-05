<?php include 'include/header.php' ?>
<?php include 'include/menu.php' ?>

<section class="profile-section">
    <div class="container bg-colored">
        <div class="row align-items-center">
            <div class="col-lg-4">
                <div class="profile-left-parent doted-border">
                    <p class="heading-3">
                        Profile Picture
                    </p>
                    <figure class="large-profile">
                        <img src="images/large-profile-pic.png" class="img-fluid" alt="">
                    </figure>
                    <span class="upload-profile-pic">
                    <label for="file-upload" class="custom-file-upload">
                        <figure>
                            <img src="images/upload-icon.png" class="img-fluid" alt="">
                        </figure>
                        <p>
                            Drag and Upload files here
                        </p>
                    </label>
                    <input id="file-upload" type="file">
                    </span>
                    
                    <p class="choice-profile">
                        or
                    </p>
                    <span class="btn custom-btn black">
                    <label for="file-upload" class="custom-file-upload">
                        Browse File
                    </label>
                    <input id="file-upload" type="file">
                    </span>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="profile-right-parent">
                    <p class="heading-3">
                        Profile Details
                    </p>
                    <div class="parent-profile-details">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1"
                                            placeholder="Jason">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="exampleFormControlInput1"
                                            placeholder="Jasonmartin@gmail.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1"
                                            placeholder="987 654 321">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1"
                                            placeholder="Martin">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Business Email</label>
                                        <input type="email" class="form-control" id="exampleFormControlInput1"
                                            placeholder="Jasonmartin@domain.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Business Phone</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1"
                                            placeholder="987 654 321">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1"
                                            placeholder="987 654 321">
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

<?php include 'include/footer.php' ?>