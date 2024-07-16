@extends($layout)
@section('title', 'Client detail')
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
        /* Mouse over link */
        a {text-decoration: none; color: black;}   /* Mouse over link */

        .anchor_project_name:hover {
            color: #00aeef;
        }
    </style>
@endpush
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Client detail</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        {{--brand detail--}}
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="row text-center mb-4">
                    <div class="col-md-8 offset-md-2">
                        <h2>{{$client->name . ' ' . $client->last_name}}</h2>
{{--                        <p style="font-size: medium;">--}}
                            @if($client->contact)
                                <div class="col-12">
                                    <i class="fas fa-phone text-primary"></i>
                                    <a href="tel:{{$client->contact}}">{{$client->contact}}</a>
                                </div>
                            @endif

                            @if($client->email)
                                <div class="col-12">
                                    <i class="fas fa-envelope text-primary"></i>
                                    <a href="mailto:{{$client->email}}">{{$client->email}}</a>
                                </div>
                            @endif
{{--                        </p>--}}


                        <div class="row mt-4">
                            @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head)) && count($client->invoices))
                                @php
                                    $generate_payment_route = '';
                                    if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                        $generate_payment_route = 'admin.invoice.index';
                                    } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                        $generate_payment_route = 'manager.generate.payment';
                                    } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                        $generate_payment_route = 'client.generate.payment';
                                    } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head) {
                                        $generate_payment_route = 'support.client.generate.payment';
                                    }
                                @endphp
                                <div class="col">
                                    <p style="font-size: medium;">
                                        <a href="{{route($generate_payment_route, ['id' => $client->id , 'redirect_to_client_detail' => true])}}">
                                            <i class="fas fa-dollar-sign text-success"></i>
                                            <br />
                                            Generate payment
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [6, 4]))
                                @php
                                    $client_user = \App\Models\User::where('client_id', $client->id)->first();
                                    if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                        $message_route = 'manager.message.show';
                                    } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {
                                        $message_route = 'support.message.show.id';
                                    }
                                @endphp
                                @if($client_user)
                                    <div class="col">
                                        <p style="font-size: medium;">
                                            <a href="{{route($message_route, ['id' => $client_user->id ,'name' => $client->name])}}">
                                                <i class="i-Speach-Bubble-3 text-warning"></i>
                                                <br />
                                                Message
                                            </a>
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>



                        @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0, 4])))
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-12" style="border: 1px solid #b7b7b7; font-size: 16px;" id="header1">
                                            <i class="fas fa-user mr-2"></i>
                                            <b>Account</b>
                                        </div>
                                        <div class="col-md-12 p-2" style="border-top: 1px solid #b7b7b7;" id="wrapper1" >
                                            @php
                                                $create_auth_route = '';
                                                $update_auth_route = '';
                                                if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                                    $create_auth_route = route('admin.client.createauth');
                                                    $update_auth_route = route('admin.client.updateauth');
                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                                    $create_auth_route = route('manager.client.createauth');
                                                    $update_auth_route = route('manager.client.updateauth');
                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                                    $create_auth_route = route('sale.client.createauth');
                                                    $update_auth_route = route('sale.client.updateauth');
                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {
                                                    $create_auth_route = route('support.client.createauth');
                                                    $update_auth_route = route('support.client.updateauth');
                                                }
                                            @endphp
                                            @if($client->user == null)
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm auth_create" data-route="{{$create_auth_route}}">Create account</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn btn-success btn-sm auth_create" data-route="{{$update_auth_route}}">Reset password</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                        @endif


                        @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head)) && count($client->invoices))
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-12" style="border: 1px solid #b7b7b7; font-size: 16px;" id="header2">
                                            <i class="i-Credit-Card mr-2"></i>
                                            <b>Invoices</b>
                                            <br>
                                        </div>
                                        <div class="col-md-12 p-0" style="border-top: 1px solid #b7b7b7;" id="wrapper2"  >
                                            {{--                                            <div class="row m-auto p-2" style="font-size: 15px;">--}}
                                            {{--                                                <div class="col-md-12">--}}
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Package</th>
                                                    <th>Service</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($client->invoices as $invoice)
                                                        <tr>
                                                            <td style="vertical-align: middle;">
                                                                <span class="badge badge-sm badge-dark">#{{ $invoice->invoice_number }}</span>
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                @if($invoice->package == 0)
                                                                    {{ $invoice->custom_package }}
                                                                @else
                                                                    {{ $invoice->package }}
                                                                @endif
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                @php
                                                                    $service_list = explode(',', $invoice->service);
                                                                @endphp
                                                                @for($i = 0; $i < count($service_list); $i++)
                                                                    <span class="badge badge-info badge-sm mb-1">{{ $invoice->services($service_list[$i])->name }}</span>
                                                                @endfor
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <span class="">
                                                                    {{ App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status] }}
                                                                    @if($invoice->payment_status == 1)
                                                                        @php
                                                                            if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                                                                $invoice_update_route = 'admin.invoice.paid';
                                                                            } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                                                                $invoice_update_route = 'manager.invoice.paid';
                                                                            } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                                                                $invoice_update_route = 'sale.invoice.paid';
                                                                            } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {
                                                                                $invoice_update_route = 'support.invoice.paid';
                                                                            }
                                                                        @endphp
                                                                        <form method="post" action="{{ route($invoice_update_route, $invoice->id) }}">
                                                                            @csrf
                                                                            <button type="submit" class="badge badge-danger" style="border: 0px;">Mark As Paid</button>
                                                                        </form>
                                                                    @endif
                                                                </span>
                                                            </td>
                                                            <td style="vertical-align: middle;">{{ $invoice->currency_show->sign }}{{ $invoice->amount }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{--                                                </div>--}}
                                            {{--                                            </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                        @endif


                        @if($client->user)
                            @php
                                $briefs_pendings = get_briefs_pending($client->user->id);
                            @endphp
                            @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head)) && count($briefs_pendings))
                                <div class="row my-4">
                                    <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                        <div class="row">
                                            <div class="col-md-12" style="border: 1px solid #b7b7b7; font-size: 16px;" id="header3">
                                                <i class="i-Folder-Close mr-2"></i>
                                                <b>Briefs pending</b>
                                            </div>
                                            <div class="col-md-12" style="border-top: 1px solid #b7b7b7;" id="wrapper3" >
                                                <div class="row m-auto p-1" style="font-size: 15px;">
                                                    <div class="text-center" style="width: 100%">
                                                        @foreach($briefs_pendings as $brief_pending)
        {{--                                                    <div class="col">--}}
                                                                <span class="badge badge-pill badge-primary my-1">{{$brief_pending}}</span>
                                                                &nbsp;
        {{--                                                    </div>--}}
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                            @endif
                        @endif

                        @if($client->user)
                            @php
                                $pending_projects = get_pending_projects($client->user->id);
                            @endphp
                            @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head)) && count($pending_projects))
                                <div class="row my-4">
                                    <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                        <div class="row">
                                            <div class="col-md-12" style="border: 1px solid #b7b7b7; font-size: 16px;" id="header4">
                                                <i class="i-Folder-Loading mr-2"></i>
                                                <b>Pending projects</b>
                                            </div>
                                            <div class="col-md-12 p-0" style="border-top: 1px solid #b7b7b7;" id="wrapper4" >
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Brief Pending</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pending_projects as $pending_project)
                                                            @php
                                                                $pending_project_detail_route = '';
                                                                $assign_pending_project_route = '';
                                                                if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                                                    $pending_project_detail_route = 'admin.pending.project.details';
                                                                    $assign_pending_project_route = 'admin.assign.support';
                                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                                                    $pending_project_detail_route = 'manager.pending.project.details';
                                                                    $assign_pending_project_route = 'admin.assign.support';
                                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head) {
                                                                    $pending_project_detail_route = 'support.pending.project.details';
                                                                    $assign_pending_project_route = 'admin.assign.support';
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{$pending_project['project_type']}}</td>
                                                                <td>
                                                                    <a href="javascript:;" class="badge badge-primary badge-icon badge-sm" onclick="assignAgentToPending({{$pending_project['id']}}, {{$pending_project['form_number']}}, {{$pending_project['brand_id']}})">
                                                                        <span class="ul-badge__icon"><i class="i-Checked-User"></i></span>
                                                                        <span class="ul-badge__text">Assign</span>
                                                                    </a>
                                                                    <a href="{{ route($pending_project_detail_route, ['id' => $pending_project['id'], 'form' => $pending_project['form_number']]) }}" class="badge badge-info badge-icon badge-sm">
                                                                        <span class="ul-badge__icon"><i class="i-Eye-Visible"></i></span>
                                                                        <span class="ul-badge__text">View</span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                            @endif
                        @endif

                        @if (\Illuminate\Support\Facades\Auth::user()->is_employee != 0 && count($projects))
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-12" style="border: 1px solid #b7b7b7; font-size: 16px;" id="header5">
                                            <i class="i-Suitcase mr-2"></i>
                                            <b>Services</b>
                                        </div>
                                        <div class="col-md-12 p-0" style="border-top: 1px solid #b7b7b7;" id="wrapper5" >
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Service</th>
                                                        @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6]))
                                                            <th>Assigned to</th>
                                                        @endif
                                                        <th>Status</th>
                                                        @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6]))
                                                            <th>Actions</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($projects as $project)
                                                        @php
                                                            if (Auth::user()->is_employee == 2) {
                                                                $active_tasks = \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)->get();
                                                                $department_count = count(array_unique(\App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)->get()->pluck('category_id')->toArray())) ?? 0;
                                                            } else {
                                                                $active_tasks = \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)
                                                                    ->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list())->get();
                                                                $department_count = count(array_unique(\App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list())->get()->pluck('category_id')->toArray())) ?? 0;
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td style="vertical-align: middle;">
                                                                <a href="{{route('projects.detail', $project->id)}}" class="anchor_project_name">
                                                                    {{str_replace($client->name, '', str_replace(' - ', '', $project->name))}}
                                                                </a>
                                                            </td>

                                                            @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6]))
                                                                <td style="vertical-align: middle;">
                                                                    <h6>{{$project->added_by->name . ' ' . $project->added_by->last_name}}</h6>
                                                                </td>
                                                            @endif

                                                            <th style="vertical-align: middle;">
                                                                @if(no_pending_tasks_left($project->id))
                                                                    <span class="badge badge-success">No pending tasks</span>
                                                                @endif

                                                                @if($department_count > 0)
                                                                    <small class="text-muted mt-2 mb-2">{{count($active_tasks)}} active task(s) in {{$department_count}} department(s)</small>
                                                                @endif
                                                            </th>

                                                            @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6]))
                                                                <td style="vertical-align: middle;">
                                                                    {{--                                                                            <a href="{{ route('support.message.show.id', ['id' => $project->client->id ,'name' => $project->client->name]) }}" class="badge badge-warning badge-sm">--}}
                                                                    {{--                                                                                Message--}}
                                                                    {{--                                                                            </a>--}}
                                                                    {{--                                                                            <br>--}}

                                                                    <a href="javascript:;" class="badge badge-primary btn-icon btn-sm" onclick="assignAgent({{$project->id}}, {{$project->form_checker}}, {{$project->brand_id}})">
                                                                        <span class="ul-btn__icon"><i class="i-Checked-User"></i></span>
                                                                        <span class="ul-btn__text">Re Assign</span>
                                                                    </a>
                                                                    <br>
                                                                    @if($project->form_checker != 0)
                                                                        <a href="{{ route('support.form', [ 'form_id' => $project->form_id , 'check' => $project->form_checker, 'id' => $project->id]) }}" class="badge badge-info badge-icon badge-sm">
                                                                            <i class="i-Receipt-4 mr-1"></i>
                                                                            View Form
                                                                        </a>
                                                                    @endif
                                                                    <br>
                                                                    <a href="{{ route('create.task.by.project.id', ['id' => $project->id, 'name' => preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(str_replace(' ', '-', $project->name))) ]) }}" class="badge badge-success badge-icon badge-sm">
                                                                        <i class="fas fa-plus"></i>
                                                                        Create Task
                                                                    </a>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
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
            <form action="{{ route('support.reassign.support') }}" method="post">
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

