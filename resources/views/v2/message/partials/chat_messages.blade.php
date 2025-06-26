@foreach ($messages as $message)
    @php
        $created_at = \Carbon\Carbon::parse($message->created_at);
        $now = \Carbon\Carbon::now();
    @endphp
    <div class="main-chat-message {{ $message->role_id == $is_employee ? '' : 'for-reply' }}">
        @if ($message->role_id == $is_employee)
            <div class="message-img client-img">
                <img src="{{ asset($user_image ?? 'assets/imgs/default-avatar.jpg') }}" class="img-fluid">
            </div>
        @endif

        <div class="message-content">
            @if ($message->message)
                <div class="message-line">
                    <h6 id="msg-text-{{ $message->id }}">{!! nl2br($message->message) !!}</h6>
                    @if ($now->diffInMinutes($created_at) <= 10)
                        <a href="#" class="edit-message" data-message-id="{{ $message->id }}">Edit</a>
                    @endif

                    @if (count($message->sended_client_files) != 0)
                        <div class="msg-img">
                            @foreach ($message->sended_client_files as $key => $client_file)
                                @php
                                    $filePath = asset('files/' . $client_file->path);
                                    $fileExtension = strtolower(pathinfo($client_file->path, PATHINFO_EXTENSION));
                                    $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp

                                @if ($isImage)
                                    <a href="{{ $filePath }}" target="_blank" title="{{ $client_file->name }}">
                                        <img src="{{ $filePath }}" alt="Image"
                                            style="max-width: 150px; max-height: 150px; border-radius: 5px;">
                                    </a>
                                @else
                                    <a href="{{ $filePath }}" download title="{{ $client_file->name }}">
                                        {{ substr($client_file->name, 0, 21) . '...' }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="message-time">
                <span>{{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Karachi')->format('h:i A, M d') }}</span>
            </div>
        </div>
        @if ($message->role_id != $is_employee)
            @php
                $receiver_user = \App\Models\User::find($message->user_id);
            @endphp
            <div class="message-img you-img">
                <img src="{{ asset($receiver_user->image ?? 'assets/imgs/default-avatar.jpg') }}" class="img-fluid">
            </div>
        @endif

    </div>
@endforeach
