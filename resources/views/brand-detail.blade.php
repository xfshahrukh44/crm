@extends($layout)
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
                                    <b>Sales</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($buhs as $buh)
                                        <a href="mailto:{{$buh->email}}">
                                            <h6>{{$buh->name . ' ' . $buh->last_name}}</h6>
                                        </a>
                                        <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-md-6 offset-md-3">
                                <h6>
                                    <b>Project managers</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($agents as $agent)
                                        <a href="mailto:{{$agent->email}}">
                                            <h6>{{$agent->name . ' ' . $agent->last_name}}</h6>
                                        </a>
                                        <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="breadcrumb">
            <h1 class="mr-2">Clients</h1>
        </div>
        <div class="separator-breadcrumb border-top"></div>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card text-left">
                    <div class="card-body">
                        <form action="{{ route('brands.detail', $brand->id) }}" method="GET">
                            <div class="row">
                                <div class="col-md-12 form-group mb-3">
                                    <label for="package">Search client</label>
                                    <input type="text" class="form-control" id="client_name" name="client_name" value="{{ Request::get('client_name') }}" placeholder="Client information">
                                </div>
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <button class="btn btn-primary">Search Result</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card text-left">
                    <div class="card-body">
                        <h4 class="card-title mb-3 count-card-title">Clients list <span> Total: {{ $clients->total() }} </span></h4>
                        <div class="table-responsive">
                            <table class="display table table-striped table-bordered" style="width:100%" id="display">
                                <thead>
                                <tr>
{{--                                    <th>ID</th>--}}
                                    <th>Client</th>
                                    <th>Invoices</th>
                                    <th>Projects</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr>
{{--                                        <td><span class="btn btn-primary btn-sm">#{{ $client->id }}</span></td>--}}
                                        <td>
                                            <a target="_blank" href="{{route('clients.detail', $client->id)}}">
                                                <span class="btn btn-primary btn-sm">{{$client->name . ' ' . $client->last_name}}</span>
                                            </a>
                                        </td>
                                        <td>{{count($client->invoices)}}</td>
                                        <td>{{count($client->projects)}}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                                <tfoot>
                                <tr>
{{--                                    <th>ID</th>--}}
                                    <th>Client</th>
                                    <th>Invoices</th>
                                    <th>Projects</th>
                                </tr>
                                </tfoot>
                            </table>
{{--                            <div class="ajax-loading"><img src="{{ asset('newglobal/images/loader.gif') }}" /></div>--}}
                            {{ $clients->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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