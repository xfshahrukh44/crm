@extends('layouts.app-support')
@push('styles')
<style>
    .ul-widget2__username {
       font-size: 0.8rem;
    }
    .ul-widget4__actions {flex: 0 0 220px;}
</style>
@endpush
@section('content')

<div class="breadcrumb">
    <h1 class="mr-2">Messages</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<section class="widgets-content">
    <!-- begin::users-->
    <div class="row mt-2">
        <form action="{{route('support.message.get.by.support')}}" style="width: 100%">
            <div class="col-xl-12">
                <input type="text" class="form-control mb-2" placeholder="Search client" name="client_name" value="{{request()->get('client_name')}}">
            </div>
            <div class="col-md-2 offset-md-5 mb-4">
                <button class="btn btn-block btn-primary" type="submit">Search client</button>
            </div>
        </form>
        <div class="col-xl-12">
            @foreach($clients_with_messages as $client_with_messages)
            @if($client_with_messages)
                @php
                    $message = \App\Models\Message::where('user_id', $user->id)->orWhere('sender_id', $user->id)->orderBy('id', 'desc')->first();
                @endphp
            @endif
            @if(isset($message) && $message)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="ul-widget__body">
                            <div class="tab-content pt-0 pb-0">
                                <div class="tab-pane active show">
                                    <div class="ul-widget1">
                                        <div class="ul-widget4__item ul-widget4__users">
                                            <div class="ul-widget4__img">
    {{--                                            @if($message['image'] == null)--}}
                                                <img id="userDropdown" src="{{ asset('newglobal/images/no-user-img.jpg') }}" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />
    {{--                                            @else--}}
    {{--                                            <img id="userDropdown" src="{{ asset($message['image']) }}" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />--}}
    {{--                                            @endif--}}
                                            </div>
                                            <div class="ul-widget2__info ul-widget4_qsers-info">
                                                <a class="ul-widget2__title" href="#">{{$client_with_messages->name}} {{$client_with_messages->last_name ?? ''}}</a>
                                                <span class="ul-widget2__username" href="#">{!! strip_tags($message->message) !!}</span>
                                            </div>
    {{--                                        <div class="ul-widget4__actions text-center">--}}
    {{--                                            <span class="badge badge-primary">{{ \Carbon\Carbon::parse($message['created_at'])->format('d M Y h:i A') }}</span>--}}
    {{--                                        </div>--}}
    {{--                                        @if($message['task_id'] != 0)--}}
    {{--                                            <div class="ul-widget4__actions text-right">--}}
    {{--                                                <a href="{{ route('support.task.show', $message['task_id']) }}" class="btn btn-outline-success m-1">View Details</a>--}}
    {{--                                            </div>--}}
    {{--                                        @else--}}
                                                <div class="ul-widget4__actions text-right">
                                                    <a href="{{ route('support.message.show.id', ['id' => $user->id, 'name' => $client_with_messages->name]) }}" class="btn btn-outline-success m-1">View Details</a>
                                                </div>
    {{--                                        @endif--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @endforeach
        </div>
    </div>
    <!-- end::users-->
</section>
@endsection

@push('scripts')
@endpush