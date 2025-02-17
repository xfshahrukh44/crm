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
{{--        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">--}}
{{--            <i class="fas fa-arrow-left"></i>--}}
{{--        </a>--}}
        <h1 class="mr-2">Production Dashboard</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            {{--brand detail--}}
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="row text-center mb-4">
                        <div class="col-md-8 offset-md-2">
                            <div class="row my-4">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <div class="row">
                                        <div class="col-md-12" style="border: 1px solid #b7b7b7; font-size: 16px;" id="header2">
{{--                                            <a href="#" wire:click="back">--}}
{{--                                                <i class="fas fa-arrow-left"></i>--}}
{{--                                            </a>--}}
                                            <b>Projects</b>
                                            <br>
                                        </div>
                                        <div class="col-md-12" style="font-size: 16px;">
                                            <div class="row justify-content-center">
                                                <input id="status_0" type="checkbox" wire:model="dashboard_project_status.0" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[0] == 0 ? '-outline' : '' !!}-danger badge-sm' for="status_0" style="cursor: pointer;">
                                                        Open
                                                    </label>
                                                </p>

                                                <input id="status_1" type="checkbox" wire:model="dashboard_project_status.1" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[1] == 0 ? '-outline' : '' !!}-primary badge-sm' for="status_1" style="cursor: pointer;">
                                                        Re Open
                                                    </label>
                                                </p>

                                                <input id="status_2" type="checkbox" wire:model="dashboard_project_status.2" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[2] == 0 ? '-outline' : '' !!}-info badge-sm' for="status_2" style="cursor: pointer;">
                                                        Hold
                                                    </label>
                                                </p>

                                                <input id="status_3" type="checkbox" wire:model="dashboard_project_status.3" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[3] == 0 ? '-outline' : '' !!}-success badge-sm' for="status_3" style="cursor: pointer;">
                                                        Completed
                                                    </label>
                                                </p>

                                                <input id="status_4" type="checkbox" wire:model="dashboard_project_status.4" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[4] == 0 ? '-outline' : '' !!}-warning badge-sm' for="status_4" style="cursor: pointer;">
                                                        In Progress
                                                    </label>
                                                </p>

                                                <input id="status_5" type="checkbox" wire:model="dashboard_project_status.5" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[5] == 0 ? '-outline' : '' !!}-info badge-sm' for="status_5" style="cursor: pointer;">
                                                        Sent for Approval
                                                    </label>
                                                </p>

                                                <input id="status_6" type="checkbox" wire:model="dashboard_project_status.6" hidden>
                                                <p class="mb-0 mx-1 my-2" style="font-size: 14px;">
                                                    <label class='badge badge{!! $dashboard_project_status[6] == 0 ? '-outline' : '' !!}-warning badge-sm' for="status_6" style="cursor: pointer;">
                                                        Incomplete Brief
                                                    </label>
                                                </p>
                                            </div>
                                            <div class="row justify-content-center mx-2">
                                                <div class="m-0 p-0 col-md-8 offset-md-2">
                                                    <select name="" id="" class.old="form-control" wire:model="dashboard_category_id" style="width: 100%; font-size: 12px;">
                                                        <option value="All">All departments</option>
                                                        @foreach(Auth()->user()->category as $category)
                                                            <option value="{{ $category->id }}" @if(request()->get('category') != null) {{ (request()->get('category') == $category->id ? 'selected' : ' ') }} @endif>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row p-2 pt-0">
                                                @foreach($current_projects as $project)
                                                    <div class="col-md-4 p-3 my-2"
                                                         style="border: 1px solid #b7b7b7; cursor: pointer;"
                                                         wire:click="set_active_page('project_detail-{{$project->id}}')">
                                                        <div class="row justify-content-center">
                                                            <span class="badge badge-sm badge-dark text-uppercase">
                                                                #{{ $project->id }}
                                                            </span>

                                                            <span class="badge badge-dark badge-sm mx-1">
                                                                {{ $project->brand->name }}
                                                            </span>

                                                            <span class="badge badge-dark badge-sm">
                                                                {{ implode('', array_map(function($v) { return $v[0] . '.'; }, explode(' ', $project->category->name))) }}
                                                            </span>
                                                        </div>
                                                        <div class="row justify-content-center mt-2" style="letter-spacing: 2px; font-weight: 100; line-height: 12px;">
                                                            {{$project->projects->name}}
                                                        </div>
                                                        <div class="row justify-content-center">
                                                            <span class="">
                                                                <b style="font-weight: 800; font-size: 10px;">
                                                                    {{ $project->projects->added_by->name ?? '' }} {{ $project->projects->added_by->last_name ?? '' }}
                                                                </b>
                                                            </span>
                                                        </div>

                                                        <div class="row justify-content-center mt-2">
                                                            {!! $project->project_status_badge() !!}

                                                            @if(in_array($project->id, $notification_project_ids))
                                                                <span class="badge badge-danger badge-sm mx-1">
                                                                    <i class="fas fa-bell"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ $current_projects->links() }}

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
