<?php include 'include/header.php' ?>
<?php include 'include/menu.php' ?>

<section class="dashboard my">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="parent-dashboard-card">
                    <div class="dashboard-cards active">
                        <figure>
                            <img src="images/card-icon-1.png" class="img-fluid" alt="">
                        </figure>
                        <h2 class="heading-2">
                            <span class="d-block">
                                Welcome To
                            </span>
                            Design Crm
                        </h2>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur dipiscing elit sed do eiusmod tempor incididunt
                            ultrices
                            gravida.
                        </p>
                        <a href="setup-profile.php" class="btn custom-btn transparent">Setup Profile</a>
                    </div>
                    <div class="dashboard-cards">
                        <figure>
                            <img src="images/card-icon-2.png" class="img-fluid" alt="">
                        </figure>
                        <h2 class="heading-2">
                            <span class="d-block">
                                Get Started
                            </span>
                            Your Project
                        </h2>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur dipiscing elit sed do eiusmod tempor incididunt
                            ultrices
                            gravida.
                        </p>
                        <a href="setup-profile.php" class="btn custom-btn transparent">Setup Profile</a>
                    </div>
                    <div class="dashboard-cards">
                        <figure>
                            <img src="images/card-icon-3.png" class="img-fluid" alt="">
                        </figure>
                        <h2 class="heading-2">
                            <span class="d-block">
                                Find Your
                            </span>
                            Invoices
                        </h2>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur dipiscing elit sed do eiusmod tempor incididunt
                            ultrices
                            gravida.
                        </p>
                        <a href="setup-profile.php" class="btn custom-btn transparent">Setup Profile</a>
                    </div>
                </div>
                <div class="chart-parent">
                    <div class="chart-header">
                        <h3 class="heading-2">
                            Progress
                        </h3>
                        <select class="form-select" aria-label="Default select example">
                            <option selected>01-07 May</option>
                            <option value="1">01-07 June</option>
                            <option value="2">01-07 July</option>
                            <option value="3">01-07 Auguest</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="myChart" width="100%"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <?php include 'include/messages.php' ?>
            </div>
        </div>
    </div>
</section>




<?php include 'include/footer.php' ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>

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
        type: "line",
        data: {
            labels: ["00:00 AM", "01:00 AM", "02:00 AM", "03:00 AM", "04:00 AM", "05:00 AM", "06:00 AM", "07:00 AM", "01:00 AM", "09:00 AM"],
            datasets: [
                {
                    label: "Checking",
                    data: checkingData,
                    backgroundColor: ["rgba(113, 88, 203, .15)"],
                    borderColor: ["rgba(113, 88, 203, 1)"],
                    borderWidth: 1,
                    fill: "start"
                },
                {
                    label: "Savings",
                    data: savingsData,
                    backgroundColor: ["rgba(161, 201, 249, .15)"],
                    borderColor: ["rgba(161, 201, 249, 1)"],
                    borderWidth: 1,
                    fill: "start"
                }
            ]
        },
        options: {
            animation: {
                duration: 250
            },
            tooltips: {
                intersect: false,
                backgroundColor: "rgba(113, 88, 203, 1)",
                titleFontSize: 16,
                titleFontStyle: "400",
                titleSpacing: 4,
                titleMarginBottom: 8,
                bodyFontSize: 12,
                bodyFontStyle: '400',
                bodySpacing: 4,
                xPadding: 8,
                yPadding: 8,
                cornerRadius: 4,
                displayColors: false,
                callbacks: {
                    title: function (t, d) {
                        const o = d.datasets.map((ds) => "$" + ds.data[t[0].index])

                        return o.join(', ');
                    },
                    label: function (t, d) {
                        return d.labels[t.index];
                    }
                }
            },
            title: {
                text: "Progress",
                display: false
            },
            maintainAspectRatio: true,
            spanGaps: false,
            elements: {
                line: {
                    tension: 0.3
                }
            },
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                xAxes: [
                    {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0
                        }
                    }
                ]
            }
        }
    });

</script>
