@extends($layout)
@section('title', 'Service detail')
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
    </style>
@endpush
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Service detail</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        {{--brand detail--}}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="row text-center mb-4">
                    <div class="col-md-8 offset-md-2">
                        <h2>{{$project->name}}</h2>

                        <div class="col-12">
                            <b>Client: </b>
                            {{$project->client->name . ' ' . $project->client->last_name}}
                        </div>

                        <div class="col-12">
                            <b>Added By: </b>
                            {{$project->added_by->name . ' ' . $project->added_by->last_name}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-12 col-md-12">
                <h2 class="ml-3">Active Tasks</h2>
            </div>
            @php
                if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                    $task_index_route = 'admin.task.index';
                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                    $task_index_route = 'manager.task.index';
                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                    $task_index_route = 'sale.task.index';
                } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {
                    $task_index_route = 'support.task';
                }
            @endphp
            <div class="col-lg-12 col-md-12">
{{--                <a target="_blank" href="{{route($task_index_route, ['project_id' => $project->id])}}" class="btn btn-primary ml-3">View all tasks</a>--}}
                <a href="{{route($task_index_route, ['project_id' => $project->id])}}" class="btn btn-primary ml-3">View all tasks</a>
            </div>
        </div>

        @foreach($categories_with_active_tasks as $category_with_active_tasks)
            @if(count($category_with_active_tasks['tasks']))
                <div class="row my-4">
                    <div class="col-md-6 offset-md-3" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                        <div class="row">
                            <div class="col-md-12 text-center" style="border: 1px solid #b7b7b7; font-size: 16px;">
                                <b>{{$category_with_active_tasks['category']->name}}</b>
                                <br>
                            </div>
                            <div class="col-md-12 p-0" style="border-top: 1px solid #b7b7b7;" id="wrapper2"  >
                                {{--                                            <div class="row m-auto p-2" style="font-size: 15px;">--}}
                                {{--                                                <div class="col-md-12">--}}
                                <table class="table table-sm table-striped table-bordered mb-0">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Task</th>
                                        <th>Agent</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                            $show_route = 'admin.task.show';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $show_route = 'manager.task.show';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                            $show_route = 'sale.task.show';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4) {
                                            $show_route = 'support.task.show';
                                        }
                                    @endphp
                                    @foreach($category_with_active_tasks['tasks'] as $task)
                                        <tr class="text-center">
                                            <td style="vertical-align: middle;">
                                                <span class="badge badge-sm badge-dark">#{{$task->id}}</span>
                                            </td>
                                            {{--                                <td style="vertical-align: middle;"><a target="_blank" href="{{route($show_route, $task->id)}}">{!! \Illuminate\Support\Str::limit(strip_tags($task->description), 25, $end='...') !!}</a></td>--}}
                                            <td style="vertical-align: middle;"><a href="{{route($show_route, $task->id)}}">{!! \Illuminate\Support\Str::limit(strip_tags($task->description), 25, $end='...') !!}</a></td>
                                            <td style="vertical-align: middle;">{{$task->user->name}} {{$task->user->last_name}}</td>
                                            <td style="vertical-align: middle;">{!! $task->project_status_badge() !!}</td>
                                            <td style="vertical-align: middle;">
                                                {{--                                    <a target="_blank" href="{{route($show_route, $task->id)}}" class="btn btn-primary btn-icon btn-sm">--}}
                                                <a href="{{route($show_route, $task->id)}}" class="badge badge-primary badge-icon badge-sm">
                                                    <span class="ul-badge__icon"><i class="i-Eye"></i> View</span>
                                                </a>
                                                <a href="{{route($show_route, $task->id) . '?show-message=true'}}" class="badge badge-info badge-icon badge-sm">
                                                    <span class="ul-badge__icon"><i class="i-Speach-Bubble-3"></i> Message</span>
                                                </a>
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

                <div class="col-md-6 offset-md-3">
                    <hr>
                </div>
            @endif

        @endforeach
    </div>

</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {

    });
</script>
@endpush