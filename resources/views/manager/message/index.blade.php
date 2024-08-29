@extends('layouts.app-manager')
@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('newglobal/css/image-uploader.min.css') }}">
<style>
    .ul-widget2__username {
       font-size: 0.8rem;
    }
    button#write-message {
        margin-bottom: 30px;
    }
    .ul-widget3-body p {margin-bottom: 4px;}
</style>
@endpush
@section('content')
<div class="breadcrumb row">
    <div class="col-md-6">
        <h1>Messages</h1>
        <ul>
            <li><a href="#">{{ $user->name }} {{ $user->last_name }} | {{ $user->email }}</a></li>
        </ul>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('manager.message') }}" class="btn btn-primary">Back</a>
    </div>
</div>

<div class="separator-breadcrumb border-top"></div>
<section id="basic-form-layouts">
    <div class="row">
        <div class="col-md-12 message-box-wrapper">
        @foreach($messages as $message)
            <div class="card mb-3 {{ ($message->role_id == Auth()->user()->is_employee) || ($message->role_id == 4) ? 'left-card' : 'right-card' }}">
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
                                        @if($message->role_id != 3 && (\Carbon\Carbon::now() <= \Carbon\Carbon::parse($message->created_at)->addMinutes(10)))
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
    </div>
</section>
<section class="widgets-content">
    <!-- begin::users-->
    <div class="row">
{{--        <div class="col-md-6"></div>--}}
        <div class="col-md-12">
{{--            <button class="btn btn-primary ml-auto" id="write-message">Write A Message</button>--}}
            <div class="">
                <div class="">
                    <form class="form" action="{{ route('manager.message.send') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $user->id }}">
                        <div class="form-body">
                            <div class="form-group mb-0">
{{--                                <h1>Write A Message <span id="close-message-left"><i class="nav-icon i-Close-Window"></i></span></h1>--}}
                                <textarea id="message" rows="8" class="form-control border-primary" name="message" placeholder="Write a Message">{{old('message')}}</textarea>
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
    </div>
</section>
<div class="left-message-box-wrapper">
    <div class="left-message-box">
        <form class="form" action="{{ route('manager.message.send') }}" enctype="multipart/form-data" method="post">
            @csrf
            <input type="hidden" name="client_id" value="{{ $user->id }}">
            <div class="form-body">
                <div class="form-group mb-0">
                    <h1>Write A Message <span id="close-message-left"><i class="nav-icon i-Close-Window"></i></span></h1>
                    <textarea id="message" rows="8" class="form-control border-primary" name="message" required placeholder="Write a Message">{{old('message')}}</textarea>
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
<!--  Modal -->
<div class="modal fade" id="exampleModalMessageEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle-2">Edit Message</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="{{ route('manager.message.update') }}" method="post">
                @csrf
                <input type="hidden" name="message_id" id="message_id">
                <div class="modal-body">
                    <textarea name="editmessage" id="editmessage" cols="30" rows="10" class="form-control"></textarea> 
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary ml-2" type="submit">Update changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/v342h96m9l2d2xvl69w2yxp6fwd33xvey1c4h3do99vwwpt2/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
        tinymce.init({
            selector: '#message',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
        tinymce.init({
            selector: '#editmessage',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
    </script>

{{--    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>--}}
    <script>
        $(document).ready(() => {
            setTimeout(() => {
                $('.cke_notifications_area').remove();

                setTimeout(() => {
                    $('.cke_notifications_area').remove();

                    setTimeout(() => {
                        $('.cke_notifications_area').remove();

                        setTimeout(() => {
                            $('.cke_notifications_area').remove();
                        }, 1000);
                    }, 1000);
                }, 1000);
            }, 1000);
        });
    </script>
    <script src="{{ asset('newglobal/js/image-uploader.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('.input-images').imageUploader();
        });
        // CKEDITOR.replace('editmessage');
        // CKEDITOR.replace('message');
        function editMessage(message_id){
            var url = "{{ route('manager.message.edit', ":message_id") }}";
            url = url.replace(':message_id', message_id);
            $.ajax({
                type:'GET',
                url: url,
                success:function(data) {
                    if(data.success){
                        // CKEDITOR.instances['editmessage'].setData(data.data.message);
                        tinymce.get('editmessage').setContent(data.data.message)
                        $('#exampleModalMessageEdit').find('#message_id').val(data.data.id);
                        $('#exampleModalMessageEdit').modal('toggle');
                        console.log();
                    }
                }
            });
        }
         $(document).ready(function(){
            $("html, body").animate({ scrollTop: document.body.scrollHeight }, "slow");
            $('#write-message').click(function(){
                $('.left-message-box-wrapper').addClass('fixed-option');
            });
            $('#close-message-left').click(function(){
                $('.left-message-box-wrapper').removeClass('fixed-option');
            })
        });
    </script>
    <script>
    g_FileUploadControlCounter = 0;

    function Clicked_h_btnAddFileUploadControl() {
        var v_btnFileUploadControl = document.getElementById("h_btnAddFileUploadControl");
            v_btnFileUploadControl.value = "Add Another Attachment";

        var n="h_Item_Attachments_FileInput[]";
        var z="h_Item_Attachment" + g_FileUploadControlCounter;
        var x = document.createElement("INPUT");

            x.setAttribute("type", "file");
            x.setAttribute("id", z);
            x.setAttribute("name", n);
            x.setAttribute("onchange", "UpdateAttachmentsDisplayList()");
            x.setAttribute("class", "Otr_Std_pad");
            document.getElementById("h_ItemAttachmentControls").appendChild(x);
            g_FileUploadControlCounter++;
        }

        function Clicked_h_hrefRemoveFileUploadControl(v_Item_Attachment) {

            document.getElementById(v_Item_Attachment.id).value = null;
            UpdateAttachmentsDisplayList();
        }

        function UpdateAttachmentsDisplayList() {

        var inputs = document.getElementsByTagName('input');
        var txt='';

        for(var i = 0; i < inputs.length; i++) {
            if(inputs[i].type.toLowerCase() == 'file') {
                if(inputs[i].value.length > 0)
                {
                    var x = inputs[i];
                    txt += "<div class='item-attachments-wrapper'><strong>" + inputs[i].value + "</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:Clicked_h_hrefRemoveFileUploadControl(" + x.id + ");'>Delete</a></div>";
                    document.getElementById(inputs[i].id).style.visibility = "hidden";
                    document.getElementById(inputs[i].id).style.height = "0";
                    document.getElementById(inputs[i].id).style.width = "0";
                }else{
                    document.getElementById(inputs[i].id).style.visibility = "visible";
                }
            }
            document.getElementById("h_ItemAttachments").innerHTML = txt;
            }
        }
    </script>
@endpush