<?php include "include/header.php" ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  </head>
  <body>
<?php include "include/menu.php" ?>    


<div class="main-side-menu">
    <?php include "include/side-menu.php" ?>    
<div class="for-slider-main-banner">
<section class="chat-integrate">
        <div class="container-fluid">
            <div class="row for-main-border align-items-center">
                <div class="col-lg-3">

                </div>
                <div class="col-lg-6">
                    <div class="container contact-tab">
                        <ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="Conversations-tab" data-bs-toggle="tab"
                                    data-bs-target="#Conversations" type="button" role="tab"
                                    aria-controls="Conversations" aria-selected="true">Conversations
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="Shortcodes-tab" data-bs-toggle="tab"
                                    data-bs-target="#Shortcodes" type="button" role="tab" aria-controls="Shortcodes"
                                    aria-selected="false">Shortcodes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                                    type="button" role="tab" aria-controls="contact" aria-selected="false">Contact
                                </button>
                            </li>
                        </ul>


                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="icon-info">
                        <div class="bell-img">
                            <a href="javascript:;">
                                <img src="images/icon.png" class="img-fluid">
                                <span></span>
                            </a>
                        </div>
                        <a href="javascript:;">
                            <img src="images/circle.png" class="img-fluid">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="Conversations" role="tabpanel"
                            aria-labelledby="Conversations-tab">
                            <div class="row">
                                <div class="col-lg-3 p-0">
                                    <div class="main-client-details">
                                        <div class="search-container">
                                            <input type="text" class="form-control search-input"
                                                placeholder="Search...">
                                            <i class="fas fa-search search-icon"></i>
                                        </div>
                                        <h3>All Conversations</h3>
                                        <div class="container contact-tab">
                                            <ul class="nav nav-tabs" id="myTab1" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="Alexandra-tab"
                                                        data-bs-toggle="tab" data-bs-target="#Alexandra" type="button"
                                                        role="tab" aria-controls="Alexandra" aria-selected="true">
                                                        <div class="client-info-detail">
                                                            <div class="client-profile">
                                                                <img src="images/circle.png" class="img-fluid">
                                                                <span></span>
                                                            </div>
                                                            <div class="client-content">
                                                                <h4>Alexandra</h4>
                                                                <p>Etiam eget metus eget...</p>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="Sofia-tab" data-bs-toggle="tab"
                                                        data-bs-target="#Sofia" type="button" role="tab"
                                                        aria-controls="Sofia" aria-selected="true">
                                                        <div class="client-info-detail">
                                                            <div class="client-profile">
                                                                <img src="images/circle.png" class="img-fluid">
                                                                <span></span>
                                                            </div>
                                                            <div class="client-content">
                                                                <h4>Sofia</h4>
                                                                <p>Etiam eget metus eget...</p>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="Mariana-tab" data-bs-toggle="tab"
                                                        data-bs-target="#Mariana" type="button" role="tab"
                                                        aria-controls="Mariana" aria-selected="true">
                                                        <div class="client-info-detail">
                                                            <div class="client-profile">
                                                                <img src="images/circle.png" class="img-fluid">
                                                                <span></span>
                                                            </div>
                                                            <div class="client-content">
                                                                <h4>Mariana</h4>
                                                                <p>Etiam eget metus eget...</p>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="Cortana-tab" data-bs-toggle="tab"
                                                        data-bs-target="#Cortana" type="button" role="tab"
                                                        aria-controls="Cortana" aria-selected="true">
                                                        <div class="client-info-detail">
                                                            <div class="client-profile">
                                                                <img src="images/circle.png" class="img-fluid">
                                                            </div>
                                                            <div class="client-content">
                                                                <h4>Cortana</h4>
                                                                <p>Etiam eget metus eget...</p>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </li>
                                            </ul>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 p-0">
                                    <div class="tab-content" id="myTabContent1">
                                        <div class="tab-pane fade show active" id="Alexandra" role="tabpanel"
                                            aria-labelledby="Alexandra-tab">
                                            <div class="single-client-full-detail">
                                                <div class="client-brief">
                                                    <div class="chat-person">
                                                        <h4>To: Alexandra</h4>
                                                        <a href="javascript:;">
                                                            <img src="images/more-icon.png" class="img-fluid">
                                                        </a>
                                                    </div>
                                                    <div class="main-chat-message">
                                                        <div class="message-img">
                                                            <img src="images/circle.png" class="img-fluid">
                                                        </div>
                                                        <div class="message-content">
                                                            <div class="message-line">
                                                                <p>Donec et eleifend neque lectus ac mauris ornare
                                                                    molestie.</p>
                                                            </div>
                                                            <div class="message-time">
                                                                <span>10:00 AM, Today</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="main-chat-message for-reply">
                                                        <div class="message-content">
                                                            <div class="message-line">
                                                                <p>Quisque vestibulum, mi vel molestie pulvinar neque
                                                                    risus laoreet augue necades laoreet odio augue
                                                                    luctus neque nunc bibendum.
                                                                    efficitur tortor dapibus molestie.
                                                                </p>
                                                            </div>
                                                            <div class="message-time">
                                                                <span>10:25 AM, Today</span>
                                                            </div>
                                                        </div>
                                                        <div class="message-img">
                                                            <img src="images/circle.png" class="img-fluid">
                                                        </div>
                                                    </div>
                                                    <div class="main-chat-message">
                                                        <div class="message-img">
                                                            <img src="images/circle.png" class="img-fluid">
                                                        </div>
                                                        <div class="message-content">
                                                            <div class="message-line">
                                                                <p>Donec et eleifend neque lectus ac mauris ornare
                                                                    molestie.</p>
                                                            </div>
                                                            <div class="message-time">
                                                                <span>11:00 AM, Today</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="main-chat-message for-reply">
                                                        <div class="message-content">
                                                            <div class="msg-img">
                                                                <img src="images/square-icon.png" class="img-fluid">
                                                                <img src="images/square-icon.png" class="img-fluid">
                                                                <img src="images/square-icon.png" class="img-fluid">
                                                            </div>
                                                            <div class="message-time">
                                                                <span>11:25 AM, Today</span>
                                                            </div>
                                                        </div>
                                                        <div class="message-img">
                                                            <img src="images/circle.png" class="img-fluid">
                                                        </div>
                                                    </div>
                                                    <div class="main-chat-message">
                                                        <div class="message-img">
                                                            <img src="images/circle.png" class="img-fluid">
                                                        </div>
                                                        <div class="message-content">
                                                            <div class="message-line">
                                                                <p>Alexandra is typing <span><img
                                                                            src="images/printing.png"
                                                                            class="img-fluid"></span></p>
                                                            </div>
                                                            <div class="message-time">
                                                                <span>11:00 AM, Today</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="for-sending">
                                                        <a href="javascript:;">
                                                            <img src="images/smile.png" class="img-fluid">
                                                        </a>
                                                        <input type="text" placeholder="Type message here...">
                                                        <a href="javascript:;">
                                                            <img src="images/file.png" class="img-fluid">
                                                        </a>
                                                        <button type="submit">
                                                            <img src="images/btn.png" class="img-fluid">
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="single-client">
                                                    <div class="for-cross">
                                                        <a href="javascript:;">
                                                            <img src="images/close-icon.png" class="img-fluid">
                                                        </a>
                                                    </div>
                                                    <div class="client-info-detail">
                                                        <div class="client-profile">
                                                            <img src="images/circle.png" class="img-fluid">
                                                            <a href="javascript:;">
                                                                <span></span>
                                                            </a>
                                                        </div>
                                                        <div class="client-content">
                                                            <h4>Alex Alexandrov</h4>
                                                            <p>Lorem, Lipsum</p>
                                                        </div>
                                                    </div>
                                                    <div class="container contact-tab new-setting-tab">
                                                        <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="Files-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#Files"
                                                                    type="button" role="tab" aria-controls="Files"
                                                                    aria-selected="true">Files
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="Setting-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#Setting"
                                                                    type="button" role="tab" aria-controls="Setting"
                                                                    aria-selected="false">Setting
                                                                </button>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content" id="myTabContent2">
                                                            <div class="tab-pane fade show active" id="Files"
                                                                role="tabpanel" aria-labelledby="Files-tab">
                                                                <h4>Recent files</h4>
                                                                <a href="javascript:;">
                                                                    <div class="for-files">
                                                                        <img src="images/music.png" class="img-fluid">
                                                                        <h5>Sound of Freedom.mp3</h5>
                                                                    </div>
                                                                </a>
                                                                <a href="javascript:;">
                                                                    <div class="for-files">
                                                                        <img src="images/project-file.png"
                                                                            class="img-fluid">
                                                                        <h5>Project.zip</h5>
                                                                    </div>
                                                                </a>
                                                                <a href="javascript:;">
                                                                    <div class="for-files">
                                                                        <img src="images/loop.png" class="img-fluid">
                                                                        <h5>Project logos.eps</h5>
                                                                    </div>
                                                                </a>
                                                                <h4>Uploaded Photos</h4>
                                                                <div class="upload-photos">
                                                                    <img src="images/square-icon.png" class="img-fluid">
                                                                    <img src="images/square-icon.png" class="img-fluid">
                                                                    <img src="images/square-icon.png" class="img-fluid">
                                                                </div>
                                                                <div class="upload-photos">
                                                                    <img src="images/square-icon.png" class="img-fluid">
                                                                    <img src="images/square-icon.png" class="img-fluid">
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="Setting"
                                                                role="tabpanel" aria-labelledby="Setting-tab">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="Sofia" role="tabpanel"
                                            aria-labelledby="Sofia-tab">
                                            <div class="single-client-full-detail">
                                                <div class="client-brief">

                                                </div>
                                                <div class="single-client">
                                                    <div class="for-cross">
                                                        <a href="javascript:;">
                                                            <img src="images/close-icon.png" class="img-fluid">
                                                        </a>
                                                    </div>
                                                    <div class="client-info-detail">
                                                        <div class="client-profile">
                                                            <img src="images/circle.png" class="img-fluid">
                                                            <a href="javascript:;">
                                                                <span></span>
                                                            </a>
                                                        </div>
                                                        <div class="client-content">
                                                            <h4>Sofia</h4>
                                                            <p>Lorem, Lipsum</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="Shortcodes" role="tabpanel" aria-labelledby="Shortcodes-tab">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua. Aliquam id diam maecenas ultricies mi
                                eget mauris pharetra. Tincidunt lobortis feugiat vivamus at augue eget. Aliquet
                                porttitor lacus luctus accumsan tortor posuere ac ut consequat. Massa massa
                                ultricies mi quis hendrerit dolor.
                            </p>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <p>
                                I love cheese, especially stinking bishop cheese and biscuits. Stinking bishop
                                cheesy feet brie fromage red leicester taleggio cut the cheese who moved my cheese.
                                Red leicester cow hard cheese cheese slices cheese strings goat camembert de
                                normandie cheesy grin. Gouda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>
    </div>

<?php include"include/footer.php" ?>

<script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>
</script>