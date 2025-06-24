@extends('v2.layouts.app')

@section('title', 'Merchants')

@section('css')
    <style>
        #zero_configuration_table td {
            word-break: break-all;
            max-width: 300px; /* adjust as needed */
            white-space: normal;
        }

        /*#zero_configuration_table th,*/
        /*#zero_configuration_table td {*/
        /*    vertical-align: middle;*/
        /*}*/
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                @php
                    $merchants = \App\Models\Merchant::get();
                @endphp
                <section class="list-0f">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing table-responsive">
                                    <div class="row text-left pr-3 pb-2">
                                        <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                            <h1 style="font-weight: 100;">Merchants</h1>
                                        </div>
                                        <div class="col-md-6 m-auto d-flex justify-content-end">
                                            <a href="{{route('v2.merchants.create')}}" class="btn btn-sm btn-success">
                                                <i class="fas fa-plus"></i>
                                                Create
                                            </a>
                                        </div>
                                    </div>

                                    <table id="zero_configuration_table" style="width: 100%;">
                                        <thead>

                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Public key</th>
                                            <th>Secret key</th>
                                            <th>Merchant</th>
                                            <th>Status</th>
                                            <th>Action</th>

                                        </thead>
                                        <tbody>
                                            @foreach($merchants as $merchant)
                                                <tr>
                                                    <td>{{$merchant->id}}</td>
                                                    <td>{{$merchant->name}}</td>
                                                    <td style="width: 20%;">{{$merchant->public_key}}</td>
                                                    <td style="width: 20%;">{{$merchant->secret_key}}</td>
                                                    <td>
                                                        <div class="badge badge-sm bg-secondary text-white">
                                                            {{ $merchant->is_authorized == 0 ? 'STRIPE' : 'AUTHORIZE.NET' }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($merchant->status == 1)
                                                            <div class="badge badge-sm bg-success text-white">Active</div>
                                                        @else
                                                            <div class="badge badge-sm bg-danger text-white">Deactive</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{route('v2.merchants.edit', $merchant->id)}}" class="badge bg-primary">
                                                            <i class="fas fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
{{--                                    <div class="d-flex justify-content-end mt-2">--}}
{{--                                        {!! $merchants->links() !!}--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="list-0f">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing">
                                    <table>
                                        <thead>

                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Public key</th>
                                        <th>Secret key</th>
                                        <th>Merchant</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td>123</td>
                                            <td>Merchant 1</td>
                                            <td>
                                                sadasdsadasdsadakjdyihd18272bd871bd82b
                                            </td>
                                            <td>
                                                sadasdsadasdsadakjdyihd18272bd871bd82b
                                            </td>
                                            <td>
                                                <div class="badge badge-sm bg-secondary text-white">STRIPE</div>
                                            </td>
                                            <td>
                                                <div class="badge badge-sm bg-danger text-white">Deactive</div>
                                            </td>
                                            <td>
                                                <a href="#" class="badge bg-primary">
                                                    <i class="fas fa-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
        @endswitch
    </div>
@endsection

@section('script')
    {{-- <script>
        if($('#zero_configuration_table').length != 0){
            $('#zero_configuration_table').DataTable({
                order: [[0, "asc"]],
                responsive: true // or keep it true if you want resizing but no column hiding
            });

        }
    </script> --}}
@endsection
