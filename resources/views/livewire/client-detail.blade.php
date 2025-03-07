<div>
    @include('livewire.loader')
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

        .btn_mark_as_paid:hover {
            cursor: pointer;
        }

        .span_client_priority_badge {
            cursor: pointer;
        }

        .dropdown-content {
            position: absolute;
            background-color: #e6e6e6;
            min-width: 100px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border: 1px solid #ddd;
            top: 104%;
            right: 35%;
        }

        .dropdown-content a {
            color: black;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        @if(auth()->user()->is_employee == 4)
        .support_sensitive {
            color: white !important;
        }
        @endif

        .rounded-buttons {
            font-size: 20px;
            border: solid #0076c2 2px;
            border-radius: 60%;
            max-width: 42px;
            height: 42px;
        }
    </style>

    <div class="breadcrumb">
        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">
            <i class="fas fa-arrow-left"></i>
        </a>
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
                            {{--                        @if(in_array(auth()->user()->is_employee, [0, 2, 6]))--}}

                            @if($client->brand)
                                <div class="col-12">
                                    <b>Brand</b>: {{$client->brand->name}}
                                </div>
                            @endif

                            <div class="col-12">
                                <b>Priority</b>:
                                {!! $client->priority_badge() !!}

                                <!-- Invisible Dropdown -->
                                <div id="priorityDropdown" class="dropdown-content" style="display: none;">
                                    <a href="#">
                                        <span class="badge badge-danger badge_select_priority" data-client="{{$client->id}}" data-value="1">HIGH</span>
                                    </a>
                                    <a href="#">
                                        <span class="badge badge-warning badge_select_priority" data-client="{{$client->id}}" data-value="2">MEDIUM</span>
                                    </a>
                                    <a href="#">
                                        <span class="badge badge-info badge_select_priority" data-client="{{$client->id}}" data-value="3">LOW</span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4 offset-md-4">
                                <div class="row py-3 justify-content-center">
                                    @if($client->contact)
                                        <div class="col rounded-buttons px-0 mx-2 d-flex align-items-center justify-content-center">
                                            <a class="" href="tel:{{$client->contact}}" style="line-height: 10px;">
                                                <i class="fas fa-phone text-primary "></i>
                                            </a>
                                        </div>
                                    @endif

                                    @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [6, 4]))
                                        @php
                                            $client_user = \App\Models\User::where('client_id', $client->id)->first();
                                            if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                                $message_url = 'manager.message.show';
                                            } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {
                                                $message_url = 'support.message.show.id';
                                            }
                                        @endphp
                                        @if($client_user)
                                            <div class="col rounded-buttons px-0 mx-2 d-flex align-items-center justify-content-center">
                                                <a class="" href="{{route($message_url, ['id' => $client_user->id ,'name' => $client->name])}}" style="line-height: 10px;">
                                                    <i class="fas fa-message text-primary "></i>
                                                </a>
                                            </div>
                                        @endif
                                    @endif

                                    @if($client->email)
                                        <div class="col rounded-buttons px-0 mx-2 d-flex align-items-center justify-content-center">
                                            <a class="" href="mailto:{{$client->email}}" style="line-height: 10px;">
                                                <i class="fas fa-envelope text-primary "></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

{{--                            @if($client->contact)--}}
{{--                                <div class="col-12">--}}
{{--                                    <i class="fas fa-phone text-primary support_sensitive"></i>--}}
{{--                                    <a class="support_sensitive" href="tel:{{$client->contact}}">{{$client->contact}}</a>--}}
{{--                                </div>--}}
{{--                            @endif--}}

{{--                            @if($client->email)--}}
{{--                                <div class="col-12">--}}
{{--                                    <i class="fas fa-envelope text-primary support_sensitive"></i>--}}
{{--                                    <a class="support_sensitive" href="mailto:{{$client->email}}">{{$client->email}}</a>--}}
{{--                                </div>--}}
{{--                            @endif--}}
                            {{--                        </p>--}}
                            {{--                        @endif--}}


                            <div class="row mt-4">
                                @if ((in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head)) && count($client->invoices))
                                    <div class="col">
                                        <p style="font-size: medium;">
                                            <a wire:click="set_active_page('client_payment_link-{{$client->id}}')" href="javascript:void(0)">
                                                <i class="fas fa-dollar-sign text-success"></i>
                                                <br />
                                                Generate payment
                                            </a>
                                        </p>
                                    </div>
                                @endif

