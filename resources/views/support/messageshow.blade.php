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
        <div class="col-xl-12">
            @foreach($message_array as $key => $message)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="ul-widget__body">
                        <div class="tab-content pt-0 pb-0">
                            <div class="tab-pane active show">
                                <div class="ul-widget1">
                                    <div class="ul-widget4__item ul-widget4__users">
                                        <div class="ul-widget4__img">
                                            @if($message['image'] == null)
                                            <img id="userDropdown" src="{{ asset('newglobal/images/no-user-img.jpg') }}" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />
                                            @else
                                            <img id="userDropdown" src="{{ asset($message['image']) }}" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />
                                            @endif
                                        </div>
                                        <div class="ul-widget2__info ul-widget4__users-info">
                                            <a class="ul-widget2__title" href="#">{{$message['f_name']}} {{$message['l_name']}}</a>
                                            <span class="ul-widget2__username" href="#">{!! strip_tags($message['message']) !!}</span>
                                        </div>
                                        @if($message['task_id'] != 0)
                                        <div class="ul-widget4__actions text-right">
                                            <a href="{{ route('support.task.show', $message['task_id']) }}" class="btn btn-outline-success m-1">View Details</a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <!-- end::users-->
</section>
@endsection

@push('scripts')
@endpush