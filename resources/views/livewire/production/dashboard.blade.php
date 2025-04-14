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

        .hover_box_shadow {
            transition: box-shadow 0.1s ease-in;
        }

        .hover_box_shadow:hover {
            box-shadow: 0px 0px 5px 1px #b7b7b7;
        }

        .badge-sm {
            border-radius: 0px;
            padding: 4px 4px;
        }
    </style>

{{--    <div class="breadcrumb">--}}
{{--        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">--}}
{{--            <i class="fas fa-arrow-left"></i>--}}
{{--        </a>--}}
{{--        <h1 class="mr-2">Production Dashboard</h1>--}}
{{--    </div>--}}
{{--    <div class="separator-breadcrumb border-top"></div>--}}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            {{--brand detail--}}
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="row text-center mb-4">
                        <div class="col-md-8 offset-md-2">
                            <div class="row mb-3">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-12 mt-2" style="font-size: 18px;" id="header2">
                                            <b>
                                                Projects
                                            </b>
                                            @php
                                                $noti_count = count(auth()->user()->unreadNotifications);
                                            @endphp
                                            @if($noti_count)
                                                <a href="{{ route('production.my-notifications') }}" class="text-danger">
                                                    <i class="bi bi-bell-fill mb-1" style="font-size: 20px;"></i>
                                                    <span class="badge badge-dark" style="font-size: 8px; font-weight: 600; position: relative; top: -12px; left: -14px;">
                                                        {{$noti_count}}
                                                    </span>
                                                </a>
                                            @endif
                                            <br>
                                        </div>
                                        <div class="col-md-12" style="font-size: 16px;">
                                            <div class="row justify-content-center">
                                                <input id="status_0" type="checkbox" wire:model="dashboard_project_status.0" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[0] == 0 ? '-outline' : '' !!}-danger badge-sm {!! $dashboard_project_status[0] == 0 ? '' : ' text-white' !!}' for="status_0" style="cursor: pointer;">
                                                        Open
                                                    </label>
                                                </p>

                                                <input id="status_1" type="checkbox" wire:model="dashboard_project_status.1" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[1] == 0 ? '-outline' : '' !!}-primary badge-sm {!! $dashboard_project_status[1] == 0 ? '' : ' text-white' !!}' for="status_1" style="cursor: pointer;">
                                                        Re Open
                                                    </label>
                                                </p>

                                                <input id="status_2" type="checkbox" wire:model="dashboard_project_status.2" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[2] == 0 ? '-outline' : '' !!}-info badge-sm {!! $dashboard_project_status[2] == 0 ? '' : ' text-white' !!}' for="status_2" style="cursor: pointer;">
                                                        Hold
                                                    </label>
                                                </p>

                                                <input id="status_3" type="checkbox" wire:model="dashboard_project_status.3" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[3] == 0 ? '-outline' : '' !!}-success badge-sm {!! $dashboard_project_status[3] == 0 ? '' : ' text-white' !!}' for="status_3" style="cursor: pointer;">
                                                        Completed
                                                    </label>
                                                </p>

                                                <input id="status_4" type="checkbox" wire:model="dashboard_project_status.4" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[4] == 0 ? '-outline' : '' !!}-warning badge-sm {!! $dashboard_project_status[4] == 0 ? '' : ' text-white' !!}' for="status_4" style="cursor: pointer;">
                                                        In Progress
                                                    </label>
                                                </p>

                                                <input id="status_5" type="checkbox" wire:model="dashboard_project_status.5" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[5] == 0 ? '-outline' : '' !!}-info badge-sm {!! $dashboard_project_status[5] == 0 ? '' : ' text-white' !!}' for="status_5" style="cursor: pointer;">
                                                        Sent for Approval
                                                    </label>
                                                </p>

                                                <input id="status_6" type="checkbox" wire:model="dashboard_project_status.6" hidden>
                                                <p class="mb-0 mb-2" style="font-size: 14px; margin: 0px 2px;">
                                                    <label class='hover_box_shadow badge badge{!! $dashboard_project_status[6] == 0 ? '-outline' : '' !!}-warning badge-sm {!! $dashboard_project_status[6] == 0 ? '' : ' text-white' !!}' for="status_6" style="cursor: pointer;">
                                                        Incomplete Brief
                                                    </label>
                                                </p>
                                            </div>
                                            <div class="row justify-content-center mx-2">
                                                <div class="m-0 p-0 col-md-8 offset-md-2">
                                                    <select name="" id="" class.old="form-control" wire:model="dashboard_category_id" style="width: 100%; font-size: 12px; border-radius: 0px;">
                                                        <option value="All">All departments</option>
                                                        @foreach(Auth()->user()->category as $category)
                                                            <option value="{{ $category->id }}" @if(request()->get('category') != null) {{ (request()->get('category') == $category->id ? 'selected' : ' ') }} @endif>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center mx-2 my-1">
                                                <div class="m-0 p-0 col-md-8 offset-md-2">
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-12 m-0 p-0">
{{--                                                            <input id="input_dashboard_search" type="text" placeholder="Press enter to search" style="width: 100%; font-size: 12px;" value="{{$dashboard_search}}" {!! $dashboard_search !== '' ? 'autofocus' : '' !!}>--}}
                                                            <input id="input_dashboard_search" class="ctrl_f" type="text" placeholder="Press enter to search" style="width: 100%; font-size: 12px; border-radius: 0px; border: 1px solid grey;" value="{{$dashboard_search}}" autocomplete="false">
                                                        </div>
                                                        @if($dashboard_search !== '')
                                                            <div class="col-md-1 m-0 p-0" style="position: absolute; right: -6px; top: 1px;">
{{--                                                            <i id="btn_dashboard_search" class="fas fa-search mt-2" style="font-size: 12px; cursor: pointer;"></i>--}}
                                                                <i class="bi bi-x-octagon mt-2 text-danger"
                                                                   style="font-size: 12px; cursor: pointer;"
                                                                   wire:click="$emit('mutate', {name: 'dashboard_search', value: ''})">
                                                                </i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-10 offset-md-1 mt-2 mb-4">
                                            @foreach($current_projects as $project)
                                                <div class="row my-2 hover_box_shadow p-2" style="border: 1px solid #b7b7b7; cursor: pointer; padding: 8px; padding-bottom: 6px; padding-top: 12px;"
                                                     wire:click="set_active_page('project_detail-{{$project->id}}')">
                                                    <div class="col-md-10">
                                                        <div class="row justify-content-start">
                                                            <span class="badge badge-sm badge-dark text-uppercase">
                                                                #{{ $project->id }}
                                                            </span>

                                                            <span class="badge badge-dark badge-sm mx-1">
                                                                {{ $project->brand->name }}
                                                            </span>

                                                            <span class="badge badge-dark badge-sm mr-1">
                                                                {{ implode('', array_map(function($v) { return $v[0] . '.'; }, explode(' ', $project->category->name))) }}
                                                            </span>

                                                            <span class="badge badge-outline-dark badge-sm mr-1">
                                                                {{ $project->latest_subtask_time() }}
                                                            </span>

                                                            @if(in_array($project->id, $notification_project_ids))
                                                                <i class="bi bi-bell-fill text-danger"></i>
                                                            @endif
                                                        </div>
                                                        <div class="row text-left mt-2" style="letter-spacing: 2px; font-weight: 400; line-height: 12px;">
                                                            {{$project->projects->name}}
                                                        </div>
                                                        <div class="row justify-content-start mt-2">
                                                            <span class="badge badge-sm" style="background-color: lightblue;">
                                                                <i class="bi bi-headset"></i>
                                                                <b style="font-weight: 800; font-size: 10px;">
                                                                    {{ $project->projects->added_by->name ?? '' }} {{ $project->projects->added_by->last_name ?? '' }}
                                                                </b>
                                                            </span>
                                                            @php
                                                                $memeber_name = $project->assigned_member_name();
                                                            @endphp
                                                            @if($memeber_name != '')
                                                                <span class="badge badge-sm ml-1" style="background-color: #FFA500;">
                                                                    <i class="bi bi-people-fill"></i>
                                                                    <b style="font-weight: 800; font-size: 10px;">
                                                                        {{ $project->assigned_member_name() }}
                                                                    </b>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center">
                                                        <div class="row m-auto">
                                                            {!! $project->project_status_badge() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

{{--                                            <div class="row p-2 pt-0">--}}
{{--                                                @foreach($current_projects as $project)--}}
{{--                                                    <div class="col-md-4 p-3 my-2"--}}
{{--                                                         style="border: 1px solid #b7b7b7; cursor: pointer;"--}}
{{--                                                         wire:click="set_active_page('project_detail-{{$project->id}}-{{$current_projects->currentPage()}}')">--}}
{{--                                                        <div class="row justify-content-center">--}}
{{--                                                            <span class="badge badge-sm badge-dark text-uppercase">--}}
{{--                                                                #{{ $project->id }}--}}
{{--                                                            </span>--}}

{{--                                                            <span class="badge badge-dark badge-sm mx-1">--}}
{{--                                                                {{ $project->brand->name }}--}}
{{--                                                            </span>--}}

{{--                                                            <span class="badge badge-dark badge-sm">--}}
{{--                                                                {{ implode('', array_map(function($v) { return $v[0] . '.'; }, explode(' ', $project->category->name))) }}--}}
{{--                                                            </span>--}}
{{--                                                        </div>--}}
{{--                                                        <div class="row justify-content-center mt-2" style="letter-spacing: 2px; font-weight: 100; line-height: 12px;">--}}
{{--                                                            {{$project->projects->name}}--}}
{{--                                                        </div>--}}
{{--                                                        <div class="row justify-content-center">--}}
{{--                                                            <span class="">--}}
{{--                                                                <b style="font-weight: 800; font-size: 10px;">--}}
{{--                                                                    {{ $project->projects->added_by->name ?? '' }} {{ $project->projects->added_by->last_name ?? '' }}--}}
{{--                                                                </b>--}}
{{--                                                            </span>--}}
{{--                                                        </div>--}}

{{--                                                        <div class="row justify-content-center mt-2">--}}
{{--                                                            {!! $project->project_status_badge() !!}--}}

{{--                                                            @if(in_array($project->id, $notification_project_ids))--}}
{{--                                                                <span class="badge badge-danger badge-sm mx-1">--}}
{{--                                                                    <i class="bi bi-bell-fill"></i>--}}
{{--                                                                </span>--}}
{{--                                                            @endif--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @endforeach--}}
{{--                                            </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                {{ $current_projects->links() }}
                            </div>

                            <hr>

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
</div>
