{{-- If a particular client is set to show first, do it here first --}}
@if (isset($client) && $client)
    {{-- Display the client with messages --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link active client-tab"
            id="{{ $client->id }}-tab"
            data-toggle="tab"
            data-target="#{{ $client->id }}"
            type="button" role="tab"
            aria-controls="{{ $client->id }}"
            aria-selected="true">
            <div class="client-info-detail">
                <div class="client-profile position-relative">
                    <img src="{{ asset($client->image ?? 'assets/imgs/default-avatar.jpg') }}"
                         class="img-fluid"
                         alt="{{ $client->name }}">
                    <span></span>
                </div>

                <div class="client-content">
                    <h4>{{ $client->name }} {{ $client->last_name ?? '' }}</h4>

                    {{-- Display their last message if available --}}
                    @php
                        $message = \App\Models\Message::where('user_id', $client->id)
                                   ->orWhere('sender_id', $client->id)
                                   ->orderBy('id', 'desc')
                                   ->first();

                        $isUnread = $message &&
                                      $message->sender_id == $client->id &&
                                      $message->is_read == null;

                        $messagePreview = $message ? \Illuminate\Support\Str::limit(strip_tags($message->message), 25, '...') : '';
                    @endphp

                    @if ($isUnread)
                        <p class="unread-p" title="{{ strip_tags($message->message) }}">{{ $messagePreview }}</p>
                    @elseif ($message)
                        <p title="{{ strip_tags($message->message) }}">{{ $messagePreview }}</p>
                    @else
                        <p>No messages yet</p>
                    @endif
                </div>
            </div>
        </button>
    </li>
@endif

{{-- Check if clients_with_messages is not null and has data --}}
@if(!empty($clients_with_messages) && $clients_with_messages->count())
    @foreach($clients_with_messages as $index => $client_with_messages)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $client ? '' : ($index === 0 ? 'active' : '') }} client-tab"
                id="{{ $client_with_messages->id }}-tab"
                data-toggle="tab"
                data-target="#{{ $client_with_messages->id }}"
                type="button" role="tab"
                aria-controls="{{ $client_with_messages->id }}"
                aria-selected="{{ $client ? 'false' : ($index === 0 ? 'true' : 'false') }}">

                <div class="client-info-detail">
                    <div class="client-profile position-relative">
                        <img src="{{ asset($client_with_messages->image ?? 'assets/imgs/default-avatar.jpg') }}"
                             class="img-fluid"
                             alt="{{ $client_with_messages->name }}">
                        <span></span>
                    </div>

                    <div class="client-content">
                        <h4>{{ $client_with_messages->name }} {{ $client_with_messages->last_name ?? '' }}</h4>

                        @php
                            $message = \App\Models\Message::where('user_id', $client_with_messages->id)
                                       ->orWhere('sender_id', $client_with_messages->id)
                                       ->orderBy('id', 'desc')
                                       ->first();

                            $isUnread = $message &&
                                          $message->sender_id == $client_with_messages->id &&
                                          $message->is_read == null;

                            $messagePreview = $message ? \Illuminate\Support\Str::limit(strip_tags($message->message), 25, '...') : '';
                        @endphp

                        @if ($isUnread)
                            <p class="unread-p" title="{{ strip_tags($message->message) }}">{{ $messagePreview }}</p>
                        @elseif ($message)
                            <p title="{{ strip_tags($message->message) }}">{{ $messagePreview }}</p>
                        @else
                            <p>No messages yet</p>
                        @endif
                    </div>
                </div>
            </button>
        </li>
    @endforeach
@endif
