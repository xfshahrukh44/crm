@extends('v2.layouts.app')

@section('title', 'Tasks')

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
        .task-actions-box {
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
                                    <h1 style="font-weight: 100;">Tasks</h1>
                                </div>
                                <div class="col-md-6 m-auto d-flex justify-content-end">
                                    {{--                                            <a href="{{route('v2.tasks.create')}}" class="btn btn-sm btn-success">--}}
                                    {{--                                                <i class="fas fa-plus"></i>--}}
                                    {{--                                                Create--}}
                                    {{--                                            </a>--}}
                                </div>
                            </div>

                            <br>

                            {{--                                    <div class="search-invoice">--}}
                            <form class="search-invoice" action="{{route('v2.tasks')}}" method="GET">
                                <input type="text" name="project" placeholder="Search By Project Name" value="{{ request()->get('project') }}">
                                <input type="text" name="id" placeholder="Search by ID" value="{{ request()->get('id') }}">
                                <input type="text" name="id" placeholder="Search by Project ID" value="{{ request()->get('project_id') }}">
                                @if(v2_acl([2, 4, 6]))
                                    <select name="brand_id" class="select2">
                                        <option value="">Select brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}" {{ request()->get('brand_id') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="name" placeholder="Search From Client Name Or Email" value="{{ request()->get('name') }}">
                                @endif
                                <select name="category_id" class="select2">
                                    <option value="">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{ request()->get('category_id') ==  $category->id ? 'selected' : ' '}}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="select2">
                                    <option value=""  {{ request()->get('status') ==  "" ? 'selected' : ' '}} >Any</option>
                                    <option value="0" {{ request()->get('status') ==  "0" ? 'selected' : ' '}} >Open</option>
                                    <option value="1" {{ request()->get('status') ==  "1" ? 'selected' : ' '}} >Re Open</option>
                                    <option value="4" {{ request()->get('status') ==  "4" ? 'selected' : ' '}} >In Progress</option>
                                    <option value="5" {{ request()->get('status') ==  "5" ? 'selected' : ' '}} >Sent for Approval</option>
                                    <option value="6" {{ request()->get('status') ==  "6" ? 'selected' : ' '}} >Incomplete Brief</option>
                                    <option value="2" {{ request()->get('status') ==  "2" ? 'selected' : ' '}} >Hold</option>
                                    <option value="3" {{ request()->get('status') ==  "3" ? 'selected' : ' '}} >Completed</option>
                                </select>

                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>
                            {{--                                    </div>--}}

                            @if(v2_acl([6, 4]))
                                <h2>
                                    <span class="text-danger">
                                        <i class="fas fa-bell"></i>
                                    </span>
                                    Task Notifications
                                </h2>
                                <table id="zero_configuration_table" class="table table-striped" style="width: 100%;">
                                    <thead>

                                    <th>ID</th>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Assigned To</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                    </thead>
                                    <tbody>
                                    @foreach($notify_data as $task)
                                        <tr>
                                            <td>{{$task->id}}</td>
                                            <td>
                                                <a class="p-2 bg-white" href="{{ route('v2.tasks.show', $task->id) }}">
                                                    {!! \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($task->description)), 30, $end='...') !!}
                                                </a>
                                            </td>
                                            <td>{{$task->projects->name}}</td>
                                            <td>
                                                {{ $task->projects->client->name }} {{ $task->projects->client->last_name }}
                                            </td>
                                            <td>
                                                {{ $task->user->name }} {{ $task->user->last_name }}
                                            </td>
                                            <td>
                                                <button class="badge bg-primary text-white badge-sm p-2" style="border: 0px;" title="{{ $task->brand->name }}">{{ $task->brand->name }}</button>
                                            </td>
                                            <td>
                                                <button class="badge badge-sm bg-dark text-white p-2" style="border: 0px;" title="{{ $task->category->name }}">{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $task->category->name))) }}</button>
                                                @if(count($task->member_list) != 0)
                                                    <ul id="" class="task-list-member-box">
                                                        @foreach($task->member_list as $key => $member)
                                                            <li>
                                                                {{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $member->user->name . ' ' . $member->user->last_name))) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td>{!! $task->project_status_badge() !!}</td>
                                            <td style="position: relative;">
                                                <a href="{{ route('v2.tasks.show', $task->id) }}" class="badge bg-dark badge-icon badge-sm text-white p-2">
                                                    <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                                    <span class="ul-btn__text">
                                                                    <i class="fas fa-eye"></i>
                                                                </span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <br />
                                <br />
                            @endif

                            <table id="zero_configuration_table" class="table-striped" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Assigned To</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Action</th>

                                </thead>
                                <tbody>
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>{{$task->id}}</td>
                                        <td>
                                            <a class="p-2 bg-transparent" href="{{ route('v2.tasks.show', $task->id) }}">
                                                {!! \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($task->description)), 30, $end='...') !!}
                                            </a>
                                        </td>
                                        <td>{{$task->projects->name}}</td>
                                        <td>
                                            {{ $task->projects->client->name }} {{ $task->projects->client->last_name }}
                                        </td>
                                        <td>
                                            {{ $task->user->name }} {{ $task->user->last_name }}
                                        </td>
                                        <td>
                                            <button class="badge bg-primary text-white badge-sm p-2" style="border: 0px;" title="{{ $task->brand->name }}">{{ $task->brand->name }}</button>
                                        </td>
                                        <td>
                                            <button class="badge badge-sm bg-dark text-white p-2" style="border: 0px;" title="{{ $task->category->name }}">{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $task->category->name))) }}</button>
                                            @if(count($task->member_list) != 0)
                                                <ul id="" class="task-list-member-box">
                                                    @foreach($task->member_list as $key => $member)
                                                        <li>
                                                            {{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $member->user->name . ' ' . $member->user->last_name))) }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>{!! $task->project_status_badge() !!}</td>
                                        <td style="position: relative;">
                                            <a href="{{ route('v2.tasks.show', $task->id) }}" class="badge bg-dark badge-icon badge-sm text-white p-2">
                                                <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                                <span class="ul-btn__text">
                                                                <i class="fas fa-eye"></i>
                                                            </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $tasks->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Notes Modal -->
        <div class="modal fade" id="modal_show_notes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Notes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <textarea class="form-control" name="" id="textarea_notes" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row" id="div_modifier_info" hidden>
                            <div class="col-md-12">
                                <label for="">
                                    <b>Last updated by:</b>
                                </label>
                            </div>
                            <div class="col-md-12">
                                <label for="" id="label_modifier_info">
                                    asd asd (12-12-12 12:12 AM)
                                </label>
                            </div>
                        </div>
                    </div>
                    {{--            <div class="modal-footer">--}}
                    {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    {{--            </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {

        });
    </script>

    <script>
        function toggleTaskActions(taskId) {
            const box = document.getElementById(`taskActionsBox_${taskId}`);
            document.querySelectorAll('.task-actions-box').forEach(el => {
                if (el !== box) el.classList.add('d-none');
            });
            box.classList.toggle('d-none');
        }

        // Optional: hide on outside click
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.task-actions-box') && !event.target.closest('button')) {
                document.querySelectorAll('.task-actions-box').forEach(el => el.classList.add('d-none'));
            }
        });
    </script>
@endsection
