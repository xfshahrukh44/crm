@extends('v2.layouts.app')

@section('title', 'Pending projects')

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
                                    <h1 style="font-weight: 100;">Pending projects</h1>
                                </div>
                                <div class="col-md-6 m-auto d-flex justify-content-end">
                                    <a href="{{route('v2.clients.create')}}" class="btn btn-sm btn-success" hidden>
                                        <i class="fas fa-plus"></i>
                                        Create
                                    </a>
                                </div>
                            </div>
                            <br>

                            <form class="search-invoice" action="{{route('v2.pending.projects')}}" method="GET">
                                <input type="text" name="client_name" placeholder="Search client name" value="{{ request()->get('client_name') }}">
                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>

                            <table id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Pending projects</th>

                                </thead>
                                <tbody>
                                @foreach($client_users_with_pending_projects as $user)
                                    <tr>
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->name}} {{$user->last_name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>
                                            <span class="btn btn-info btn-sm">{{$user->client?->brand?->name}}</span>
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
                                                <a href="javascript:;" class="badge bg-primary text-white p-2 badge-sm" onclick="assignAgentToPending({{$pending_project['id']}}, {{$pending_project['form_number']}}, {{$pending_project['brand_id']}})">
                                                    <span class="ul-btn__icon"><i class="i-Checked-User"></i></span>
                                                    <span class="ul-btn__text">Assign</span>
                                                </a>
                                                <a href="#" class="badge bg-info text-white p-2 badge-sm">
                                                    <span class="ul-btn__icon"><i class="i-Eye-Visible"></i></span>
                                                    <span class="ul-btn__text">View</span>
                                                </a>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $client_users_with_pending_projects->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!--  Assign Pending Model -->
    <div class="modal fade" id="assignPendingModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('v2.projects.assign') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="pending_assign_id">
                        <input type="hidden" name="form" id="pending_form_id">
                        <div class="form-group">
                            <label class="col-form-label" for="agent-name-wrapper">Name:</label>
                            <select name="agent_id" id="agent-name-wrapper-2" class="form-control">

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

@section('script')
    <script>
        function assignAgentToPending(id, form, brand_id){
            $('#agent-name-wrapper-2').html('');
            var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
            url = url.replace('temp', brand_id);
            $.ajax({
                type:'GET',
                url: url,
                success:function(data) {
                    var getData = data.data;
                    for (var i = 0; i < getData.length; i++) {
                        $('#agent-name-wrapper-2').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                    }

                    $('#agent-name-wrapper-2').select2();
                }
            });

            $('#assignPendingModel').find('#pending_assign_id').val(id);
            $('#assignPendingModel').find('#pending_form_id').val(form);
            $('#assignPendingModel').modal('show');
        }

        $(document).ready(() => {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection
