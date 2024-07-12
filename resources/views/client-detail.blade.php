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
                                <div class="col">
                                    @php
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                            $route = 'admin.invoice';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $route = 'manager.invoice';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                            $route = 'sale.invoice';
                                        } else if (auth()->user()->is_employee == 4 && auth()->user()->is_support_head) {
                                            $route = 'support.invoice';
                                        }
                                    @endphp
                                    <p style="font-size: medium;">
                                        <a href="{{route($route, ['client_id' => $client->id])}}">
                                            <i class="i-Credit-Card text-success"></i>
                                            <br />
                                            Invoices
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [6, 4]))
                                <div class="col">
                                    @php
                                        $client_user = \App\Models\User::where('client_id', $client->id)->first();
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $message_route = 'manager.message.show';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head) {
                                            $message_route = 'support.message.show.id';
                                        }
                                    @endphp
                                    @if($client_user)
                                        <p style="font-size: medium;">
                                            <a href="{{route($message_route, ['id' => $client->id ,'name' => $client->name])}}">
                                                <i class="i-Speach-Bubble-3 text-warning"></i>
                                                <br />
                                                Message
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 4]))
                                <div class="col">
                                    @php
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                            $route = 'admin.pending.project';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $route = 'manager.pending.project';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head) {
                                            $route = 'support.pending.project';
                                        } else {
                                            $route = 'support.pending.project';
                                        }
                                    @endphp
                                    @if($client_user)
                                        <p style="font-size: medium;">
                                            <a href="{{route($route, ['user_id' => $client_user->id])}}">
                                                <i class="i-Folder-Loading text-primary"></i>
                                                <br />
                                                Pending projects
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>


                        @if($client->user)
                            @php
                                $briefs_pendings = get_briefs_pending($client->user->id);
                            @endphp
                            @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head)) && count($briefs_pendings))
                                <div class="row my-4">
                                    <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                        <div class="row">
                                            <div class="col-md-3 d-flex align-items-center" style="border-right: 1px solid #b7b7b7;">
                                                <i class="i-Folder-Close mr-2"></i>
                                                <b>Briefs pending</b>
                                            </div>
                                            <div class="col-md-9 d-flex align-items-center" style="border-right: 1px solid #b7b7b7;">
                                                <div class="row m-auto p-2" style="font-size: 15px;">
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
                            @endif
                        @endif

                        @if (\Illuminate\Support\Facades\Auth::user()->is_employee != 0 && count($projects))
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-3 d-flex align-items-center" style="border-right: 1px solid #b7b7b7;">
                                            <i class="i-Suitcase mr-2"></i>
                                            <b>Services</b>
                                        </div>
                                        <div class="col-md-9 px-0" style="border-right: 1px solid #b7b7b7;">
{{--                                            <div class="row m-auto p-2" style="font-size: 15px;">--}}
{{--                                                <div class="col-md-12">--}}
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Service</th>
                                                                @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6]))
                                                                    <th>Assigned to</th>
                                                                    <th>Actions</th>
                                                                @endif
                                                                <th>Status</th>
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

                                                                    <th style="vertical-align: middle;">
                                                                        @if(no_pending_tasks_left($project->id))
                                                                            <span class="badge badge-success">No pending tasks</span>
                                                                        @endif

                                                                        @if($department_count > 0)
                                                                            <small class="text-muted mt-2 mb-2">{{count($active_tasks)}} active task(s) in {{$department_count}} department(s)</small>
                                                                        @endif
                                                                    </th>
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
@endsection
@push('scripts')
<script>
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

    });
</script>
@endpush