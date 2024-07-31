@extends('layouts.app-manager')
@push('styles')
<style>
    .ul-widget2__username {
       font-size: 0.8rem;
    }
</style>
@endpush
@section('content')

<div class="breadcrumb">
    <h1 class="mr-2">Messages</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('manager.message') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="brand">Search By Brand</label>
                            <select name="brand" id="brand" class="form-control select2">
                                <option value="">Select Brand</option>
                                @foreach($brands as $key => $value)
                                    <option value="{{ $value->id }}" {{ Request::get('brand') ==  $value->id ? 'selected' : ' '}}>{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="message">Search By Message</label>
                            <input type="text" class="form-control" id="message" name="message" value="{{ Request::get('message') }}">
                        </div>
                        <div class="col-md-12">
                            <div class="text-right">
                                <button class="btn btn-primary">Search Result</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="separator-breadcrumb border-top"></div>
{{--<section class="widgets-content">--}}
{{--    <!-- begin::users-->--}}
{{--    <div class="row mt-2">--}}
{{--        <div class="col-xl-12">--}}
{{--            @foreach($messages as $key => $message)--}}
{{--            <div class="card mb-4">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="ul-widget__body">--}}
{{--                        <div class="tab-content pt-0 pb-0">--}}
{{--                            <div class="tab-pane active show">--}}
{{--                                <div class="ul-widget1">--}}
{{--                                    <div class="ul-widget4__item ul-widget4__users p-0">--}}
{{--                                        <div class="ul-widget4__img">--}}
{{--                                            @if($message->user->image == null)--}}
{{--                                                <img src="{{ asset('newglobal/images/no-user-img.jpg') }}" alt="{{ $message->user->name }}" />--}}
{{--                                            @else--}}
{{--                                                <img src="{{ asset($message->user->image) }}" alt="{{ $message->user->name }}" />--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                        <div class="ul-widget2__info ul-widget4__users-info">--}}
{{--                                            <a class="ul-widget2__title text-left" href="#">{{ $message->user->name }} {{ $message->user->last_name }}</a>--}}
{{--                                            <span class="ul-widget2__username text-left" href="#">{!! strip_tags($message->user->lastmessage->message) !!}</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="ul-widget4__actions text-left">--}}
{{--                                            <a href="{{ route('manager.message.show', ['id' => $message->user->id, 'name' => preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(str_replace(' ', '-', $message->user->name . '-' . $message->user->last_name))) ]) }}" class="btn btn-outline-success m-1">Message Details</a>--}}
{{--                                            <a href="{{ route('manager.client.details', ['id' => $message->user->id, 'name' => preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(str_replace(' ', '-', $message->user->name . '-' . $message->user->last_name))) ]) }}" class="btn btn-primary">Client Details</a>--}}
{{--                                            <br>--}}
{{--                                            <span class="badge badge-info">{{ $message->user->client->brand->name }}</span>--}}
{{--                                            <span class="badge badge-primary">{{ \Carbon\Carbon::parse($message->created_at)->format('d M Y h:i A') }}</span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="view-task-list-button">--}}
{{--                                        @if(count($message->user->projects) != 0)--}}
{{--                                        <div class="ul-widget4__actions">--}}
{{--                                        @foreach($message->user->projects as $project)--}}
{{--                                        @if(count($project->tasks) != 0)--}}
{{--                                        @foreach($project->tasks as $task)--}}
{{--                                        <a href="{{ route('manager.task.show', $task->id) }}" class="btn btn-outline-success m-1">View Details - {{ $task->category->name }}</a>--}}
{{--                                        @endforeach--}}
{{--                                        @endif--}}
{{--                                        @endforeach--}}
{{--                                        </div>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            @endforeach--}}
{{--            {{ $messages->links("pagination::bootstrap-4") }}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <!-- end::users-->--}}
{{--</section>--}}

<section class="widgets-content">
    <!-- begin::users-->
    <div class="row mt-2">
        <div class="col-xl-12">
            @foreach($data as $key => $value)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="ul-widget__body">
                            <div class="tab-content pt-0 pb-0">
                                <div class="tab-pane active show">
                                    <div class="ul-widget1">
                                        <div class="ul-widget4__item ul-widget4__users">
                                            <div class="ul-widget4__img">
                                                <img id="userDropdown" src="{{ asset('newglobal/images/no-user-img.jpg') }}" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />
                                            </div>
                                            <div class="ul-widget2__info ul-widget4__users-info">
                                                <a class="ul-widget2__title" href="#">
                                                    {{ $value->name }} {{ $value->last_name }} - <span>{{ $value->email }}</span>
                                                    <span class="badge badge-primary">{{ Request('message')}}</span>
                                                    <span class="badge badge-warning">{{ $value->client->brand->name }}</span>
                                                    <span class="badge badge-primary">{{ \Carbon\Carbon::parse($value->created_at)->format('d M Y h:i A') }}</span>
                                                </a>
                                                <span class="ul-widget2__username" href="#">
                                                @if($value->lastmessage != null )
                                                        {!! strip_tags($value->lastmessage->message) !!}
                                                    @endif
                                            </span>
                                            </div>
                                        </div>
                                        <div class="view-task-list-button">
                                            @foreach($value->projects as $key => $projects)
                                                <ul>
                                                    <li class="project"><a href="javascript:;" class="btn btn-info m-1">{{ $projects->name }}</a></li>
                                                    <li><span><i class="i-Bell text-muted header-icon i-Right"></i></span></li>
                                                    @foreach($projects->tasks as $task_key => $task)
                                                        <li><a href="{{ route('manager.task.show', $task->id) }}" target="_blank" class="btn btn-outline-success m-1">View Details - {{ $task->category->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            {{ $data->appends(['brand' => Request::get('brand'), 'message' => Request::get('message')])->links("pagination::bootstrap-4") }}
        </div>
    </div>
    <!-- end::users-->
</section>
@endsection

@push('scripts')
@endpush