@extends('layouts.app-billing')
@section('title', 'Home')
@section('content')

{{--    <div class="breadcrumb">--}}
{{--        <h1 class="mr-2">Dashboard</h1>--}}
{{--    </div>--}}
{{--    <div class="separator-breadcrumb border-top"></div>--}}

{{--    <div class="row">--}}
{{--        <div class="col-lg-12 col-md-12">--}}
{{--            <!-- CARD ICON-->--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-4 col-md-6 col-sm-6">--}}
{{--                    <div class="card card-icon mb-4">--}}
{{--                        <a href="#">--}}
{{--                            <div class="card-body text-center"><i class="i-Suitcase"></i>--}}
{{--                                <p class="text-muted mt-2 mb-2">Total tasks</p>--}}
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{ $total_tasks_count }}</p>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6 col-sm-6">--}}
{{--                    <div class="card card-icon mb-4">--}}
{{--                        <a href="#">--}}
{{--                            <div class="card-body text-center"><i class="i-Suitcase"></i>--}}
{{--                                <p class="text-muted mt-2 mb-2">Tasks in QA</p>--}}
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{ $sent_for_qa_count }}</p>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6 col-sm-6">--}}
{{--                    <div class="card card-icon mb-4">--}}
{{--                        <a href="#">--}}
{{--                            <div class="card-body text-center"><i class="i-Suitcase"></i>--}}
{{--                                <p class="text-muted mt-2 mb-2">Sent for approval</p>--}}
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{ $sent_for_approval_count }}</p>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6 col-sm-6">--}}
{{--                    <div class="card card-icon mb-4">--}}
{{--                        <a href="#">--}}
{{--                            <div class="card-body text-center"><i class="i-Suitcase"></i>--}}
{{--                                <p class="text-muted mt-2 mb-2">Completed</p>--}}
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{ $completed_count }}</p>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}


{{--        @if(count($recent_tasks))--}}
{{--            <div class="col-lg-6 col-md-12">--}}
{{--                <div class="card mb-2">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="card-title">Recent tasks</div>--}}

{{--                        @foreach($recent_tasks as $recent_task)--}}
{{--                            <div class="d-flex align-items-center border-bottom-dotted-dim pb-3 mb-3">--}}
{{--                                <img class="avatar-md rounded mr-3"--}}
{{--                                     src="{{ asset('newglobal/images/no-user-img.jpg') }}"--}}
{{--                                     alt="{{ $recent_task->user->name }} {{ $recent_task->user->last_name }}"/>--}}
{{--                                <div class="flex-grow-1">--}}
{{--                                    <h6 class="m-0">{{ $recent_task->user->name }} {{ $recent_task->user->last_name }}</h6>--}}
{{--                                    <p class="m-0 text-small text-muted">--}}
{{--                                        <a href="{{ route('qa.task.show', $recent_task->id) }}">{!! \Illuminate\Support\Str::limit(strip_tags($recent_task->description), 150, $end='...') !!}</a>--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        @if(count($todays_tasks))--}}
{{--            <div class="col-lg-6 col-md-12">--}}
{{--                <div class="card mb-2">--}}
{{--                    <div class="card-body" style="max-height: 350px; overflow-y: scroll;">--}}
{{--                        <div class="card-title">Today's taks</div>--}}

{{--                        @foreach($todays_tasks as $todays_task)--}}
{{--                            <div class="d-flex align-items-center border-bottom-dotted-dim pb-3 mb-3">--}}
{{--                                <img class="avatar-md rounded mr-3"--}}
{{--                                     src="{{ asset('newglobal/images/no-user-img.jpg') }}"--}}
{{--                                     alt="{{ $todays_task->user->name }} {{ $todays_task->user->last_name }}"/>--}}
{{--                                <div class="flex-grow-1">--}}
{{--                                    <h6 class="m-0">{{ $todays_task->user->name }} {{ $todays_task->user->last_name }}</h6>--}}
{{--                                    <p class="m-0 text-small text-muted">--}}
{{--                                        <a href="{{ route('qa.task.show', $todays_task->id) }}">{!! \Illuminate\Support\Str::limit(strip_tags($todays_task->description), 150, $end='...') !!}</a>--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}
{{--    </div>--}}
@endsection
