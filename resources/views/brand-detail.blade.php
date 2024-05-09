@extends('layouts.app-admin')
@section('title', 'Brand detail')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /*img {*/
        /*    max-width: 50px;*/
        /*}*/

        .card-body.text-center {
            min-height: 150px;
        }

        p.text-muted.mt-2.mb-2 {
            font-size: 15px;
        }

        .card-body.text-center:hover {
            box-shadow: 0px 0px 15px 8px #00aeee1a;
        }
    </style>
@endpush
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Brand detail</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        {{--brand detail--}}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <img src="{{asset($brand->logo)}}" alt="">
                    </div>
                </div>
                <div class="row text-center mb-4">
                    <div class="col-md-6 offset-md-3">
                        <h2>{{$brand->name}}</h2>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <h4>
                            <i class="fas fa-phone"></i>
                            <a href="tel:{{$brand->phone}}">{{$brand->phone}}</a>
                        </h4>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <h4>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{$brand->email}}">{{$brand->email}}</a>
                        </h4>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <h4>
                            <i class="fas fa-link"></i>
                            <a target="_blank" href="{{$brand->url}}">{{$brand->url}}</a>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-6 offset-md-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-md-6 offset-md-3">
                                <h6>
                                    <b>BUH(s)</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 offset-md-3">
                                @foreach($buhs as $buh)
                                    <h6>{{$buh->name . ' ' . $buh->last_name}}</h6>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-md-6 offset-md-3">
                                <h6>
                                    <b>Agents</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 offset-md-3">
                                @foreach($agents as $agent)
                                    <h6>{{$agent->name . ' ' . $agent->last_name}}</h6>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{--            <form action="{{route('brands.detail', $brand->id)}}" method="GET">--}}
        <div class="row mb-4">
            <div class="col-lg-12 col-md-12">
                <h2 class="ml-3">Clients</h2>
            </div>
{{--                <div class="col-lg-11 col-md-11">--}}
{{--                    <input type="text" class="form-control" placeholder="Search client" name="client_name" value="" id="input_client_name">--}}
{{--                </div>--}}
{{--                <div class="col-lg-1 col-md-1">--}}
{{--                    <button type="submit" class="btn btn-primary btn-block">Search</button>--}}
{{--                </div>--}}
        </div>
{{--            </form>--}}
        <!-- CARD ICON-->
        <div class="row client_wrapper">
            @foreach($clients as $client)
                <div class="col-lg-2 col-md-6 col-sm-6">

                    <a target="_blank" href="{{route('clients.detail', $client->id)}}">
                        <div class="card card-icon mb-4">
                            <div class="card-body text-center">
{{--                                <img src="{{asset($client->logo)}}" alt="">--}}
                                <p class="text-muted mt-2 mb-2">{{$client->name . ' ' . $client->last_name}}</p>
                                <small class="text-muted mt-2 mb-2">Invoices: {{count($client->invoices)}} | Projects: {{count($client->projects)}}</small>
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{$brand->name}}</p>--}}
                            </div>
                        </div>
                    </a>

                </div>
            @endforeach
        </div>
    </div>
{{--    <div class="col-lg-12 col-md-12">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-12 col-md-12">--}}
{{--                <div class="card card-chart-bottom o-hidden mb-4">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="text-muted">123, 456 Sales</div>--}}
{{--                        <p class="mb-4 text-primary text-24">$123,456</p>--}}
{{--                    </div>--}}
{{--                    <div id="echartBar" style="height: 400px;"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
<script>
    {{--var data = {!! json_encode($data->toArray()) !!};--}}
    {{--var data_array = [];--}}
    {{--var data_day = [];--}}
    {{--for(var i = 0; i < data.length; i++){--}}
    {{--    data_array.push(data[i].amount);--}}
    {{--    data_day.push(data[i].invoice_date);--}}
    {{--}--}}

    $(document).ready(function() {
        function render_clients () {
            let clients = {!! $clients !!};

            let clients_to_render = clients;
            let client_name = $('#input_client_name').val();
            if (client_name != '') {
                clients_to_render.filter((item) => {
                    return ((item.name && item.name.includes(client_name)) || (item.last_name && item.last_name.includes(client_name)));
                });
            }

            $('.client_wrapper').html();
            for (const client of clients_to_render) {
                $('.client_wrapper').append(`<div class="col-lg-2 col-md-6 col-sm-6">
                                                <a href="#">
                                                    <div class="card card-icon mb-4">
                                                        <div class="card-body text-center">
                                                            <p class="text-muted mt-2 mb-2">` + client.name + ` ` + client.last_name +`</p>
                                                            <small class="text-muted mt-2 mb-2">Invoices: ` + client.invoices.length + ` | Projects: ` + client.projects.length + `</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>`);

            }
        }


        $('#input_client_name').on('keyup', function () {
            render_clients();
        });
        console.log(clients);
        alert(clients);
    });
</script>
@endpush