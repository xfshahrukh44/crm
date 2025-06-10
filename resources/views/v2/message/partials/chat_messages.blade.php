
    @foreach ($messages as $message)
        <div class="main-chat-message {{ $message->role_id == $is_employee ? 'for-reply' : '' }}">
            @if ($message->role_id != $is_employee)
                @php
                    $receiver_user = \App\Models\User::find($message->user_id);
                @endphp
                <div class="message-img">
                    <img src="{{ asset($receiver_user->image ?? 'assets/imgs/default-avatar.jpg') }}" class="img-fluid">
                </div>
            @endif

            <div class="message-content">
                @if ($message->message)
                    <div class="message-line">
                        <p>{!! nl2br($message->message) !!}</p>
                    </div>
                @endif

                <div class="message-time">
                    <span>{{ $message->created_at->format('h:i A, M d') }}</span>
                    @if ($message->sender_id == auth()->id())
                        @if ($message->is_read)
                            <span class="read-status">✓ Read</span>
                        @else
                            <span class="read-status">✓ Delivered</span>
                        @endif
                    @endif
                </div>
            </div>

            @if ($message->role_id == $is_employee)
                <div class="message-img">
                    <img src="{{ asset($user_image ?? 'assets/imgs/default-avatar.jpg') }}" class="img-fluid">
                </div>
            @endif
        </div>
    @endforeach

