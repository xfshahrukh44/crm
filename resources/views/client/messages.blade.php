@extends('client.layouts.app')
@section('title', 'Messages')

@section('css')

@endsection

@section('content')
    @php
        $messages = \App\Models\Message::where('user_id', auth()->user()->id)->orWhere('sender_id', auth()->user()->id)
                ->orWhere('client_id', auth()->user()->id)
                ->get();
    @endphp
    <section class="dashboard my">
        <div id="loading-screen" style="
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            text-align: center;
            color: white;
            font-size: 20px;
            line-height: 100vh;">
            Please wait...
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="custom-chat-body">
                        <div class="chat-header">
                            <div class="chat-profile">
                                <div class="profile-detail">
                                    <button>
                                        <img src="{{asset(auth()->user()->client->brand->logo ?? 'images/sidebarlogo.png')}}" class="img-fluid" alt="" style="max-width: 50px;">
                                    </button>
                                </div>
                                <a class="nav-link" href="#" role="button">
                                    {{auth()->user()->client->brand->name ?? 'Design CRM'}}
                                </a>
                            </div>
                            <div class="maximize-chat">
{{--                                <button id="redirectButton">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"--}}
{{--                                         class="bi bi-arrows-angle-contract" viewBox="0 0 16 16">--}}
{{--                                        <path fill-rule="evenodd"--}}
{{--                                              d="M.172 15.828a.5.5 0 0 0 .707 0l4.096-4.096V14.5a.5.5 0 1 0 1 0v-3.975a.5.5 0 0 0-.5-.5H1.5a.5.5 0 0 0 0 1h2.768L.172 15.121a.5.5 0 0 0 0 .707M15.828.172a.5.5 0 0 0-.707 0l-4.096 4.096V1.5a.5.5 0 1 0-1 0v3.975a.5.5 0 0 0 .5.5H14.5a.5.5 0 0 0 0-1h-2.768L15.828.879a.5.5 0 0 0 0-.707"/>--}}
{{--                                    </svg>--}}
{{--                                </button>--}}
                            </div>
                        </div>
                        <div class="chat-container">
                            <div class="conversions">
{{--                                <span class="chat-data">--}}
{{--                                    29 Nov 24--}}
{{--                                </span>--}}
                                @foreach($messages as $message)
                                    @php
                                        $bubble_class = $message->role_id == Auth()->user()->is_employee ? 'sender' : 'reciver';
                                        $timestamp_class = $message->role_id == Auth()->user()->is_employee ? 'text-end' : 'text-start';
                                        $profile_image = $message->role_id == Auth()->user()->is_employee ? (
                                            auth()->user()->image ?? 'images/avatar.png'
                                        ) : (
                                            auth()->user()->client->brand->logo ?? 'images/sidebarlogo.png'
                                        );
                                    @endphp
                                    <div class="messages {{ $bubble_class }}">
                                        <button>
                                            <img src="{{asset($profile_image)}}" class="img-fluid" alt="">
                                        </button>
                                        <div class="text-area">
                                            <p>{!! nl2br($message->message) !!}</p>

                                            @if(count($message->sended_client_files) != 0)
                                                <br>
                                                <div class="file-wrapper">
                                                    <table class="table table-sm table-striped table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <th class="text-center" style="color: #e99e15;">
                                                                    <i class="fas fa-file mr-4" style="font-size: 12px;"></i>
                                                                    Attached files
                                                                </th>
                                                            </tr>
                                                            @foreach($message->sended_client_files as $key => $client_file)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <a href="{{asset('files/'.$client_file->path)}}" download="">{{$client_file->name}}</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif

                                            <span class="{{$timestamp_class}}" style="font-size: 10px; color: grey;">
                                                {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('America/New_York')->format('d M Y h:i A') }} (EST)
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="chat-footer">
                                <form class="form" action="{{ route('client.send.messages') }}" enctype="multipart/form-data" method="post">
                                    @csrf
                                    <div class="chat-icons">
                                        <span>
                                            <label for="file-upload" class="custom-file-upload">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#0076c2"
                                                     class="bi bi-paperclip" viewBox="0 0 16 16">
                                                    <path
                                                        d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z"/>
                                                </svg>
                                            </label>
                                            <input id="file-upload" type="file" name="h_Item_Attachments_FileInput[]" multiple />
                                        </span>
                                    </div>
                                    <textarea name="message" placeholder="Type something..." id=""></textarea>
                                    <button class="send">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#0076c2"
                                             class="bi bi-send" viewBox="0 0 16 16">
                                            <path
                                                d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            var chatContainer = $('.conversions');
            chatContainer.scrollTop(chatContainer.prop('scrollHeight'));

            $('#file-upload').on('change', function () {
                toastr.success('File attached');
            });

            $('.form').on('submit', function (e) {
                $('#loading-screen').show();
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.text-area p').forEach((p) => {
                if (!p.textContent.trim()) {
                    p.remove();
                }
            });

            document.querySelectorAll('.text-area br').forEach((br) => {
                if (!br.textContent.trim()) {
                    br.remove();
                }
            });
        });
    </script>
@endsection
