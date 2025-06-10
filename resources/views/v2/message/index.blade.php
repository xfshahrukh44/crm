@extends('v2.layouts.app')

@section('title', 'Messages')

@section('css')
    <style>
        body {
            background: none;
        }

        /* CSS for client profile image as logo */
        .client-profile {
            position: relative;
            width: 40px;
            height: 40px;
            margin-right: 12px;
        }

        .client-profile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .client-profile span {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background-color: #4CAF50;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        .client-content h4 {
            margin-bottom: 2px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .client-content p {
            margin: 0;
            font-size: 12px;
            color: #777;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .main-client-details ul#myTab1 {
            overflow-y: scroll;
            display: block;
            height: 630px;

        }

        .client-brief .messages-wrapper {
            overflow-y: scroll;
            height: 64vh;
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="chat-integrate">
                    <div class="container-fluid">
                        <div class="row for-main-border align-items-center">
                            <div class="col-lg-3">

                            </div>
                            <div class="col-lg-6">
                                <div class="container contact-tab">
                                    <ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="Conversations-tab" data-toggle="tab"
                                                data-target="#Conversations" type="button" role="tab"
                                                aria-controls="Conversations" aria-selected="true">Conversations
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="Shortcodes-tab" data-toggle="tab" data-target="#Shortcodes"
                                                type="button" role="tab" aria-controls="Shortcodes"
                                                aria-selected="false">Shortcodes
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact"
                                                type="button" role="tab" aria-controls="contact" aria-selected="false">Contact
                                            </button>
                                        </li>

                                    </ul>


                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="icon-info">
                                    <div class="bell-img">
                                        <a href="javascript:;">
                                            <img src="images/icon.png" class="img-fluid">
                                            <span></span>
                                        </a>
                                    </div>
                                    <a href="javascript:;">
                                        <img src="images/circle.png" class="img-fluid">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="Conversations" role="tabpanel"
                                        aria-labelledby="Conversations-tab">
                                        <div class="row">
                                            <div class="col-lg-3 p-0">
                                                <div class="main-client-details">
                                                    <div class="search-container">
                                                        <input type="text" class="form-control search-input"
                                                            placeholder="Search...">
                                                        <i class="fas fa-search search-icon"></i>
                                                    </div>
                                                    <h3>All Conversations</h3>

                                                    <div class="container contact-tab">
                                                        <ul class="nav nav-tabs clients-list" id="myTab1" role="tablist">
                                                            @include('v2.message.partials.client_list', [
                                                                'clients_with_messages' => $clients_with_messages,
                                                            ])
                                                        </ul>
                                                    </div>

                                                    <div id="loading-spinner"
                                                        style="display: none; text-align: center; padding: 10px;">
                                                        <i class="fas fa-spinner fa-spin"></i> Loading more clients...
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-9 p-0">
                                                <div class="tab-content" id="myTabContent1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="Shortcodes" role="tabpanel" aria-labelledby="Shortcodes-tab">
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                            incididunt ut labore et dolore magna aliqua. Aliquam id diam maecenas ultricies mi
                                            eget mauris pharetra. Tincidunt lobortis feugiat vivamus at augue eget. Aliquet
                                            porttitor lacus luctus accumsan tortor posuere ac ut consequat. Massa massa
                                            ultricies mi quis hendrerit dolor.
                                        </p>
                                    </div>
                                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                        <p>
                                            I love cheese, especially stinking bishop cheese and biscuits. Stinking bishop
                                            cheesy feet brie fromage red leicester taleggio cut the cheese who moved my cheese.
                                            Red leicester cow hard cheese cheese slices cheese strings goat camembert de
                                            normandie cheesy grin. Gouda.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @break

            @default
        @endswitch
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let loading = false;
            let page = {{ $page }};
            let hasMore = true;

            $('.clients-list').scroll(function() {
                if (loading || !hasMore) return;

                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
                    loadMoreClients();
                }
            });

            function loadMoreClients() {
                loading = true;
                $('#loading-spinner').show();

                $.ajax({
                    url: '{{ route('v2.messages') }}',
                    type: 'GET',
                    data: {
                        page: page + 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.html) {
                            $('.clients-list').append(response.html);
                            page = response.next_page;
                            hasMore = response.has_more;
                        }
                        loading = false;
                        $('#loading-spinner').hide();
                    },
                    error: function() {
                        loading = false;
                        $('#loading-spinner').hide();
                    }
                });
            }

            // Handle client tab clicks to load messages
            $(document).on('click', '[data-toggle="tab"]', function(e) {
                const clientId = $(this).attr('id').replace('-tab', '');
                loadClientMessages(clientId);
            });

            function loadClientMessages(clientId) {
                // Check if tab content already exists
                if ($(`#${clientId}`).length) {
                    $(`#${clientId}`).tab('show');
                    return;
                }

                // Create new tab pane
                const tabPane = $(`
                    <div class="tab-pane fade" id="${clientId}" role="tabpanel" aria-labelledby="${clientId}-tab">
                        <div class="single-client-full-detail">
                            <div class="client-brief" id="messages-container-${clientId}">
                                <div class="chat-person">
                                    <h4>To: <span id="client-name-${clientId}"></span></h4>
                                </div>
                                <div class="messages-wrapper">
                                    <div class="loading-spinner" style="display: none; text-align: center;">
                                        Loading more messages...
                                    </div>
                                </div>
                                <div class="for-sending">
                                    <a href="javascript:;" class="emoji-picker">
                                        <img src="{{ asset('images/smile.png') }}" class="img-fluid">
                                    </a>
                                    <input type="text" placeholder="Type message here..." id="message-input-${clientId}">
                                    <a href="javascript:;" class="file-upload">
                                        <img src="{{ asset('images/file.png') }}" class="img-fluid">
                                    </a>
                                    <button type="submit" class="send-message" data-client-id="${clientId}">
                                        <img src="{{ asset('images/btn.png') }}" class="img-fluid">
                                    </button>
                                </div>
                            </div>
                            <div class="single-client">
                                <!-- Client info sidebar -->
                            </div>
                        </div>
                    </div>
                `);

                $('#myTabContent1').append(tabPane);
                $(`#${clientId}`).tab('show');

                // Load initial messages
                $.ajax({
                    url: "{{ url('v2/messages') }}/"+clientId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $(`#client-name-${clientId}`).text(response.user_name);
                        $(`#messages-container-${clientId} .messages-wrapper`).html(response.html);
                        scrollToBottom(clientId);
                        setupMessageScroll(clientId);
                    }
                });
            }

            function scrollToBottom(clientId) {
                const container = $(`#messages-container-${clientId} .messages-wrapper`);
                container.scrollTop(container[0].scrollHeight);
            }

            function setupMessageScroll(clientId) {
                const wrapper = $(`#messages-container-${clientId} .messages-wrapper`);
                let loading = false;
                let nextPage = null;
                let hasMore = true;

                wrapper.on('scroll', function() {
                    if (loading || !hasMore) return;

                    // Check if we're near the top (5% from top)
                    if (wrapper.scrollTop() <= (0.05 * wrapper[0].scrollHeight)) {
                        loadMoreMessages(clientId);
                    }
                });

                function loadMoreMessages(clientId) {
                    if (!nextPage) return; // No more pages to load

                    loading = true;
                    $(`#messages-container-${clientId} .loading-spinner`).show();

                    $.ajax({
                        url: "{{ url('v2/messages') }}/"+clientId,
                        type: 'GET',
                        data: {page: nextPage},
                        dataType: 'json',
                        success: function(response) {
                            if (response.html) {
                                // Store current scroll position
                                const oldScrollHeight = wrapper[0].scrollHeight;
                                const oldScrollTop = wrapper.scrollTop();

                                // Prepend older messages
                                wrapper.prepend(response.html);

                                // Maintain scroll position
                                const newScrollHeight = wrapper[0].scrollHeight;
                                wrapper.scrollTop(oldScrollTop + (newScrollHeight - oldScrollHeight));

                                // Update pagination state
                                nextPage = response.next_page;
                                hasMore = response.has_more;
                            }
                            loading = false;
                            $(`#messages-container-${clientId} .loading-spinner`).hide();
                        }
                    });
                }

                // Initialize pagination after first load
                $.ajax({
                    url: "{{ url('v2/messages') }}/"+clientId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        nextPage = response.next_page;
                        hasMore = response.has_more;
                    }
                });
            }

            // Handle sending new messages
            // $(document).on('click', '.send-message', function() {
            //     const clientId = $(this).data('client-id');
            //     const message = $(`#message-input-${clientId}`).val();

            //     if (message.trim()) {
            //         $.ajax({
            //             url: '/messages',
            //             type: 'POST',
            //             data: {
            //                 _token: '{{ csrf_token() }}',
            //                 client_id: clientId,
            //                 message: message
            //             },
            //             success: function(response) {
            //                 // Add new message to chat
            //                 const messageHtml = `
            //                     <div class="main-chat-message for-reply">
            //                         <div class="message-content">
            //                             <div class="message-line">
            //                                 <p>${message}</p>
            //                             </div>
            //                             <div class="message-time">
            //                                 <span>Just now</span>
            //                                 <span class="read-status">✓ Delivered</span>
            //                             </div>
            //                         </div>
            //                         <div class="message-img">
            //                             <img src="{{ asset(auth()->user()->image ?? 'assets/imgs/default-avatar.jpg') }}"
            //                                 class="img-fluid"
            //                                 alt="{{ auth()->user()->name }}">
            //                         </div>
            //                     </div>
            //                 `;

            //                 $(`#messages-container-${clientId} .messages-wrapper`).append(messageHtml);
            //                 $(`#message-input-${clientId}`).val('');
            //                 scrollToBottom(clientId);
            //             }
            //         });
            //     }
            // });
        });
    </script>
@endsection
