<?php include"include/header.php" ?>

  </head>
  <body>
<?php include "include/menu.php" ?>    

<div class="main-side-menu">
    <?php include "include/side-menu.php" ?>    
<div class="for-slider-main-banner">
<section class="payment-detail">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="payment-detail-info">
                    <div class="main-payment-heading">
                        <h2>Payment Details Form</h2><span>*</span>
                        <h6>indicates mandatory</h6>
                    </div>
                    <form action="">
                        <div class="row">
                            <div class="col-12">
                                <div class="for-check-payment">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                        <label class="form-check-label" for="checkDefault">
                                           is recurring payment
                                        </label>
                                    </div>
                                </div>
                                <div class="payment-form-fill">
                                    <div class="form-group">
                                        <label>First Name <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Contact <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-form-fill">
                                    <div class="form-group new-service-form">
                                        <label>Brand Name <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Service <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                        <div class="for-service-check for-check-payment">
                                            <p>Select which forms the client will be</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkDefault">
                                                <label class="form-check-label" for="checkDefault">
                                                    Logo Design
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkDefault">
                                                <label class="form-check-label" for="checkDefault">
                                                    Web Design
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkDefault">
                                                <label class="form-check-label" for="checkDefault">
                                                    Logo Design
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkDefault">
                                                <label class="form-check-label" for="checkDefault">
                                                    Web Design
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-form-fill">
                                    <div class="form-group">
                                        <label>Package <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Create form of Service <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Name for a Custom Package</label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-form-fill">
                                    <div class="form-group">
                                        <label>Currency <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Payment Type <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Merchant <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Send Email to Customer <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-form-fill">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="" id="textarea" rows="7" class="form-control"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-form-fill">
                                    <div class="form-group">
                                        <label>Specify Agent</label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Recurring amount</label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Sell/Upsell <span>*</span></label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                    <button type="submit">
                                        CREATE INNVOICE
                                    </button>
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

<?php include"include/footer.php" ?>