@extends('v2.layouts.app')

@section('title', 'Briefs pending')

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

    <style>
        .client-actions-box {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 100;
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
            <section class="list-0f">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-0f-head for-invoice-listing table-responsive">
                                <div class="row text-left pr-3 pb-2">
                                    <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                        <h1 style="font-weight: 100;">Briefs pending</h1>
                                    </div>
                                    <div class="col-md-6 m-auto d-flex justify-content-end">
                                        <a href="{{route('v2.clients.create')}}" class="btn btn-sm btn-success" hidden>
                                            <i class="fas fa-plus"></i>
                                            Create
                                        </a>
                                    </div>
                                </div>
                                <br>

                                <table id="zero_configuration_table" style="width: 100%;">
                                    <thead>

                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Briefs pending</th>

                                    </thead>
                                    <tbody>
                                    @foreach($client_users_with_brief_pendings as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>{{$user->name}} {{$user->last_name}}</td>
                                            <td>{{$user->email}}</td>
                                            <td>
                                                <span class="btn btn-info btn-sm">{{$user->client->brand->name}}</span>
                                            </td>
                                            <td>
                                                @if($user->status == 1)
                                                    <span class="btn btn-success btn-sm">Active</span><br>
                                                @else
                                                    <span class="btn btn-danger btn-sm">Deactive</span><br>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach(get_briefs_pending($user->id) as $brief_pending)
                                                    <span class="badge badge-primary badge-sm badge-pill">{{$brief_pending}}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end mt-2">
                                    {{ $client_users_with_brief_pendings->appends(request()->query())->links() }}
                                </div>
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

    <!-- Notes Modal -->
        <div class="modal fade" id="modal_show_notes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Notes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <textarea class="form-control" name="" id="textarea_notes" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row" id="div_modifier_info" hidden>
                            <div class="col-md-12">
                                <label for="">
                                    <b>Last updated by:</b>
                                </label>
                            </div>
                            <div class="col-md-12">
                                <label for="" id="label_modifier_info">
                                    asd asd (12-12-12 12:12 AM)
                                </label>
                            </div>
                        </div>
                    </div>
                    {{--            <div class="modal-footer">--}}
                    {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    {{--            </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection
