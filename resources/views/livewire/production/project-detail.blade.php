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
        <h1 class="mr-2">Project detail</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
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

                                <span class="badge badge-primary badge-sm mx-1">
                                    {{ $project->brand->name }}
                                </span>

                                <span class="badge badge-dark badge-sm mx-1">
                                    {{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $project->category->name))) }}
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

                            <div class="row mt-2">
                                <div class="col-md-6 offset-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <b>STATUS</b>
                                        </div>
                                        <div class="col">
                                            <label class='badge badge-danger' wire:click="set_project_status({{$project->id}}, 0)" style="cursor: pointer; {!! $project->status == 0 ? 'border: 3px solid #00aeef; border-radius: 50px;' : '' !!}">
                                                Open
                                            </label>

                                            <label class='badge badge-primary' wire:click="set_project_status({{$project->id}}, 1)" style="cursor: pointer; {!! $project->status == 1 ? 'border: 3px solid #00aeef; border-radius: 50px;' : '' !!}">
                                                Reopen
                                            </label>

                                            <label class='badge badge-warning' wire:click="set_project_status({{$project->id}}, 4)" style="cursor: pointer; {!! $project->status == 4 ? 'border: 3px solid #00aeef; border-radius: 50px;' : '' !!}">
                                                In Progress
                                            </label>

                                            <label class='badge badge-info' wire:click="set_project_status({{$project->id}}, 2)" style="cursor: pointer; {!! $project->status == 2 ? 'border: 3px solid #00aeef; border-radius: 50px;' : '' !!}">
                                                Hold
                                            </label>

                                            <label class='badge badge-warning' wire:click="set_project_status({{$project->id}}, 6)" style="cursor: pointer; {!! $project->status == 6 ? 'border: 3px solid #00aeef; border-radius: 50px;' : '' !!}">
                                                Incomplete Brief
                                            </label>

                                            <label class='badge badge-success' wire:click="set_project_status({{$project->id}}, 3)" style="cursor: pointer; {!! $project->status == 3 ? 'border: 3px solid #00aeef; border-radius: 50px;' : '' !!}">
                                                Completed
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row mt-2">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    Conversation
                                </div>
                                <div class="col-md-12 px-4" style="border: 1px solid #b7b7b7; max-height: 450px; overflow-y: scroll;" id="chat_bubbles_wrapper">
                                    @foreach($project->sub_tasks_default_order as $message)
                                        @if(in_array($message->user->is_employee, [0, 2, 4, 6]))
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="row my-2 py-2" style="border: 2px solid lightblue; border-radius: 10px; background-color: #add8e644;">
                                                        <div class="col-md-12 text-left" style="font-weight: 800; font-size: 14px;">
                                                            {{($message->user->name ?? '') . ' ' . ($message->user->last_name ?? '')}}
                                                            <span class="float-right" style="font-weight: 400; font-size: 10px; color: #161676; margin-top: 1px;">
                                                                {{\Carbon\Carbon::parse($message->created_at)->format('d F Y, h:i A')}}
                                                            </span>
                                                            {{--                                                    <br>--}}
                                                            {{--                                                    <span class="badge badge-danger badge-sm float-right">Today</span>--}}
                                                        </div>
                                                        <div class="col-md-12 text-left" style="font-weight: 400; font-size: 12px">
                                                            {{ (strlen(strip_tags($message->description)) > 33) ? (substr(strip_tags($message->description), 0, 33) . '...') : strip_tags($message->description) }}

                                                            <span data-fancybox data-src="#fancybox-content" data-text="{{$message->description}}" class="btn_read_more badge badge-primary badge-sm" style="cursor: pointer;">
                                                                Read more
                                                            </span>
                                                            <br>

                                                            <hr style="margin: 4px 0px 4px 0px !important;">

                                                            <span class="badge badge-success badge-sm btn_assign_subtask" style="cursor: pointer;" data-subtask="{{$message->id}}">
                                                                Assign subtask
                                                            </span>

                                                            @if(count($message->assign_members))
                                                                <span class="badge badge-danger badge-sm">
                                                                    {{count($message->assign_members)}}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4"></div>
                                            </div>
                                        @elseif(in_array($message->user->is_employee, [1, 5]))
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8">
                                                    <div class="row my-2 py-2" style="border: 2px solid orange; border-radius: 10px; background-color: #ffa50044;">
                                                        <div class="col-md-12 text-left" style="font-weight: 800; font-size: 14px;">
                                                            Production
                                                            <span class="float-right" style="font-weight: 400; font-size: 10px; color: #161676; margin-top: 1px;">
                                                        08 Aug 2024, 04:07 AM
                                                    </span>
                                                        </div>
                                                        <div class="col-md-12 text-left" style="font-weight: 400; font-size: 12px;">
                                                            Check the link provided in the asdasd jaj shdjk asd ashd asgdj....
                                                            <span data-fancybox data-src="#fancybox-content" class="btn_read_more badge badge-primary badge-sm" style="cursor: pointer;">
                                                                Read more
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-md-12 px-0" style="border: 1px solid #b7b7b7;">
                                    <textarea class="form-control" name="" id="" cols="30" rows="1" placeholder="Type message..."></textarea>
                                </div>
                                <div class="col-md-12 px-0">
                                    <button class="btn btn-block btn-primary">
                                        Send
                                    </button>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    Files
                                </div>
                                <div class="col-md-12 px-0" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                                    <table class="table table-bordered table-striped table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th colspan="3">
                                                    <span type="button" class="badge badge-success badge-sm btn_download_all_files" style="cursor: pointer;">
                                                        <i class="fas fa-download"></i>
                                                        Download all files
                                                    </span>
                                                    <span type="button" class="badge badge-dark badge-sm btn_download_all_files" style="cursor: pointer;">
                                                        <i class="fas fa-upload"></i>
                                                        Upload
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>File</th>
                                                <th>Uploader</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="#">
                                                        <span class="badge badge-dark badge-sm">#123456</span>
                                                        ASD.docx
                                                    </a>
                                                </td>
                                                <td>
                                                    <span style="color: #FFA500;">Production</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary badge-sm">
                                                        13 February 2025, 05:43 AM
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="#">
                                                        <span class="badge badge-dark badge-sm">#123456</span>
                                                        ASD123.docx
                                                    </a>
                                                </td>
                                                <td>
                                                    <span style="color: lightblue;">Sales</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary badge-sm">
                                                        13 February 2025, 05:43 AM
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                        <span aria-hidden="true">&times;</span>
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

    <div id="fancybox-content" style="display: none;">
{{--        <div class="p-5">--}}
{{--            asdsadasd--}}
{{--        </div>--}}
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css">
    <!-- Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script>
        $('form').on('submit', function () {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.btn_read_more').on('click', function () {
                $('#fancybox-content').html($(this).data('text'));
            });

            let subtask_id = '';
            $('.btn_assign_subtask').on('click', function () {
                subtask_id = $(this).data('subtask');
                $('#exampleModalCenter').modal('show');
            });

            $('#btn_save_changes_assign_subtask').on('click', function () {
               let val = $('#assign_subtask_user_id').val();
               let comment = $('#assign_subtask_comment').val() ?? '';
               if (val == '') {
                   alert('Please select a valid option');
                   return false;
               }

                $('#exampleModalCenter').modal('hide');
                $('#assign_subtask_user_id').val('');
                $('#assign_subtask_comment').val('');

                Livewire.emit('assign_subtask', {
                    subtask_id: subtask_id,
                    member_id: val,
                    comment: comment,
                });
                return false;
            });


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
