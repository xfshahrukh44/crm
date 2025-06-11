@foreach($clients_with_messages as $index => $client_with_messages)
    @php
        $message = \App\Models\Message::where('user_id', $client_with_messages->id)
                    ->orWhere('sender_id', $client_with_messages->id)
                    ->orderBy('id', 'desc')
                    ->first();
    @endphp
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $index === 0 ? 'active' : '' }} client-tab" id="{{ $client_with_messages->id }}-tab"
            data-toggle="tab" data-target="#{{ $client_with_messages->id }}"
            type="button" role="tab"
            aria-controls="{{ $client_with_messages->id }}"
            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
            <div class="client-info-detail">
                <div class="client-profile">
                    <img src="{{ asset($client_with_messages->image ?? 'assets/imgs/default-avatar.jpg') }}"
                         class="img-fluid"
                         alt="{{ $client_with_messages->name }}">
                    <span></span>
                </div>
                <div class="client-content">
                    <h4>{{ $client_with_messages->name }} {{ $client_with_messages->last_name ?? '' }}</h4>
                    @if($message)
                        <p title="{{ strip_tags($message->message) }}">
                            {!! \Illuminate\Support\Str::limit(strip_tags($message->message), 25, '...') !!}
                        </p>
                    @else
                        <p>No messages yet</p>
                    @endif
                </div>
            </div>
        </button>
    </li>
@endforeach
