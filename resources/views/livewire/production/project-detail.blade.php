<div id="123213213213213">
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

        .file_wrapper:hover {
            background-color: #F3F3F3;
        }

        .hover_box_shadow {
            transition: box-shadow 0.2s ease-in;
        }

        .hover_box_shadow:hover {
            box-shadow: 0px 0px 5px 1px #b7b7b7;
        }

        .badge-sm {
            border-radius: 0px;
            padding: 4px 4px;
        }
    </style>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            {{--brand detail--}}
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="row text-center mb-4">
                        <div class="col-md-8 offset-md-2">
                            <div class="row my-1 justify-content-center">
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
                            <div class="row justify-content-center" style="letter-spacing: 2px; font-weight: 100;">
                                {{$project->projects->name}}
                            </div>
                            <div class="row justify-content-center">
                                <span class="">
                                    <b style="font-weight: 800; font-size: 10px;">
                                        {{ $project->projects->added_by->name ?? '' }} {{ $project->projects->added_by->last_name ?? '' }}
                                    </b>
                                </span>
                            </div>

                            <hr style="margin: 14px 0px 12px 0px !important;">

                            <div class="row mt-2 text-center m-0">
                                <div class="col">
                                    <a href="javascript:void(0);" wire:click="back">
                                        <span class="badge badge-dark badge-sm hover_box_shadow">
                                            <i class="bi bi-arrow-left"></i>
                                            Back
                                        </span>
                                    </a>
                                    <a href="javascript:void(0);" wire:click="refresh">
                                        <span class="badge badge-success badge-sm hover_box_shadow">
                                            <i class="bi bi-arrow-repeat"></i>
                                            Refresh
                                        </span>
                                    </a>
                                    <a href="{{ route('production.form', [ 'form_id' => $project->projects->form_id , 'check' => $project->projects->form_checker, 'id' => $project->projects->id]) }}" target="_blank">
                                        <span class="badge badge-info badge-sm hover_box_shadow">
                                            <i class="bi bi-list-ul"></i>
                                            View form
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-12 py-1" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <h6 class="mb-0"><b>STATUS</b></h6>
                                    <hr style="margin: 4px 0px 4px 0px !important;">
                                    <div class="row m-0 justify-content-center">
                                        <p class="mb-0 mx-1" style="font-size: 14px;">
                                            <label class='hover_box_shadow badge badge-sm badge{!! $project->status != 0 ? '-outline' : '' !!}-danger {!! $project->status != 0 ? '' : 'text-white' !!}' wire:click="set_project_status({{$project->id}}, 0)" style="cursor: pointer;">
                                                Open
                                            </label>
                                        </p>

                                        <p class="mb-0 mx-1" style="font-size: 14px;">
                                            <label class='hover_box_shadow badge badge-sm badge{!! $project->status != 1 ? '-outline' : '' !!}-primary {!! $project->status != 1 ? '' : 'text-white' !!}' wire:click="set_project_status({{$project->id}}, 1)" style="cursor: pointer;">
                                                Reopen
                                            </label>
                                        </p>

                                        <p class="mb-0 mx-1" style="font-size: 14px;">
                                            <label class='hover_box_shadow badge badge-sm badge{!! $project->status != 4 ? '-outline' : '' !!}-warning {!! $project->status != 4 ? '' : 'text-white' !!}' wire:click="set_project_status({{$project->id}}, 4)" style="cursor: pointer;">
                                                In Progress
                                            </label>
                                        </p>

                                        <p class="mb-0 mx-1" style="font-size: 14px;">
                                            <label class='hover_box_shadow badge badge-sm badge{!! $project->status != 2 ? '-outline' : '' !!}-info {!! $project->status != 2 ? '' : 'text-white' !!}' wire:click="set_project_status({{$project->id}}, 2)" style="cursor: pointer;">
                                                Hold
                                            </label>
                                        </p>

                                        <p class="mb-0 mx-1" style="font-size: 14px;">
                                            <label class='hover_box_shadow badge badge-sm badge{!! $project->status != 6 ? '-outline' : '' !!}-warning {!! $project->status != 6 ? '' : 'text-white' !!}' wire:click="set_project_status({{$project->id}}, 6)" style="cursor: pointer;">
                                                Incomplete Brief
                                            </label>
                                        </p>

                                        <p class="mb-0 mx-1" style="font-size: 14px;">
                                            <label class='hover_box_shadow badge badge-sm badge{!! $project->status != 3 ? '-outline' : '' !!}-success {!! $project->status != 3 ? '' : 'text-white' !!}' wire:click="set_project_status({{$project->id}}, 3)" style="cursor: pointer;">
                                                Completed
                                            </label>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <hr style="margin: 14px 0px 12px 0px !important;">

                            <div class="row mt-2">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <h6 class="my-1 mb-1">
                                        <b>CONVERSATION</b>

                                        <span id="btn_search_messages" class="badge badge-dark badge-sm hover_box_shadow" style="cursor: pointer;">
                                            <i class="bi bi-search m-0"></i>
                                        </span>
                                    </h6>
                                </div>
                                <div class="col-md-12 px-4 py-1" style="border: 1px solid #b7b7b7; max-height: 450px; overflow-y: scroll;" id="chat_bubbles_wrapper">
                                    {{--no messages info--}}
                                    @if(count($sub_task_messages) == 0)
                                        <div class="row py-4 justify-content-center">
                                            <h6>
                                                No messages
                                            </h6>
                                        </div>
                                    @endif
                                    {{--end no messages info--}}

                                    @foreach($sub_task_messages as $message)
                                        @if(in_array($message->user->is_employee, [0, 2, 4, 6]))
                                            <div class="row ">
                                                <div class="col-md-9 col-9">
                                                    <div class="row my-2 py-2" style="border: 2px solid lightblue; border-radius: 8px; background-color: #add8e644;">
                                                        <div class="col-md-12 text-left" style="font-weight: 800; font-size: 14px;">
                                                            {{($message->user->name ?? '') . ' ' . ($message->user->last_name ?? '')}}
                                                            @if(in_array($message->id, $notification_subtask_ids))
                                                                <i class="bi bi-bell-fill text-danger btn_clear_subtask_notification" style="cursor: pointer" data-notification="{{$notification_notification_ids[$message->id]}}"></i>
                                                            @endif
                                                            <span class="float-right" style="font-weight: 400; font-size: 10px; margin-top: 1px;">
                                                                {{\Carbon\Carbon::parse($message->created_at)->format('d F Y, h:i A')}}
                                                            </span>
                                                            {{--                                                    <br>--}}
                                                            {{--                                                    <span class="badge badge-danger badge-sm float-right">Today</span>--}}
                                                        </div>
                                                        <div class="col-md-12 text-left" style="font-weight: 400; font-size: 12px">
                                                            <p class="mb-0" style="word-wrap: break-word;">
                                                                {{ strip_tags(html_entity_decode($message->description)) }}
                                                                <br>
                                                                <span class="badge badge-sm badge-outline-dark mt-2 pl-0" style="border: 0px;">
                                                                    <b class="{!! array_unique($message->assign_members->pluck('status')->toArray()) !== [3] && \Carbon\Carbon::today() >= $message->duedate ? 'text-danger' : '' !!}">
                                                                        <i class="bi bi-alarm text-danger"></i>
                                                                        {{\Carbon\Carbon::parse($message->duedate)->format('l j M')}}
                                                                    </b>
                                                                </span>
                                                            </p>
{{--                                                            <p class="mb-0 mt-1" style="word-wrap: break-word;">--}}
{{--                                                                <b>--}}
{{--                                                                    <i class="bi bi-hourglass-split"></i>--}}
{{--                                                                    Monday 14 Jan--}}
{{--                                                                </b>--}}
{{--                                                            </p>--}}

                                                            <hr style="margin: 4px 0px 4px 0px !important;">

                                                            <span class="badge badge-success badge-sm btn_assign_subtask hover_box_shadow" style="cursor: pointer;" data-subtask="{{$message->id}}">
                                                                Assign
                                                            </span>

                                                            <span class="text-dark btn_read_more float-right" style="cursor: pointer; font-weight: 100;"
                                                                  data-text="{{$message->description}}"
                                                                  data-user="{{($message->user->name ?? '') . ' ' . ($message->user->last_name ?? '')}}"
                                                                  data-time="{{\Carbon\Carbon::parse($message->created_at)->format('d F Y, h:i A')}}">
                                                                <i class="bi bi-fullscreen"></i>
                                                            </span>

                                                            @if(count($message->assign_members))
                                                                <span class="badge badge-info badge-sm btn_view_assigned_members hover_box_shadow" style="cursor: pointer;" data-members="{{$message->assign_members}}">
                                                                    <i class="bi bi-people-fill"></i>
                                                                    {{count($message->assign_members)}}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-3"></div>
                                            </div>
                                        @elseif(in_array($message->user->is_employee, [1, 5]))
                                            <div class="row ">
                                                <div class="col-md-3 col-3"></div>
                                                <div class="col-md-9 col-9">
                                                    <div class="row my-2 py-2" style="border: 2px solid orange; border-radius: 8px; background-color: #ffa50044;">
                                                        <div class="col-md-12 text-left" style="font-weight: 800; font-size: 14px;">
                                                            {{($message->user->name ?? '') . ' ' . ($message->user->last_name ?? '')}}
                                                            <span class="float-right" style="font-weight: 400; font-size: 10px; margin-top: 1px;">
                                                                {{\Carbon\Carbon::parse($message->created_at)->format('d F Y, h:i A')}}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-12 text-left" style="font-weight: 400; font-size: 12px;">
                                                            @php
                                                                $string = strip_tags(html_entity_decode($message->description));
                                                            @endphp
                                                            <p class="mb-0" style="word-wrap: break-word;">
                                                                {!! $string !!}
                                                            </p>

                                                            <hr style="margin: 4px 0px 4px 0px !important;">

                                                            <span class="text-dark btn_read_more float-right" style="cursor: pointer; font-weight: 100;"
                                                                  data-text="{{$message->description}}"
                                                                  data-user="{{($message->user->name ?? '') . ' ' . ($message->user->last_name ?? '')}}"
                                                                  data-time="{{\Carbon\Carbon::parse($message->created_at)->format('d F Y, h:i A')}}">
                                                                <i class="bi bi-fullscreen"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{--search message info--}}
                                    @if($project_detail_search_message_query != '')
                                        @php
                                            $string = strip_tags(html_entity_decode($project_detail_search_message_query));
                                        @endphp
                                        <div class="row py-4 justify-content-center">
                                            <h6>
                                                Searched for "{{ (strlen($string) > 50) ? (substr($string, 0, 50) . '...') : $string }}"
                                                <a href="" class="text-danger" wire:click="$emit('mutate', {name: 'project_detail_search_message_query', value: ''})">
                                                    <i class="bi bi-x-octagon mt-2 text-danger"
                                                       style="font-size: 12px; cursor: pointer;">
                                                    </i>
                                                </a>
                                            </h6>
                                        </div>
                                    @endif
                                    {{--end search message info--}}
                                </div>
                                <div class="col-md-11 px-0" style="border: 1px solid #b7b7b7;">
                                    <textarea class="form-control" name="" id="textarea_send_message" cols="30" rows="1" placeholder="Type message..."></textarea>
                                </div>
                                <div class="col-md-1 px-0" style="background-color: #0076c2;">
                                    <button class="btn btn-block btn-primary" id="btn_send_message" data-project="{{$project->id}}">
                                        Send
                                    </button>
                                </div>
                            </div>

                            <hr style="margin: 14px 0px 12px 0px !important;">

                            @php
                                $files = $project->client_files()->whereHas('user')->get();
                            @endphp
                            <div class="row">
                                <div class="col-md-12 py-1" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <h6 class="my-1 mb-1"><b>FILES</b></h6>
                                    <hr style="margin: 4px 0px 4px 0px !important;">
                                    @if(count($files))
                                        <span type="button" class="badge badge-success badge-sm hover_box_shadow" id="btn_download_all_files" style="cursor: pointer; margin: 2px 0px;">
                                            <i class="fas fa-download"></i>
                                            Download all
                                        </span>
                                    @endif
                                    <span id="btn_upload" type="button" class="badge badge-dark badge-sm hover_box_shadow" style="cursor: pointer; margin: 2px 0px;">
                                        <i class="fas fa-upload"></i>
                                        Upload
                                    </span>
                                </div>
                                @if(count($files))
                                    <div class="col-md-12 px-0" style="border: 1px solid #b7b7b7;">
                                        <div class="row m-0 px-3 py-2"
                                             style="display: flex; flex-wrap: nowrap; /* Prevents wrapping to the next line */ overflow-x: auto; /* Enables horizontal scrolling */ gap: 10px; /* Adds spacing between items */ padding-bottom: 10px;"
                                        >
                                            @foreach($files as $client_file)
                                                @php
                                                    $color = 'black';
                                                    if (in_array($client_file->user->is_employee, [0, 2, 4, 6])) {
                                                        $color = 'lightblue';
                                                    } else if (in_array($client_file->user->is_employee, [1, 5])) {
                                                        $color = '#FFA500';
                                                    }

                                                    $temp = explode('.',$client_file->path);
                                                    $extension = end($temp);
                                                    $extension_check = in_array($extension, ['jpeg', 'jpg', 'png', 'webp', 'gif', 'avif', 'bmp']);

                                                    $file_src = asset('files/'.$client_file->path);
                                                    $image_src = $extension_check ? $file_src : asset('images/file-transparent.png');
                                                    $file_name = (limitTextAtWord($client_file->name, 20)) . '.' . $extension;
                                                    $file_name = (substr($client_file->name, 0, 15)) . '....' . $extension;
                                                    $actual_file_name = ($client_file->name) . '.' . $extension;
                                                    $file_author = ($client_file->user->name ?? '') . ' ' . ($client_file->user->last_name ?? '');
                                                    $file_timestamp = \Carbon\Carbon::parse($client_file->created_at)->format('d F Y, h:i A');
                                                @endphp
                                                <div class="file_wrapper">
                                                    @if($extension_check)
                                                        <a href="{{$file_src}}"
                                                           class="anchor_view_image"
                                                           data-lcl-txt="{{$actual_file_name}}"
                                                           data-lcl-author="{{$file_author}} at {{$file_timestamp}}"
                                                           data-lcl-thumb="{{$file_src}}"
                                                        >
                                                            <img src="{{$image_src}}" alt="" height="64">
                                                        </a>
                                                    @else
                                                        <img src="{{$image_src}}" alt="" height="64">
                                                    @endif
                                                    <br>
{{--                                                    <a href="{{$file_src}}" download>--}}
                                                        <span class="badge badge-dark badge-sm">#{{$client_file->id}}</span>
                                                        <a href="{{$file_src}}" download>
                                                            <span class="badge badge-success badge-sm" style="font-size: 10px; cursor: pointer;">
                                                                <i class="fas fa-download"></i>
                                                            </span>
                                                        </a>
                                                        <br>
                                                        <span style="font-size: 10px;">
                                                            {{$file_name}}
                                                        </span>
                                                        <br>
{{--                                                        <span style="color: {{$color}}; font-size: 10px;">--}}
                                                        <span style="font-size: 10px;">
                                                            {{$file_author}}
                                                        </span>
                                                        <br>
                                                        <span class="badge badge-primary badge-sm" style="font-size: 10px; background-color: {{$color}};">
                                                            {{$file_timestamp}}
                                                        </span>
{{--                                                    </a>--}}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign subtask</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            @php
                                $members = get_my_members_by_category($project->category_id);
                            @endphp
                            <select name="" id="assign_subtask_user_id" class="form-control">
                                <option value="">Select member</option>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{($member->name ?? '') . ' ' . ($member->last_name ?? '')}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <textarea name="" id="assign_subtask_comment" cols="30" rows="4" class="form-control" placeholder="Additional comments (OPTIONAL)"></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <button id="btn_save_changes_assign_subtask" class="btn btn-primary btn-sm btn-block">Assign</button>
                        </div>
                    </div>
                </div>
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" class="btn btn-primary" id="btn_save_changes_assign_subtask">Save changes</button>--}}
{{--                </div>--}}
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

    <!--  Message modal -->
    <div class="modal fade" id="fancybox_modal" tabindex="-1" role="dialog" style="width: 80% !important; margin: auto !important;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fancybox_modal_user_name">

                    </h5>
                    <p class="m-0" id="fancybox_modal_message_date">

                    </p>
                </div>
{{--                <div class="modal-body" id="fancybox-content" style="cursor: text!important; display: flex; flex-wrap: wrap;">--}}
                <div class="modal-body" id="fancybox-content" style="cursor: text!important;">
                </div>
            </div>
        </div>
    </div>

    <!--  Message modal -->
    <div class="modal fade" id="modal_assigned_members" tabindex="-1" role="dialog" style="width: 80% !important; margin: auto !important;">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Assigned members
                    </h5>
                </div>
                <div class="modal-body" style="cursor: text!important;">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="modal_tbody_assigned_members">
{{--                            <tr>--}}
{{--                                <td>Joh Doe</td>--}}
{{--                                <td>--}}
{{--                                    <span class="badge badge-sm badge-danger">Open</span>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--  Search messages modal -->
    <div class="modal fade" id="search_messages_modal" tabindex="-1" role="dialog" style="width: 80% !important; margin: auto !important;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="row m-0 p-0">
                        <div class="col-md-12 form-group p-0 m-0">
                            <input type="text" class="form-control form-control-lg" id="input_search_messages" placeholder="Press enter to search" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  Upload files modal -->
    <div class="modal fade" id="upload_files_modal" tabindex="-1" role="dialog" style="width: 80% !important; margin: auto !important;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="row m-0 p-0">
                        <div class="col-md-12 form-group p-4 m-0">
                            <input id="input_upload_files" type="file" name="images[]" multiple data-browse-on-zone-click="true">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let el3 = document.getElementById('chat_bubbles_wrapper');
        if (el3) el3.scrollTop = el3.scrollHeight;
    </script>
</div>