{{--                                @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [6, 4]))--}}
{{--                                    @php--}}
{{--                                        $client_user = \App\Models\User::where('client_id', $client->id)->first();--}}
{{--                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {--}}
{{--                                            $message_url = 'manager.message.show';--}}
{{--                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {--}}
{{--                                            $message_url = 'support.message.show.id';--}}
{{--                                        }--}}
{{--                                    @endphp--}}
{{--                                    @if($client_user)--}}
{{--                                        <div class="col">--}}
{{--                                            <p style="font-size: medium;">--}}
{{--                                                <a href="{{route($message_url, ['id' => $client_user->id ,'name' => $client->name])}}">--}}
{{--                                                    <i class="i-Speach-Bubble-3 text-warning"></i>--}}
{{--                                                    <br />--}}
{{--                                                    Message--}}
{{--                                                </a>--}}
{{--                                            </p>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                @endif--}}
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
                                                @if($client->user == null)
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm auth_create" data-id="{{$client->id}}">Create account</a>
                                                @else
                                                    <a href="javascript:void(0)" class="btn btn-success btn-sm auth_update" data-id="{{$client->id}}">Reset password</a>
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
                                                <table class="table table-sm table-striped table-bordered mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Package</th>
                                                        <th>Service</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Created at</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($client->invoices as $invoice)
                                                        <tr>
                                                            <td style="vertical-align: middle;">
                                                                {{--                                                                <span class="badge badge-sm badge-dark">#{{ $invoice->invoice_number }}</span>--}}
                                                                <span class="badge badge-sm badge-dark">#{{ $invoice->id }}</span>
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
                                                                    @php
                                                                        $record = $invoice->services($service_list[$i]);
                                                                    @endphp
                                                                    @if($record)
                                                                        <span class="badge badge-info badge-sm mb-1">{{ $invoice->services($service_list[$i])->name }}</span>
                                                                    @endif
                                                                @endfor
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <b>{{ $invoice->currency_show->sign }}</b>
                                                                {{ $invoice->amount }}
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <span class="">
                                                                    <span class="badge badge-{{App\Models\Invoice::STATUS_COLOR[$invoice->payment_status]}}">
                                                                        {{ App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status] }}
                                                                    </span>
                                                                    @if($invoice->payment_status == 1 && $client->user != null)
                                                                        <a type="submit" class="badge badge-danger btn_mark_as_paid text-white" style="border: 0px;" wire:click="mark_invoice_as_paid({{$invoice->id}})">
                                                                            Mark as paid
                                                                        </a>
                                                                    @endif
                                                                    @if($invoice->is_authorize)
                                                                        <a href="#" wire:click="copy_authorize_link('{{route('client.pay.with.authorize', $invoice->id)}}')" class="badge badge-warning badge-icon badge-sm mr-1">
                                                                            <span class="ul-btn__text"><b>Payment link</b></span>
                                                                        </a>
                                                                    @endif
                                                                </span>
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <span class="badge badge-info">
                                                                    {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F, Y') }}
                                                                </span>
                                                            </td>
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
                                                    <table class="table table-sm table-striped table-bordered mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Brief Pending</th>
                                                            <th>Invoice ID</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($pending_projects as $pending_project)
                                                            @php
                                                                $pending_project_detail_url = '';
                                                                if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                                                    $pending_project_detail_url = 'admin.pending.project.details';
                                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                                                    $pending_project_detail_url = 'manager.pending.project.details';
                                                                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head) {
                                                                    $pending_project_detail_url = 'support.pending.project.details';
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    {{$pending_project['project_type']}}
{{--                                                                    <span class="badge badge-info badge-sm">--}}
{{--                                                                        INV #1234--}}
{{--                                                                    </span>--}}
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-info badge-sm">
                                                                        #{{$pending_project['invoice_id']}}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:;" class="badge badge-primary badge-icon badge-sm" onclick="assignAgentToPending({{$pending_project['id']}}, {{$pending_project['form_number']}}, {{$pending_project['brand_id']}})">
                                                                        <span class="ul-badge__icon"><i class="i-Checked-User"></i></span>
                                                                        <span class="ul-badge__text">Assign</span>
                                                                    </a>
                                                                    <a href="{{ route($pending_project_detail_url, ['id' => $pending_project['id'], 'form' => $pending_project['form_number']]) }}" class="badge badge-info badge-icon badge-sm">
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
                                                <table class="table table-sm table-striped table-bordered mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Service</th>
                                                        @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6, 2]))
                                                            <th>Assigned to</th>
                                                        @endif
                                                        <th>Status</th>
                                                        <th>Created at</th>
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
                                                                {{--                                                                <span class="badge badge-sm badge-dark">#{{ $invoice->invoice_number }}</span>--}}
                                                                <span class="badge badge-sm badge-dark">#{{ $project->id }}</span>
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <a wire:click="set_active_page('projects_detail-{{$project->id}}')" href="javascript:void(0)" class="anchor_project_name">
                                                                    {{str_replace($client->name, '', str_replace(' - ', ' ', $project->name))}}
                                                                </a>
                                                            </td>

                                                            @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6, 2]))
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

                                                            <td style="vertical-align: middle;">
                                                                <span class="badge badge-info">
                                                                    {{ \Carbon\Carbon::parse($project->created_at)->format('d F, Y') }}
                                                                </span>
                                                            </td>

                                                            @if(in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [4, 6]))
                                                                <td style="vertical-align: middle;">
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

                                                            @if(\Illuminate\Support\Facades\Auth::user()->is_employee == 2)
                                                                    @if($project->form_checker != 0)
                                                                        <a href="{{ route('admin.form', [ 'form_id' => $project->form_id , 'check' => $project->form_checker, 'id' => $project->id]) }}" class="badge badge-info badge-icon badge-sm">
                                                                            <i class="i-Receipt-4 mr-1"></i>
                                                                            View Form
                                                                        </a>
                                                                    @endif
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
    <div class="modal fade" id="assignModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
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
                    <button class="btn btn-primary ml-2" id="btn_assignModel" type="submit">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!--  Assign Pending Model -->
    <div class="modal fade" id="assignPendingModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-form-label" for="agent-name-wrapper">Name:</label>
                            <select name="agent_id" id="agent-name-wrapper-2" class="form-control" wire:model="assign_pending_agent_id">

                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary ml-2" id="btn_assignPendingModel" type="submit">Save changes</button>
                    </div>
            </div>
        </div>
    </div>

    <script>
        $('form').on('submit', function () {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    </script>
    <script>
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
        });
    </script>
</div>
