@extends('layouts.app-support')
@section('title', 'Pending Projects')
@push('styles')
<style>
    .select2-container{
        width: 100% !important;
    }
</style>
@endpush
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Pending Projects</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">Clients with pending projects</h4>

                <div class="table-responsive">
                    <table class="display table table-striped table-bordered datatable-init" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Brand</th>
                            <th>Status</th>
                            <th>Pending projects</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($client_users_with_pending_projects as $user)
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
                                        @foreach(get_pending_projects($user->id) as $pending_project)
                                            <h6>{{$pending_project['project_type']}}</h6>
                                            <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" onclick="assignAgent({{$pending_project['id']}}, {{$pending_project['form_number']}}, {{$pending_project['brand_id']}})">
                                                <span class="ul-btn__icon"><i class="i-Checked-User"></i></span>
                                                <span class="ul-btn__text">Assign</span>
                                            </a>
                                            <a href="{{ route('support.pending.project.details', ['id' => $pending_project['id'], 'form' => $pending_project['form_number']]) }}" class="btn btn-info btn-icon btn-sm">
                                                <span class="ul-btn__icon"><i class="i-Eye-Visible"></i></span>
                                                <span class="ul-btn__text">View</span>
                                            </a>
                                            <br />
                                            <br />
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
                            <th>Pending projects</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Assign Model -->
<div class="modal fade" id="assignModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="{{ route('support.assign.support') }}" method="post">
            @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="assign_id">
                    <input type="hidden" name="form" id="form_id">
                    <div class="form-group">
                        <label class="col-form-label" for="agent-name-wrapper">Name:</label>
                        <select name="agent_id" id="agent-name-wrapper" class="form-control">

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary ml-2" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });

    var brand_id = 0;

    let htmlTag = new Promise((resolve) => {
        var url = "{{ route('admin.client.agent', ":id") }}";
        url = url.replace(':id', brand_id);
        setTimeout(() => {
            $.ajax({
                type:'GET',
                url: url,
                success:function(data) {
                    var getData = data.data;
                    htmlTag = '<select id="MySelect" class="form-control select2">';
                    for (var i = 0; i < getData.length; i++) {
                        htmlTag += '<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>'
                    }
                    htmlTag += '</select>';
                    resolve(htmlTag);
                }
            });
        }, 1000)
    })

    function assignAgent(id, form, brand_id){
        $('#agent-name-wrapper').html('');
        var url = "{{ route('support.client.agent', ":id") }}";
        url = url.replace(':id', brand_id);
        $.ajax({
            type:'GET',
            url: url,
            success:function(data) {
                var getData = data.data;
                for (var i = 0; i < getData.length; i++) {
                    $('#agent-name-wrapper').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                }
            }
        });
        $('#assignModel').find('#assign_id').val(id);
        $('#assignModel').find('#form_id').val(form);
        $('#assignModel').modal('show');
    }

    $(document).ready(function(){
        if($('.datatable-init').length != 0){
            $('.datatable-init').DataTable({
                order: [[0, "desc"]],
                responsive: true,
            });
        }
    });
</script>
@endpush