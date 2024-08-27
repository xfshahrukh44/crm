@extends('layouts.app-support')
@push('styles')
<style>
    .ul-widget2__username {
       font-size: 0.8rem;
    }
    .ul-widget4__actions {flex: 0 0 220px;}

    .unread_notification {
        background-color: #2978c21c;
    }
</style>
@endpush
@section('content')

<div class="breadcrumb">
    <h1 class="mr-2"><i class="i-Bell"></i> Notifications</h1>
    <a href="{{route('notification.all.read')}}" class="btn btn-primary">Mark all as read</a>
</div>
<div class="separator-breadcrumb border-top"></div>
<section class="widgets-content">
    <!-- begin::users-->
    <div class="row mt-2">
{{--        <form action="{{route('support.message.get.by.support')}}" style="width: 100%">--}}
{{--            <div class="col-xl-12">--}}
{{--                <input type="text" class="form-control mb-2" placeholder="Search client" name="client_name" value="{{request()->get('client_name')}}">--}}
{{--            </div>--}}
{{--            <div class="col-md-2 offset-md-5 mb-4">--}}
{{--                <button class="btn btn-block btn-primary" type="submit">Search client</button>--}}
{{--            </div>--}}
{{--        </form>--}}
        <div class="col-xl-12">

            @php
                $notifications = auth()->user()->notifications()->latest()->paginate(20);
            @endphp
            @foreach($notifications as $notification)
                @php
                    $route = '#';
                    $badge = '';
                    if ($notification->type == 'App\Notifications\AssignProjectNotification') {
                        $badge = '<span class="badge badge-info">Project</span>';
                        $route = route('create.task.by.project.id', ['id' => $notification->data['project_id'], 'name' => $notification->data['text'], 'notify' => $notification->id]);
                    } else if ($notification->type == 'App\Notifications\TaskNotification') {
                        $badge = '<span class="badge badge-primary">Task</span>';
                        $route = route('support.task.show', ['id' => $notification->data['task_id'], 'notify' => $notification->id]);
                    } else if ($notification->type == 'App\Notifications\MessageNotification') {
                        $badge = '<span class="badge badge-warning">Message</span>';
                        $route = route('support.message.show.id', ['id' => $notification->data['id'], 'name' => $notification->data['name']]);
                    } else {
                        $badge = '<span class="badge badge-primary">Task</span>';
                        $task_id = 0;
                        $project = \App\Models\Project::where('client_id', $notification->data['id'])->first();
                        if($project != null){
                            if(count($project->tasks) != 0){
                                $task_id = $project->tasks[0]->id;
                            }
                        }
                        $route = route('support.task.show', ['id' => $task_id, 'notify' => $notification->id]);
                    }
                @endphp
                <div class="card mb-4 {!! is_null($notification->read_at) ? 'unread_notification' : '' !!}" data-id="{{$notification->id}}">
                    <a href="{{$route}}">
                        <div class="card-body p-0">
                            <div class="ul-widget__body">
                                <div class="tab-content pt-0 pb-0">
                                    <div class="tab-pane active show">
                                        <div class="ul-widget1">
                                            <div class="ul-widget4__item ul-widget4__users">
                                                    <div class="ul-widget2__info ul-widget4_qsers-info">
        {{--                                                <a class="ul-widget2__title" href="#">John Doe</a>--}}
                                                        <h5>
                                                            {!! $badge ?? '' !!}
                                                        </h5>
                                                        <h4 style="font-weight: 100;" href="#">{{ strip_tags($notification->data['text']) }}</h4>
                                                        <h6 href="#" class="text-info">Name: {{$notification->data['name']}}</h6>
                                                        <h6 style="" href="#" class="text-primary">{{Carbon\Carbon::parse($notification->created_at)->format('d F Y, h:i A')}}</h6>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
            {{ $notifications->links()  }}

        </div>
    </div>
    <!-- end::users-->
</section>
@endsection

@push('scripts')
    <script>
        $(document).ready(() => {
            $('.unread_notification').on('click', function (e) {
                // e.preventDefault();
                $(this).removeClass('unread_notification');

                $.ajax({
                    url: "{{route('clear-notification')}}",
                    method: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        notification_id: $(this).data('id')
                    },
                    success: (data) => {
                        console.log(data);
                        window.location.href = $(this).find('a:first').prop('href');
                    },
                });


            });
        });
    </script>
@endpush