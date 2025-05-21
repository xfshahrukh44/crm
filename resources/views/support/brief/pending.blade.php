@extends('layouts.app-support')
@push('style')
@endpush
@section('content')
    <div class="breadcrumb">
        <h1 class="mr-2">Brief Pending</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-body">
                    <h4 class="card-title mb-3">Clients with briefs pending</h4>

                    <div class="table-responsive">
                        <table class="display table table-striped table-bordered datatable-init" style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Briefs pending</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($client_users_with_brief_pendings as $user)
                                @if($user->client)
                                    <tr>
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->name}} {{$user->last_name}}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </a>
                                            <span class="content_click_to_view" hidden>
                                                {{$user->email}}
                                            </span>
                                        </td>
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
                                                <span class="btn btn-info btn-sm">{{$brief_pending}}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Briefs pending</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            if($('.datatable-init').length != 0){
                $('.datatable-init').DataTable({
                    order: [[0, "desc"]],
                    responsive: true,
                });
            }

            $('.btn_click_to_view').on('click', function () {
                $('.btn_click_to_view').each((i, item) => {
                    $(item).prop('hidden', false);
                });

                $('.content_click_to_view').each((i, item) => {
                    $(item).prop('hidden', true);
                });

                $(this).prop('hidden', true);
                $(this).parent().find('.content_click_to_view').prop('hidden', false);
            });
        });
    </script>
@endpush
