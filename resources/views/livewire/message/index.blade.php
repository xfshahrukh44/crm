<div>
    <link rel="stylesheet" type="text/css" href="{{ asset('newglobal/css/image-uploader.min.css') }}">
    <style>
        .ul-widget2__username {
            font-size: 0.8rem;
        }
        button#write-message {
            margin-bottom: 30px;
        }
        .ul-widget3-body p {margin-bottom: 4px;}

        .uploaded-file img {
            max-width: 200px;
        }

        button.delete-image {
            margin-top: 10px;
        }

        .image-uploader {
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 10px; /* Space between uploaded items and the upload area */
            border: 2px dashed #ddd;
            padding: 20px;
            border-radius: 5px;
        }

        .image-uploader .uploaded {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Space between uploaded images */
        }

        .image-uploader .uploaded-file {
            position: relative;
            width: 200px; /* Increased width for better image and text fit */
            height: 200px; /* Increased height for better image and text fit */
            overflow: hidden;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f7f7f7;
            display: flex;
            flex-direction: column; /* Allows stacking of image and text */
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 5px; /* Padding inside the tile for spacing */
            box-sizing: border-box; /* Ensures padding doesn't affect width */
            line-height: 1 !important;
        }

        .image-uploader .uploaded-file img {
            max-width: 200px;
            max-height: 100px; /* Limit image height to prevent crumpling */
            object-fit: contain; /* Ensure the image fits within the container without being distorted */
            margin-bottom: 5px; /* Space between image and text */
        }

        .image-uploader .uploaded-file span {
            font-size: 12px; /* Adjust text size to fit within the tile */
            word-wrap: break-word; /* Ensure long text wraps inside the container */
        }

        .image-uploader .upload-text {
            flex: 1; /* Takes up remaining space */
            text-align: center;
            text-overflow: clip;
            cursor: pointer;
            min-width: 200px; /* Minimum width for the upload area */
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-sizing: border-box;
        }
    </style>



    <div class="breadcrumb row">
        <div class="col-md-6">
            <h1>Messages</h1>
            <ul>
                <li><a href="#">{{ $user->name }} {{ $user->last_name }} | {{ $user->email }}</a></li>
            </ul>
        </div>
        <div class="col-md-6 text-right">
        </div>
    </div>

    <div class="separator-breadcrumb border-top"></div>
    <section class="widgets-content">
        <!-- begin::users-->
        <div class="row">
            {{--    <div class="col-md-12">--}}
            {{--        <button class="btn btn-primary ml-auto" id="write-message">Write A Message</button>--}}
            {{--    </div>--}}
        </div>
    </section>
    <section id="basic-form-layouts">
        <div class="row">
            <div class="col-md-12 message-box-wrapper">
                @foreach($messages as $message)
                    <div class="card mb-3 {{ $message->role_id == Auth()->user()->is_employee ? 'left-card' : 'right-card' }}">
                        <div class="card-body">
                            <div class="card-content collapse show">
                                <div class="ul-widget__body mt-0">
                                    <div class="ul-widget3 message_show">
                                        <div class="ul-widget3-item mt-0 mb-0">
                                            <div class="ul-widget3-header">
                                                <div class="ul-widget3-info">
                                                    <a class="__g-widget-username" href="#">
                                                        <span class="t-font-bolder">{{ $message->user->name }} {{ $message->user->last_name }}</span>
                                                    </a>
                                                </div>
                                                @if($message->user_id == Auth()->user()->id && (\Carbon\Carbon::now() <= \Carbon\Carbon::parse($message->created_at)->addMinutes(10)))
                                                    <button class="btn-sm btn btn-primary" onclick="editMessage({{$message->id}})">Edit Message</button>
                                                @endif
                                            </div>
                                            <div class="ul-widget3-body">
                                                {!! nl2br($message->message) !!}
                                                <span class="ul-widget3-status text-success t-font-bolder">
                                                {{ \Carbon\Carbon::parse($message->created_at)->format('d M Y h:i A') }}
                                            </span>
                                            </div>
                                            <div class="file-wrapper">
                                                @if(count($message->sended_client_files) != 0)
                                                    @foreach($message->sended_client_files as $key => $client_file)
                                                        <ul>
                                                            <li>
                                                                <button class="btn btn-dark btn-sm">{{++$key}}</button>
                                                            </li>
                                                            <li>
                                                                @if(($client_file->get_extension() == 'jpg') || ($client_file->get_extension() == 'png') || (($client_file->get_extension() == 'jpeg')))
                                                                    <a href="{{asset('files/'.$client_file->path)}}" target="_blank">
                                                                        <img src="{{asset('files/'.$client_file->path)}}" alt="{{$client_file->name}}" width="40">
                                                                    </a>
                                                                @else
                                                                    <a href="{{asset('files/'.$client_file->path)}}" target="_blank">
                                                                        {{$client_file->name}}.{{$client_file->get_extension()}}
                                                                    </a>
                                                                @endif
                                                            </li>
                                                            <li>
                                                                <a href="{{asset('files/'.$client_file->path)}}" target="_blank">{{$client_file->name}}</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{asset('files/'.$client_file->path)}}" download>Download</a>
                                                            </li>
                                                        </ul>
                                                    @endforeach
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

            <div class="col-md-12">
                <div class="left-message-box">
                    <form class="form" action="{{ route('support.message.send') }}" enctype="multipart/form-data" method="post" id="message-post">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $user->id }}">
                        <div class="form-body">
                            <div class="form-group mb-0">
                                <h1>Write A Message <span id="close-message-left"><i class="nav-icon i-Close-Window"></i></span></h1>
                                <textarea wire:model="message_client_message" id="message" rows="8" class="form-control border-primary" name="message" placeholder="Write a Message">{{old('message')}}</textarea>
                                <div class="input-field">
                                    <div class="input-images" style="padding-top: .5rem;"></div>
                                </div>
                                <!-- <table>
                                    <tr>
                                        <td colspan="3" style="vertical-align:middle; text-align:left;">
                                            <div id="h_ItemAttachments"></div>
                                            <input type="button" id="h_btnAddFileUploadControl" value="Add Attachment" onclick="Clicked_h_btnAddFileUploadControl()" class="btn btn-info btn_Standard" />
                                            <div id="h_ItemAttachmentControls"></div>
                                        </td>
                                    </tr>
                                </table> -->
                                <div class="form-actions pb-0">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="la la-check-square-o"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!--  Modal -->
    <div class="modal fade" id="exampleModalMessageEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Edit Message</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('support.message.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="message_id" id="message_id">
                    <div class="modal-body">
                        <textarea wire:model="message_client_edit_message" name="editmessage" id="editmessage" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary ml-2" type="submit">Update changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>