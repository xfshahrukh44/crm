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

        .file-preview-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .for-reply .msg-img a {
            color: #ffffff;
        }

        p.unread-p {
            font-weight: 900;
            font-size: 14px;
            color: #1d88f6;
        }

        .disabled-click {
            pointer-events: none;
            opacity: 0.5;
        }

        .single-client .client-profile {
            width: 100px;
            height: 90px;
        }

        .single-client .client-content h4 {
            font-size: 20px;
        }

        .edit-message {
            position: relative;
            padding-left: 20px;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9em;
            cursor: pointer;
        }

        .edit-message::before {
            content: '\f040';
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 0;
            color: #ffffff;
        }

        .no-messages {
            text-align: center;
            margin-top: 187px;
        }

        .emoji-container {
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            width: 198px;
            max-height: 200px;
            overflow-y: auto;
            bottom: 230px;
        }

        .for-slider-main-banner {

            margin-left: 248px;
        }

        .main-chat-message.for-reply h6 {
            color: white;
        }

        @media(max-width:1440px) {
            .for-slider-main-banner {
                margin-left: 181px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @if ($clients_with_messages->isEmpty())
            <div class="no-messages">
                <h3>No conversations found</h3>
                <p>Start a new conversation to get started.</p>
            </div>
        @else
            <section class="chat-integrate">
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
                                                <input type="hidden" id="active-id" value="">
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
        @endif
    </div>
@endsection

@section('script')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            let loading = false;
            let page =  2;
            let hasMore = true;
            let searchQuery = '';
            const urlParams = new URLSearchParams(window.location.search);
            const getClientId = urlParams.get('clientId');

            $('.clients-list').scroll(function() {
                if (loading || !hasMore) return;

                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
                    loadMoreClients();
                }
            });

            $('.search-input').on('input', function() {
                searchQuery = $(this).val();
                page = 1;
                hasMore = true;
                $('.clients-list').empty();
                loadMoreClients();
            });

            function loadMoreClients() {
                loading = true;
                $('#loading-spinner').show();

                $.ajax({
                    url: '{{ route('v2.messages') }}',
                    type: 'GET',
                    data: {
                        page: page,
                        client_name: searchQuery
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.html) {
                             // Check for duplicates before appending
                            let tempElement = $('<div>').html(response.html);
                            let newClientIds = [];

                            tempElement.find('.nav-item button').each(function() {
                                newClientIds.push($(this).attr('id'));
                            });

                            newClientIds.forEach(function(id) {
                                if ($('.clients-list').find('#' + id).length === 0) {
                                    // Only append if this client does not already exist
                                    $('.clients-list').append(tempElement.find('#' + id).closest('.nav-item'));
                                }
                            });

                            // Remove active from all first
                            $('.clients-list .nav-item .nav-link.active').removeClass('active');

                            // Then add active to the first one
                            $('.clients-list .nav-item:first-child .nav-link').addClass('active');

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
                $('#active-id').val(clientId);
                loadClientMessages(clientId);
            });

            // Load the first chat automatically when page loads
            function loadFirstChat() {
                const tabs = $('.client-tab');
                if (!tabs.length) return; // No tabs found

                // Find active tab or default to first
                let activeTab = tabs.filter('.active').first();
                if (!activeTab.length) {
                    activeTab = tabs.first();
                    activeTab.addClass('active').tab('show'); // For Bootstrap tabs
                }

                const clientId = activeTab.attr('id').replace('-tab', '');
                $('#active-id').val(clientId);
                loadClientMessages(clientId);
            }

            if (getClientId) {
                loadClientMessages(getClientId);
            } else {
                loadFirstChat();
            }

            // Call this on page load
            // loadFirstChat();

            function loadClientMessages(clientId) {
                // Hide all other tab panes
                $('#myTabContent1 .tab-pane').remove(); // This line removes all existing tab panes

                // Create new tab pane
                const tabPane = $(`
                    <div class="tab-pane fade show active" id="${clientId}" role="tabpanel" aria-labelledby="${clientId}-tab">
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
                                <div class="for-sending" id="dropzone-${clientId}" data-client-id="${clientId}">
                                    <a href="javascript:;" class="emoji-picker" id="emoji-button-${clientId}" data-target="message-input-${clientId}">
                                        <img src="{{ asset('images/smile.png') }}" class="img-fluid">
                                    </a>
                                    <input type="text" class="message-input" placeholder="Type message here..." id="message-input-${clientId}">
                                    <a href="javascript:;" class="file-upload" data-client-id="${clientId}">
                                        <img src="{{ asset('images/file.png') }}" class="img-fluid">
                                    </a>
                                    <input type="file" id="file-input-${clientId}" multiple style="display:none;" />

                                    <button type="submit" class="send-message" data-client-id="${clientId}">
                                        <img src="{{ asset('images/btn.png') }}" class="img-fluid">
                                    </button>
                                    <div class="file-preview-container" id="file-preview-${clientId}"></div>
                                </div>
                            </div>
                            <div class="single-client">
                                <div class="for-cross">
                                    <a href="javascript:;">
                                        <img src="images/close-icon.png" class="img-fluid">
                                    </a>
                                </div>
                                <div class="client-info-detail">
                                    <div class="client-profile">
                                        <img src="" class="img-fluid" id="client-image-${clientId}">
                                        <a href="javascript:;">
                                            <span></span>
                                        </a>
                                    </div>
                                    <div class="client-content">
                                        <h4 id="client-content-${clientId}"></h4>
                                        <!--<p>Lorem, Lipsum</p>-->
                                    </div>
                                </div>
                                <div class="container contact-tab new-setting-tab">
                                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="Files-tab"
                                                data-bs-toggle="tab" data-bs-target="#Files"
                                                type="button" role="tab" aria-controls="Files"
                                                aria-selected="true">Files
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent2">
                                        <div class="tab-pane fade show active" id="Files"
                                            role="tabpanel" aria-labelledby="Files-tab">
                                            <h4>Recent files</h4>
                                            <div class="recent-files" id="recent-files-${clientId}"></div>

                                            <h4>Uploaded Photos</h4>
                                            <div class="upload-photos" id="uploaded-photos-${clientId}"></div>
                                        </div>
                                        <div class="tab-pane fade" id="Setting"
                                            role="tabpanel" aria-labelledby="Setting-tab">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                `);

                $('#myTabContent1').append(tabPane);

                // Load all messages
                $.ajax({
                    url: "{{ url('v2/messages') }}/" + clientId,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $(`#messages-container-${clientId} .loading-spinner`).show();
                    },
                    success: function(response) {
                        $(`#client-name-${clientId}`).text(response.user_name);
                        $(`#client-content-${clientId}`).text(response.user_name);
                        $(`#client-image-${clientId}`).attr('src', response.user_image);
                        $(`#messages-container-${clientId} .messages-wrapper`).html(response.html);
                        scrollToBottom(clientId);

                        let consUrl = "{{ asset('files') }}";

                        // Render recent non-image files
                        const fileContainer = $(`#recent-files-${clientId}`);
                        fileContainer.empty();
                        response.client_files.forEach(file => {
                            const ext = file.name.split('.').pop().toLowerCase();
                            const icon = getFileIcon(ext); // We'll define this helper below

                            fileContainer.append(`
                                <a href="${consUrl}/${file.path}" target="_blank">
                                    <div class="for-files">
                                        <img src="${icon}" class="img-fluid">
                                        <h5>${file.name}</h5>
                                    </div>
                                </a>
                            `);
                        });

                        // Render recent images
                        // console.log(response.client_images);
                        const photoContainer = $(`#uploaded-photos-${clientId}`);
                        photoContainer.empty();
                        response.client_images.forEach(image => {
                            photoContainer.append(`
                                <div class="inner-client-images">
                                    <img src="${consUrl}/${image.path}" class="img-fluid" />
                                </div>
                            `);
                        });
                    },
                    complete: function() {
                        $(`#messages-container-${clientId} .loading-spinner`).hide();
                    }
                });
            }

            function scrollToBottom(clientId) {
                const container = $(`#messages-container-${clientId} .messages-wrapper`);
                container.scrollTop(container[0].scrollHeight);
            }

            function getFileIcon(extension) {
                const map = {
                    mp3: 'images/music.png',
                    zip: 'images/project-file.png',
                    rar: 'images/project-file.png',
                    eps: 'images/loop.png',
                    pdf: 'images/pdf-icon.png',
                    doc: 'images/doc-icon.png',
                    docx: 'images/doc-icon.png',
                    xls: 'images/xls-icon.png',
                    xlsx: 'images/xls-icon.png',
                    ppt: 'images/ppt-icon.png',
                    pptx: 'images/ppt-icon.png',
                    default: 'images/file.png'
                };

                return map[extension] || map['default'];
            }

            $(document).on('click', '.send-message', function() {
                const $this = $(this);
                const clientId = $this.data('client-id');
                const messageInput = $(`#message-input-${clientId}`);
                const fileInput = document.getElementById(`file-input-${clientId}`);
                const files = fileInput.files;
                const message = messageInput.val().trim();

                // Check file count
                if (files.length > 5) {
                    toastr.error('You can only upload a maximum of 5 files.');
                    return;
                }

                // Check file size (each file < 10MB)
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > 10 * 1024 * 1024) {
                        toastr.error(`File "${files[i].name}" exceeds 10MB limit.`);
                        return;
                    }
                }

                if (!message && files.length === 0) return;

                const formData = new FormData();
                formData.append('message', message);
                formData.append('client_id', clientId);
                for (let i = 0; i < files.length; i++) {
                    formData.append('images[]', files[i]);
                }

                // UI Feedback - Disable input + show spinner
                messageInput.prop('disabled', true);
                $this.prop('disabled', true);
                $(`#emoji-button-${clientId}`).addClass('disabled-click');
                $(`.file-upload[data-client-id="${clientId}"]`).addClass('disabled-click');

                const $spinner = $(`
                    <div class="sending-spinner" id="sending-spinner-${clientId}" style="text-align: center; margin: 5px 0;">
                        <small>Sending...</small>
                    </div>
                `);
                $(`#messages-container-${clientId} .messages-wrapper`).append($spinner);
                var container = $(`#messages-container-${clientId} .messages-wrapper`);
                container.animate({
                    scrollTop: container[0].scrollHeight
                }, 300);

                // Clear inputs before sending
                messageInput.val('');
                $(`#file-preview-${clientId}`).empty();
                fileInput.value = '';

                // AJAX
                $.ajax({
                    url: "{{ route('v2.messages.send') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        loadClientMessages(clientId); // Reload messages

                        // Re-initialize emoji pickers in case DOM is refreshed
                        initializeEmojiPickers();

                        $(`#sending-spinner-${clientId}`).remove();
                        messageInput.prop('disabled', false);
                        $this.prop('disabled', false);
                        $(`#emoji-button-${clientId}`).removeClass('disabled-click');
                        $(`.file-upload[data-client-id="${clientId}"]`).removeClass(
                            'disabled-click');
                    },
                    error: function(xhr) {
                        $(`#sending-spinner-${clientId}`).remove();
                        messageInput.prop('disabled', false);
                        $this.prop('disabled', false);
                        $(`#emoji-button-${clientId}`).removeClass('disabled-click');
                        $(`.file-upload[data-client-id="${clientId}"]`).removeClass(
                            'disabled-click');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    toastr.error(errors[field][0]);
                                }
                            }
                        } else {
                            toastr.error('An error occurred while sending the message.');
                        }

                        console.error('Message send failed:', xhr);
                    }
                });
            });

            $(document).on('keydown', '.message-input', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const clientId = $(this).attr('id').replace('message-input-', '');
                    $(`.send-message[data-client-id="${clientId}"]`).click();
                }
            });

            $(document).on('click', '.file-upload', function() {
                const clientId = $(this).data('client-id');
                console.log(`Opening file input for client ID: ${clientId}`);
                $(`#file-input-${clientId}`).click();
            });

            $(document).on('change', 'input[type="file"]', function() {
                const clientId = $(this).attr('id').replace('file-input-', '');
                const previewContainer = $(`#file-preview-${clientId}`);
                previewContainer.empty(); // clear previous previews

                const files = this.files;
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const isImage = file.type.startsWith('image');
                    const fileUrl = URL.createObjectURL(file);

                    let fileElement;

                    if (isImage) {
                        // Preview image thumbnail
                        fileElement = $(`
                            <div class="file-preview" style="position: relative;">
                                <img src="${fileUrl}" style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px;" />
                                <span class="remove-file" data-index="${i}" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border-radius: 50%; cursor: pointer; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;">&times;</span>
                            </div>
                        `);
                    } else {
                        // Preview filename for non-image
                        fileElement = $(`
                            <div class="file-preview" style="position: relative; border: 1px solid #ccc; padding: 5px 10px; border-radius: 4px; background: #f7f7f7; display: flex; align-items: center; gap: 8px;">
                                <img src="{{ asset('images/file-icon.png') }}" style="width: 24px; height: 24px;" />
                                <span style="max-width: 150px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">${file.name}</span>
                                <span class="remove-file" data-index="${i}" style="margin-left: auto; background: red; color: white; border-radius: 50%; cursor: pointer; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;">&times;</span>
                            </div>
                        `);
                    }

                    previewContainer.append(fileElement);
                }

                previewContainer.data('files', files); // store for later use
            });


            // Remove previewed file
            $(document).on('click', '.remove-file', function() {
                const index = $(this).data('index');
                const previewContainer = $(this).closest('.file-preview-container');
                const clientId = previewContainer.attr('id').replace('file-preview-', '');

                const fileInput = document.getElementById(`file-input-${clientId}`);
                const fileList = Array.from(fileInput.files);
                fileList.splice(index, 1);

                // recreate FileList
                const dataTransfer = new DataTransfer();
                fileList.forEach(file => dataTransfer.items.add(file));
                fileInput.files = dataTransfer.files;

                $(fileInput).trigger('change'); // re-render preview
            });

            // Highlight dropzone when files are dragged over
            $(document).on('dragover', '.for-sending', function(e) {
                e.preventDefault();
                $(this).css('border', '2px dashed #555'); // highlight
            });

            // Remove highlight when files are dragged away
            $(document).on('dragleave', '.for-sending', function(e) {
                e.preventDefault();
                $(this).css('border', '');
            });

            // Handle files dropped into dropzone
            $(document).on('drop', '.for-sending', function(e) {
                e.preventDefault();

                // Reset highlight
                $(this).css('border', '');

                const files = e.originalEvent.dataTransfer.files;

                // Store files in preview just like we do with input
                const clientId = $(this).data('client-id');
                handleFiles(clientId, files);
            });

            function handleFiles(clientId, files) {
                const previewContainer = $(`#file-preview-${clientId}`);
                previewContainer.empty();

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const isImage = file.type.startsWith('image');
                    const fileUrl = URL.createObjectURL(file);

                    let fileElement;

                    if (isImage) {
                        // Preview image thumbnail
                        fileElement = $(`
                            <div class="file-preview" style="position: relative;margin-bottom: 10px">
                                <img src="${fileUrl}" style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px;" />
                                <span class="remove-file" data-index="${i}" style="position: absolute; top: -8px; right: -8px; background: red; color: #fff; border-radius: 50%; cursor: pointer; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;">&times;</span>
                            </div>
                        `);
                    } else {
                        // File preview
                        fileElement = $(`
                            <div class="file-preview" style="position: relative; border: 1px solid #ccc; padding: 5px 10px; border-radius: 4px; background: #f7f7f7; display: flex; align-items: center; gap: 8px;margin-bottom: 10px">
                                <img src="{{ asset('images/file-icon.png') }}" style="width: 24px; height: 24px;" />
                                <span style="max-width: 150px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">${file.name}</span>
                                <span class="remove-file" data-index="${i}" style="margin-left: auto; background: red; color: #fff; border-radius: 50%; cursor: pointer; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;">&times;</span>
                            </div>
                        `);
                    }

                    previewContainer.append(fileElement);
                }

                previewContainer.data('files', files);
            }

            $(document).on('click', '.edit-message', function(e) {
                e.preventDefault();

                var messageId = $(this).data('message-id');
                var messageP = $('#msg-text-' + messageId);
                var currentText = messageP.text().trim();

                // Hide the edit button while we edit
                $(this).hide();

                // Transform into editable field
                messageP.html(`
                    <input type='text' id='edit-input-${messageId}'
                        value='${currentText}'
                        class='form-control' style='width: 100%' /><br>
                    <button data-message-id="${messageId}" class='save-message btn btn-primary mr-1'>Save</button>
                    <button data-message-id="${messageId}" class='cancel-edit btn btn-secondary ml-1'>Cancel</button>
                `);
            });

            // Save
            $(document).on('click', '.save-message', function(e) {
                e.preventDefault();

                var messageId = $(this).data('message-id');
                var newText = $('#edit-input-' + messageId).val();

                if (newText == '' || newText == null) return;

                $.ajax({
                    url: "{{ route('v2.messages.edit') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        message_id: messageId,
                        editmessage: newText
                    },
                    success: function(response) {
                        // Update the message text in the UI
                        $('#msg-text-' + messageId).text(response.text);
                        toastr.success("Message updated successfully.");
                        $('.edit-message[data-message-id="' + messageId + '"]').show();
                    },
                    error: function() {
                        toastr.error("Failed to update message.");
                        $('.edit-message[data-message-id="' + messageId + '"]').show();
                    }
                });
            });

            // Cancel
            $(document).on('click', '.cancel-edit', function(e) {
                e.preventDefault();

                var messageId = $(this).data('message-id');
                var messagePC = $('#edit-input-' + messageId);
                var messageP = $('#msg-text-' + messageId);
                var currentText = messagePC.val();

                // Reload or revert
                messageP.text(currentText);

                $('.edit-message[data-message-id="' + messageId + '"]').show();
            });


            Pusher.logToConsole = true;

            var pusher = new Pusher("0745d3887ed31c97a4b5", {
                cluster: "mt1"
            });

            const activeTabId = $('#active-id').val();

            var channel = pusher.subscribe('messages-' + activeTabId);

            channel.bind('new-message', function(data) {

                let filePreviewHTML = '';
                if (data.attachments && data.attachments.length > 0) {
                    filePreviewHTML += `<div class="msg-img">`;

                    data.attachments.forEach(file => {
                        const extension = file.path.split('.').pop().toLowerCase();
                        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
                        const fileUrl = `${file.path}`;

                        if (isImage) {
                            filePreviewHTML += `
                                <a href="${fileUrl}" target="_blank" title="${file.name}">
                                    <img src="${fileUrl}" alt="Image"
                                        style="max-width: 150px; max-height: 150px; border-radius: 5px;">
                                </a>`;
                        } else {
                            filePreviewHTML += `
                                <a href="${fileUrl}" download title="${file.name}">
                                    ${file.name.length > 21 ? file.name.substring(0, 21) + '...' : file.name}
                                </a>`;
                        }
                    });

                    filePreviewHTML += `</div>`;
                }


                $('#messages-container-' + data.client_id + ' .messages-wrapper').append(`
                    <div class="main-chat-message">
                        <div class="message-content">
                            <div class="message-line">
                                <p>${data.message}</p>
                                ${filePreviewHTML}
                            </div>
                            <div class="message-time">
                                <span>${data.created_at}</span>
                            </div>
                        </div>
                        <div class="message-img">
                            <img src="${data.image}" class="img-fluid">
                        </div>
                    </div>
                `);
                scrollToBottom(data.client_id);
            });

            function initializeEmojiPickers() {
                // Load emojis from JSON file
                $.getJSON("{{ url('assets/emoji.json') }}", function(emojis) {
                    $(".emoji-picker").each(function() {
                        var smileIcon = $(this);
                        var clientId = smileIcon.data("target").replace("message-input-", "");

                        // Check if emoji container already exists
                        if ($('#emoji-container-' + clientId).length === 0) {
                            var emojiContainer = $('<div>', {
                                class: 'emoji-container d-none',
                                id: 'emoji-container-' + clientId
                            });

                            $.each(emojis, function(index, emoji) {
                                var button = $('<button>', {
                                    type: 'button',
                                    class: 'emoji',
                                    text: emoji
                                });
                                emojiContainer.append(button);
                            });

                            var inputField = $('#message-input-' + clientId);
                            emojiContainer.insertBefore(inputField);
                        }
                    });

                    // Bind emoji picker toggle
                    $(".emoji-picker").off('click').on('click', function(e) {
                        e.stopPropagation();
                        var smileIcon = $(this);
                        var clientId = smileIcon.data("target").replace("message-input-", "");
                        var emojiContainer = $("#emoji-container-" + clientId);
                        emojiContainer.toggleClass('d-none');
                    });

                    // Bind emoji insert
                    $(".emoji-container").off('click').on('click', '.emoji', function() {
                        var emoji = $(this).text();
                        var clientId = $(this).closest('.emoji-container').attr('id').replace(
                            'emoji-container-', '');
                        var inputField = $("#message-input-" + clientId);
                        inputField.val(inputField.val() + emoji);
                    });
                }).fail(function(jqxhr, textStatus, error) {
                    console.error('Error fetching emojis:', error);
                });

                // Close all emoji containers when clicked outside
                $(document).off('click.closeEmoji').on('click.closeEmoji', function() {
                    $('.emoji-container').addClass('d-none');
                });
            }

            initializeEmojiPickers();

        });
    </script>

@endsection