<!--  Assign Pending Model -->
<div class="modal fade" id="assignPendingModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="{{ route('assign-pending-project-to-agent') }}" method="post">
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
@push('scripts')
<script>
    $('form').on('submit', function () {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
</script>
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
            }
        });

        $('#assignPendingModel').find('#pending_assign_id').val(id);
        $('#assignPendingModel').find('#pending_form_id').val(form);
        $('#assignPendingModel').modal('show');
    }

    function assignAgent(id, form, brand_id){
        $('#agent-name-wrapper').html('');
        var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
        url = url.replace('temp', brand_id);
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
        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }

        for (var i = 1; i < 6; i++) {
            $('#header' + i).css('cursor', 'pointer');
        }

        $('#header1').on('click', () => {
            $('#wrapper1').prop('hidden', !($('#wrapper1').prop('hidden')));
        });
        $('#header2').on('click', () => {
            $('#wrapper2').prop('hidden', !($('#wrapper2').prop('hidden')));
        });
        $('#header3').on('click', () => {
            $('#wrapper3').prop('hidden', !($('#wrapper3').prop('hidden')));
        });
        $('#header4').on('click', () => {
            $('#wrapper4').prop('hidden', !($('#wrapper4').prop('hidden')));
        });
        $('#header5').on('click', () => {
            $('#wrapper5').prop('hidden', !($('#wrapper5').prop('hidden')));
        });

        $('.auth_create').on('click', function () {
            var id = '{{$client->id}}';
            var pass = generatePassword();
            var url = $(this).data('route');

            var el = $(this);

            swal({
                title: "Enter Password",
                input: "text",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Enter Password",
                inputValue: pass
            }).then(function (inputValue) {
                if (inputValue === false){
                    return swal({
                        title:"Field cannot be empty",
                        text: "Password Not Inserted/Updated because it is Empty",
                        type:"danger"
                    })
                }
                if (inputValue === "") {
                    return swal({
                        title:"Field cannot be empty",
                        text: "Password Not Inserted/Updated because it is Empty",
                        type:"danger"
                    })
                }

                var text = el.text();
                el.prop('disabled', !(el.prop('disabled')));
                el.text('Please wait.');

                $.ajax({
                    type:'POST',
                    url: url,
                    data: {id: id, pass:inputValue},
                    success:function(data) {
                        if(data.success == true){
                            swal("Auth Created", "Password is : " + inputValue, "success");

                            el.prop('disabled', !(el.prop('disabled')));
                            el.text(text);

                            window.location.reload();
                        }else{
                            return swal({
                                title:"Error",
                                text: "There is an Error, Please Contact Administrator",
                                type:"danger"
                            })
                        }
                    }
                });
            });
        });
    });
</script>
@endpush