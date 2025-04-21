<h1>{{ $title }}</h1>
<p>{{ $body }}</p>

@if($last_login_ip)
    <p>IP: {{ $last_login_ip }}</p>
@endif

@if($last_login_device)
    <p>Device: {{ $last_login_device }}</p>
@endif
