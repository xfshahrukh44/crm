<?php include 'include/header.php' ?>
<?php include 'include/menu.php' ?>

<section class="invoice-listing invoice">
    <div class="container bg-colored">
        <div class="row align-items-start invoice-listing-select-bar">
            <div class="col-lg-7">
                <div class="invoice-header">
                    <div class="left-invoice-header">
                        <h2 class="heading-3">
                            Invoice 2024
                        </h2>
                        <p>
                            Issued on 24th November 2024
                        </p>
                    </div>
                    <div class="right-invoice-header">
                        <button>
                            <img src="images/eye.png" class="img-fluid" alt="">
                        </button>
                        <button>
                            <img src="images/document-downloa.png" class="img-fluid" alt="">
                        </button>
                    </div>
                </div>
                <div class="summary">
                    <h3 class="heading-4">
                        Summary
                    </h3>

                    <table class="table mail-table border-0">
                        <thead>
                            <tr class="">
                                <th scope="col">To</th>
                                <th scope="col">Malik Babar</th>
                                <th scope="col">Malikbabar@technifiedlabs.com</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">From</th>
                                <td>Shahid Hussain</td>
                                <td>Shahidhussain@technifiedlabs.com</td>
                            </tr>
                            <tr>
                                <th scope="row">Notes</th>
                                <td colspan="2"><button class="btn pay-now">Thank you for your business</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border">

                </div>

                <div class="cost-break-down-table">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr class="colored-table-row">
                                <th scope="col">Cost Breakdown</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Design Landing Page</td>
                                <td class="text-end">$13,600.00</td>
                            </tr>
                            <tr>
                                <td>Development Landing Page</td>
                                <td class="text-end">$2,500.00</td>
                            </tr>
                            <tr>
                                <td>Testing & Improvements</td>
                                <td class="text-end">$550.00</td>
                            </tr>
                            <tr>
                                <td>Design Landing Page</td>
                                <td class="text-end">$13,600.00</td>
                            </tr>
                            <tr>
                                <td>Development Landing Page</td>
                                <td class="text-end">$2,500.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-5">

                <div class="paid">
                    <p> <span>$4,185.00</span></p>
                    <div class="paid-unpaid-drop-down">
                        <span class="tick-img">
                            <img src="images/tick-icon.png" alt="" class="img-fluid">
                        </span>
                        <select class="form-select" aria-label=".form-select-lg example">
                            <option selected>Paid</option>
                            <option value="2">No Paid</option>
                        </select>
                    </div>
                </div>

                <div class="main-id-card">

                    <div class="main-id-user">
                        <p><span class="icon"><i class="fa-regular fa-user"></i></span> <span class="text">Malik
                                Babar</span></p>
                        <p><span class="icon"><i class="fa-regular fa-calendar-days"></i></span> <span class="text">08
                                Nov 2024</span></p>
                        <p><span class="icon"><i class="fa-solid fa-dollar-sign"></i></span> <span class="text">Pay
                                by check or bank transfer</span></p>
                        <p><span class="icon"><i class="fa-regular fa-folder"></i></span> <span class="text">Landing
                                page design</span></p>
                        <p><span class="icon"><img src="images/link-img.png" class="img-fluid" alt=""></span>
                            <span class="text">https://dribbble.com/shots/19265438-Invoocy-Fintech-Dashboard</span>
                        </p>
                    </div>

                    <div class="main-id-date">
                        <p>
                            <span class="detail"><i class="fa-regular fa-circle"></i> Invoice created</span>
                            <span class="date">08 Jun 2024</span>
                        </p>
                        <p class="main-border">
                            <span class="detail"><i class="fa-regular fa-circle"></i> Invoice Sent</span>
                            <span class="date">10 Jun 2024</span>
                        </p>
                        <p class="active">
                            <span class="detail"><i class="fa-regular fa-circle"></i> Invoice Paid</span>
                            <span class="date">11 Jun 2024</span>
                        </p>
                    </div>

                    <div class="main-id-btn">
                        <button type="submit" class="btn submit-btn"><img src="images/cheaq-btn.png" alt=""
                                class="img-fluid"> Invoice Paid</button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>


<?php include 'include/footer.php' ?>