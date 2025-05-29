@extends('v2.layouts.app')

@section('title', 'Client Detail')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="brand-detail new-client-detail">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="brand-dela">
                                    <div class="up-sell-img">
                                        <img src="{{asset('/images/avatar.png')}}" class="img-fluid">
                                        <div class="up-sell-content">
                                            <h2>{{$client->name . ' ' . $client->last_name}}</h2>
                                            <p>{{$client->brand?->name}}</p>
                                        </div>
                                    </div>

                                    <div class="brand-email">
                                        <div class="email">
                                            <a href="tel:{{$client->contact}}">
                                                <i class="fa-solid fa-phone"></i> {{$client->contact}}
                                            </a>
                                        </div>
                                        <div class="email">
                                            <a href="mailto:{{$client->email}}">
                                                <i class="fa-solid fa-envelope"></i>  {{$client->email}}
                                            </a>
                                        </div>
                                        <div class="for-password">
                                            <a href="javascript:;">
                                                <img src="{{asset('v2/images/img-msg.jpg')}}" class="img-fluid">
                                            </a>
{{--                                            <a href="javascript:;" class="password-btn">Reset Password</a>--}}
                                            <a href="javascript:;" class="password-btn badge bg-{{ $client->user ? 'success' : 'danger' }} badge-sm auth-create"
                                               data-id="{{ $client->id }}"
                                               data-auth="{{ $client->user ? 1 : 0 }}"
                                               data-password="{{ $client->user ? '' : '' }}">
                                                {{ $client->user ? 'Reset Password' : 'Create Account' }}
                                            </a>
                                            <a href="{{route('v2.invoices.create', $client->id)}}" class="password-btn blue-assign">Generate Payment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @php
                    $invoices = \App\Models\Invoice::where('client_id', $client->id)->orderBy('created_at', 'DESC')->get();
                @endphp
                @if(count($invoices))
                    <section class="list-0f">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="list-0f-head">
                                        <h2>Invoices</h2>

                                        <table>
                                            <tbody>
                                                <tr>
                                                    <th>ID.</th>
                                                    <th>Package</th>
                                                    <th>Service </th>
                                                    <th>Amount </th>
                                                    <th>Status</th>
                                                    <th>Link</th>
                                                    <th>Created at</th>
                                                </tr>

                                                @foreach($invoices as $invoice)
                                                    <tr>
                                                        <td>{{$invoice->id}}</td>
                                                        <td>{{$invoice->package}}</td>

                                                        <td style="vertical-align: middle;">
                                                            @php
                                                                $service_list = explode(',', $invoice->service);
                                                            @endphp
                                                            @for($i = 0; $i < count($service_list); $i++)
                                                                @php
                                                                    $record = $invoice->services($service_list[$i]);
                                                                @endphp
                                                                @if($record)
                                                                    <span class="badge badge-pill badge-primary p-2 mr-1">{{ $invoice->services($service_list[$i])->name }}</span>
                                                                @endif
                                                            @endfor
                                                        </td>
                                                        <td>
                                                            <b>{{$invoice->currency_show->sign}}</b>
                                                            {{$invoice->amount}}
                                                        </td>
                                                        @if($invoice->payment_status == 2)
                                                            <td class="green">
                                                                Paid
                                                            </td>
                                                        @else
                                                            <td class="red">
                                                                Unpaid
                                                                <form method="post" action="{{route('v2.invoices.paid', $invoice->id)}}">
                                                                    @csrf
                                                                    <button type="submit" class="badge badge-sm badge-danger p-2" style="border: 0px;">Mark As Paid</button>
                                                                </form>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            @if($invoice->is_authorize == 1)
                                                                <a href="javascript:void(0);" class="badge badge-sm badge-warning p-2 btn_copy_authorize_link" data-url="{{route('client.pay.with.authorize', $invoice->id)}}">
                                                                    <i class="fas fa-copy"></i>
                                                                    Payment link
                                                                </a>
                                                            @else
                                                                <b>N/A</b>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @php
                    $client_user = \App\Models\User::where('client_id', $client->id)->first();
                    $projects = $client_user ? $client_user->recent_projects : [];
                @endphp
                @if(count($projects))
                    <section class="list-0f">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="list-0f-head">
                                        <h2>Services</h2>
                                        <table>
                                            <tbody>
                                            <tr>
                                                <th>ID.</th>
                                                <th>Service</th>
                                                <th>Assigned To </th>
                                                <th>Status</th>
                                                <th>Created at</th>
                                                <th>Actions</th>

                                            </tr>

                                            @foreach($projects as $project)
                                                @php
                                                    $active_tasks = \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)
                                                                        ->when(auth()->user()->is_employee != 2, function ($q) {
                                                                            return $q->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list());
                                                                        })
                                                                        ->count();
                                                    $department_count = count(array_unique(
                                                            \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)
                                                                ->when(auth()->user()->is_employee != 2, function ($q) {
                                                                    return $q->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list());
                                                                })
                                                                ->get()->pluck('category_id')->toArray()
                                                        )) ?? 0;
                                                @endphp
                                                <tr>
                                                    <td>{{$project->id}}</td>

                                                    <td>{{str_replace($client->name, '', str_replace(' - ', ' ', $project->name))}}</td>
                                                    <td>{{$project->added_by->name . ' ' . $project->added_by->last_name}}</td>
                                                    <td>
                                                        @if(no_pending_tasks_left($project->id))
                                                            <span class="badge badge-success">No pending tasks</span>
                                                        @endif

                                                        @if($department_count > 0)
                                                            <small class="text-muted mt-2 mb-2">{{$active_tasks}} active task(s) in {{$department_count}} department(s)</small>
                                                        @endif
                                                    </td>
                                                    <td>{{Carbon\Carbon::parse($project->created_at)->format('d F Y')}}</td>

                                                    <td>
                                                        <a href="javascript:void(0);" class="for-assign btn_assign_project"
                                                           data-id="{{$project->id}}"
                                                           data-form="{{$project->form_checker}}"
                                                           data-brand="{{$project->brand_id}}"
                                                        >
                                                            Re Assign
                                                        </a>
                                                        @if($project->form_checker != 0)
                                                            <a href="javascript:;" class="for-assign blue-assign">
                                                                View Form
                                                            </a>
                                                        @endif
                                                        <a href="javascript:;" class="for-assign dark-blue-assign">Create Task</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @php
                    $pending_projects = $client->user ? get_pending_projects($client->user->id) : [];
                @endphp
                @if(count($pending_projects))
                    <section class="list-0f">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="list-0f-head">
                                        <h2>Pending projects</h2>


                                        <table>
                                            <tbody>
                                            <tr>
                                                <th>Brief Pending</th>
                                                <th>Invoice ID</th>
                                                <th>Actions</th>
                                            </tr>

                                            @foreach($pending_projects as $pending_project)
                                                <tr>
                                                    <td>
                                                        {{$pending_project['project_type']}}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-dark badge-sm">
                                                            #{{$pending_project['invoice_id']}}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0);" class="for-assign" onclick="assignAgentToPending({{$pending_project['id']}}, {{$pending_project['form_number']}}, {{$pending_project['brand_id']}})">
                                                            Assign
                                                        </a>
                                                        <a href="javascript:;" class="for-assign blue-assign">
                                                            View Form
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
                    </section>
                @endif

                @php
                    $briefs_pendings = $client->user ? get_briefs_pending($client->user->id) : [];
                @endphp
                @if(count($briefs_pendings))
                    <section class="list-0f">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="list-0f-head">
                                        <h2>Briefs pending</h2>

                                        <div class="row m-auto">
                                            @foreach($briefs_pendings as $brief_pending)
                                                <span class="badge badge-pill badge-primary p-2 my-1 mr-2">{{$brief_pending}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @break

            @default
                <section class="brand-detail new-client-detail">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="brand-dela">
                                    <div class="up-sell-img">
                                        <img src="{{asset('v2/images/bu-head1.png')}}" class="img-fluid">
                                        <div class="up-sell-content">
                                            <h2>Mackendy Sterling</h2>
                                            <p>The Designs Craft</p>
                                        </div>
                                    </div>

                                    <div class="brand-email">
                                        <div class="email">
                                            <a href="javascript:;">
                                                <i class="fa-solid fa-phone"></i> 13159901109
                                            </a>
                                        </div>
                                        <div class="email">
                                            <a href="javascript:;">
                                                <i class="fa-solid fa-envelope"></i>  info@designsventure.com
                                            </a>
                                        </div>
                                        <div class="for-password">
                                            <a href="javascript:;">
                                                <img src="{{asset('v2/images/img-msg.jpg')}}" class="img-fluid">
                                            </a>
                                            <a href="javascript:;" class="password-btn">Reset Password</a>
                                            <a href="javascript:;" class="password-btn blue-assign">Generate Payment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="list-0f">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head">
                                    <h2>Invoices</h2>


                                    <table>
                                        <tbody>
                                        <tr>
                                            <th>ID.</th>
                                            <th>Package</th>
                                            <th>Service </th>
                                            <th>Amount </th>
                                            <th>Status</th>
                                            <th>Created at</th>

                                            <th></th>

                                        </tr>

                                        <tr>
                                            <td>387</td>
                                            <td>3 logo Concepts</td>

                                            <td>Logo Design</td>
                                            <td>$987</td>
                                            <td class="green">Paid</td>
                                            <td>24 January, 2025 </td>

                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>452</td>
                                            <td>Mobile Software Development</td>

                                            <td>Mobile Application</td>
                                            <td>$15,000</td>
                                            <td class="red">Unpaid</td>
                                            <td>24 January, 2025 </td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>999</td>
                                            <td>3 logo Concepts</td>

                                            <td>Logo Design</td>
                                            <td>$1,200</td>
                                            <td class="green">Paid</td>
                                            <td>24 January, 2025 </td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>537</td>
                                            <td>Book Publishing</td>

                                            <td>Book Publishing</td>
                                            <td>$488</td>
                                            <td class="red">Unpaid</td>
                                            <td>24 January, 2025 </td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>




                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="list-0f">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head">
                                    <h2>Services</h2>


                                    <table>
                                        <tbody>
                                        <tr>
                                            <th>ID.</th>
                                            <th>Service</th>
                                            <th>Assigned To </th>
                                            <th>Status</th>
                                            <th>Created at</th>
                                            <th>Actions</th>

                                            <th></th>

                                        </tr>

                                        <tr>
                                            <td>387</td>

                                            <td>Logo Design</td>
                                            <td>Allen Mathews</td>
                                            <td>In Progress</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign">Re Assign</a></td>

                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>452</td>
                                            <td>Mobile Application</td>
                                            <td>Allen Mathews</td>
                                            <td>Send For Approval</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign dark-blue-assign">Create Task</a></td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>999</td>
                                            <td>Logo Design</td>
                                            <td>Allen Mathews</td>
                                            <td>Pending Task</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign blue-assign">View Form</a></td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>537</td>
                                            <td>Book Publishing</td>
                                            <td>Allen Mathews</td>
                                            <td>Completed</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign">Re Assign</a></td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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

    <!--  Assign Model -->
    <div class="modal fade" id="assignModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
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
    <div class="modal fade" id="assignPendingModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
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

@section('script')
    <script>
        function copy_authorize_link (url) {
            // Create a temporary textarea to hold the link
            var tempInput = document.createElement("textarea");
            tempInput.value = url; // Assign the link to the textarea
            document.body.appendChild(tempInput); // Append textarea to body (temporarily)

            tempInput.select(); // Select the text
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy"); // Copy to clipboard

            document.body.removeChild(tempInput); // Remove the temporary textarea

            toastr.success('Link copied to clipboard!');
        }
        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }

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

                        $('#agent-name-wrapper').select2();
                    }
                });
                $('#assignModel').find('#assign_id').val(id);
                $('#assignModel').find('#form_id').val(form);
                $('#assignModel').modal('show');
            }

            $('.auth-create').on('click', function () {
                var auth = $(this).data('auth');
                var id = $(this).data('id');
                var pass = generatePassword();
                if(auth == 0){
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
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type:'POST',
                            url: "{{ route('admin.client.createauth') }}",
                            data: {id: id, pass:inputValue},
                            success:function(data) {
                                if(data.success == true){
                                    swal("Auth Created", "Password is : " + inputValue, "success");

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                }else{
                                    return swal({
                                        title:"Error",
                                        text: "There is an Error, Plase Contact Administrator",
                                        type:"danger"
                                    })
                                }
                            }
                        });
                    });
                }else{
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
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            type:'POST',
                            url: "{{ route('admin.client.updateauth') }}",
                            data: {id: id, pass:inputValue},
                            success:function(data) {
                                if(data.success == true){
                                    swal("Auth Created", "Password is : " + inputValue, "success");

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                }else{
                                    return swal({
                                        title:"Error",
                                        text: "There is an Error, Plase Contact Administrator",
                                        type:"danger"
                                    })
                                }
                            }
                        });
                    });
                }
            });

            $('.btn_assign_project').on('click', function () {
                assignAgent($(this).data('id'), $(this).data('form'), $(this).data('brand'));
            });

            //copy link
            $('.btn_copy_authorize_link').on('click', function () {
                copy_authorize_link($(this).data('url'));
            });
        });
    </script>
@endsection
