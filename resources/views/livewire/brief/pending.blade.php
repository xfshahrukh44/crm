<div>
    <div class="breadcrumb">
        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">
            <i class="fas fa-arrow-left"></i>
        </a>
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

    <script>
        $(document).ready(function(){
            if($('.datatable-init').length != 0){
                $('.datatable-init').DataTable({
                    order: [[0, "desc"]],
                    responsive: true,
                });
            }
        });
    </script>
</div>