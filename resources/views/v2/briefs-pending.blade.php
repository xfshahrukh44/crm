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
                                            <span class="btn btn-info btn-sm">{{$user->client?->brand->name}}</span>
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
    </div>
@endsection

@section('script')

@endsection
