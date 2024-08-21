<div wire:loading.delay>
    <style>
        .loadermain {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            margin: auto;
            background-color: #ffffff;
            z-index: 999999;
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="loadermain" id="loadermain">
        <img src="{{ url('images/LOGO.png') }}" class="img-fluid" alt="">
    </div>
</div>
