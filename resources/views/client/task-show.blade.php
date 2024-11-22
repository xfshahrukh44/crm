@extends('layouts.app-client')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css" integrity="sha512-6qkvBbDyl5TDJtNJiC8foyEVuB6gxMBkrKy67XpqnIDxyvLLPJzmTjAj1dRJfNdmXWqD10VbJoeN4pOQqDwvRA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.4.2/css/all.min.css" integrity="sha512-NicFTMUg/LwBeG8C7VG+gC4YiiRtQACl98QdkmfsLy37RzXdkaUAuPyVMND0olPP4Jn8M/ctesGSB2pgUBDRIw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="{{ asset('global/css/fileinput.css') }}">
<style>
    div#mCSB_1_container {
        padding: 15px 15px;
        margin-right: 15px;
    }
    .card.mb-3.left-card span.ul-widget3-status.text-success.t-font-bolder {
        text-align: right;
        color: black !important;
    }
    .cke_top {
        display: none !important;
    }
    ul.task_main_list {
        padding: 0px;
        margin-bottom: 0px;
        list-style: none;
    }
    .task_main_list-wrapper p {
        margin-bottom: 0px;
    }
    input#h_btnAddFileUploadControl {
        margin-top: 0px;
        margin-bottom: 15px;
    }
    div#h_ItemAttachments {
        margin-bottom: 0px;
    }
    span.ul-widget3-status{
        display: block;
    }
    .ul-widget3-body p {
        font-size: 15px;
        line-height: inherit;
        margin-bottom: 0;
    }
    .card.left-card {
        text-align: left;
        max-width: 70%;
        margin-left: auto;
        color: black;
        background-color: #dbefdc;
        border-radius: 15px;
        border-color: #cde9ce;
        border-top-right-radius: 0;
        position: relative;
        margin-bottom:30px !important;
    }
    .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {
        background-color: #0076c2 !important;
    }
    .card.left-card:before {
        content: "";
        width: 0;
        height: 0;
        position: absolute;
        right: -15px;
        border-top: 0 solid transparent;
        border-bottom: 18px solid transparent;
        border-left: 15px solid #dbefdc;
    }
    .card.mb-3.left-card .ul-widget3-header {
        flex-direction: inherit;
        display:none;
    }
    .ul-widget3-header {
        padding-bottom: 8px;
        font-size: 15px;
    }
    .card.mb-3.left-card a.__g-widget-username {
        color: black;
    }
    .bottom-chat {
        position: fixed;
        bottom: 0;
        width: calc(100% - 150px);
    }
    .message-box-wrapper .card-body {
        padding: 15px;
    }
</style>
@endpush
@section('content')

{{--<div class="breadcrumb">--}}
{{--    <h1 class="mr-2">Messages</h1>--}}
{{--    <button class="btn btn-primary ml-auto" id="write-message" data-intro='Send us a message.' data-step='3'>Write A Message</button>--}}
{{--</div>--}}
<div class="separator-breadcrumb border-top"></div>

<section id="basic-form-layouts">
    <div class="row">
        <div class="col-md-12 message-box-wrapper" >
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
                                    </div>
                                    <div class="ul-widget3-body">
                                        <p>{!! nl2br($message->message) !!}</p>
                                        <span class="ul-widget3-status text-success t-font-bolder">
                                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('America/New_York')->format('d M Y h:i A') }}
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
            <div class="left-message-box mt-0">
                <form class="form" action="{{ route('client.send.messages') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <input type="hidden" name="task_id" value="">
                    <div class="form-body">
                        <div class="form-group mb-0">
                            <h1>Write A Message <span id="close-message-left"><i class="nav-icon i-Close-Window"></i></span></h1>
                            <textarea id="message" rows="8" class="form-control border-primary" name="message" required placeholder="Write a Message">{{old('message')}}</textarea>
                            <table>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle; text-align:left;">
                                        <div id="h_ItemAttachments"></div>
                                        <input type="button" id="h_btnAddFileUploadControl" value="Add Attachment" onclick="Clicked_h_btnAddFileUploadControl()" class="btn btn-info btn_Standard" />
                                        <div id="h_ItemAttachmentControls"></div>
                                    </td>
                                </tr>
                            </table>
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
@endsection

@push('scripts')
<script src="{{ asset('global/js/fileinput.js') }}"></script>
<script src="{{ asset('global/js/fileinput-theme.js') }}"></script>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js" integrity="sha512-Yk47FuYNtuINE1w+t/KT4BQ7JaycTCcrvlSvdK/jry6Kcxqg5vN7/svVWCxZykVzzJHaxXk5T9jnFemZHSYgnw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function(){
        $('.btn_download_all_files').on('click', function () {
            $('.anchor_test').each((i, item) => {
                item.click();
            });
        });

        $('#write-message').click(function(){
            $('.left-message-box-wrapper').addClass('fixed-option');
        });
        $('#close-message-left').click(function(){
            $('.left-message-box-wrapper').removeClass('fixed-option');
        })
        CKEDITOR.replace('description');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#subtask' ).on('submit', function(e) {
            e.preventDefault();
            var description = CKEDITOR.instances.description.getData();
            var duedate = $(this).find('[name=duedate]').val();
            var action = $(this).attr('action');
            var task_id = $(this).find('[name=task_id]').val();
            if(description != ''){
                $.ajax({
                    type: "POST",
                    url: action,
                    data: { description:description, task_id:task_id, duedate:duedate},
                    success: function(response) {
                        var duedate = '';
                        if(response.duedate != null){
                            duedate = 'Due Date <br>' +  response.duedate
                        }
                        $('#subtask_show').prepend('<div class="ul-widget3-item">\
                                    <div class="ul-widget3-header">\
                                        <div class="ul-widget3-img">\
                                            <img id="userDropdown" src="{{ asset('global/img/user.png') }}" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                                        </div>\
                                        <div class="ul-widget3-info">\
                                            <a class="__g-widget-username" href="#">\
                                                <span class="t-font-bolder">'+response.user_name+' </span>\
                                            </a>\
                                            <br>\
                                            <span class="ul-widget-notification-item-time">'+response.created_at+'</span>\
                                        </div>\
                                        <span class="ul-widget3-status text-success t-font-bolder">\
                                        '+duedate+'\
                                        </span>\
                                    </div>\
                                    <div class="ul-widget3-body">\
                                        <p>'+response.data.description+'</p>\
                                    </div>\
                                </div>');
                        CKEDITOR.instances.description.setData('');
                        $('#duedate').val('');
                        toastr.success(response.success, '', {timeOut: 5000})
                    }
                });
            }else{
                toastr.error("Please Fill the form", '', {timeOut: 5000})
            }
        });
        $("#image-file").fileinput({
            showUpload: true,
            theme: 'fa',
            dropZoneEnabled : true,
            uploadUrl: "{{ url('support/files') }}/1",
            overwriteInitial: false,
            maxFileSize:20000000,
            maxFilesNum: 20,
        });
        $("#image-file").on('fileuploaded', function(event, data, previewId, index, fileId) {
            var month_names_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var response = data.response;
            console.log(response);
            $('.files').DataTable().destroy();
            for(var i = 0; i < response.files.length; i++){
                var formattedDate = new Date(response.files[i].created_at);
                var d = formattedDate.getDate();
                var m =  month_names_short[formattedDate.getMonth()];
                var y = formattedDate.getFullYear().toString().substr(-2);
                var hours = formattedDate.getHours();
                var minutes = formattedDate.getMinutes();
                var ampm = hours >= 12 ? 'pm' : 'am';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                minutes = minutes < 10 ? '0'+minutes : minutes;
                var strTime = hours + ':' + minutes + ' ' + ampm;
                var newDateTime = d + " " + m + ", " + y + ' ' + strTime;
                $('#files tbody').prepend('<tr>\
                                        <td>'+response.files[i].id+'</td>\
                                        <td>\
                                            <a href="/files/'+response.files[i].path+'" target="_blank">\
                                                <img src="/files/'+response.files[i].path+'" alt="'+response.files[i].name+'" width="100">\
                                            </a>\
                                        </td>\
                                        <td>'+response.files[i].name+'</td>\
                                        <td><button class="btn btn-primary btn-sm">'+newDateTime+'</button></td>\
                                        <td><a class="btn btn-dark btn-sm" href="/files/'+response.files[i].path+'" download> Download</a></td>\
                                    </tr>');
            }
            toastr.success('Image Updated Successfully', '', {timeOut: 5000})
            $('.files').DataTable({
                order:[[0,"desc"]],
                responsive: true,
            })
        });
    });

    $('#sendmessage').submit(function(e){
        e.preventDefault();
        var form_obj = $(this);
        swal({
            title: 'Are you sure?',
            text: "You want to Send this Message to Agent!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0CC27E',
            cancelButtonColor: '#FF586B',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            confirmButtonClass: 'btn btn-lg btn-success mr-5',
            cancelButtonClass: 'btn btn-lg btn-danger',
            buttonsStyling: false
        }).then(function () {
            $.ajax({
                type:'POST',
                url: "{{ route('client.message.send') }}",
                data: $(form_obj).serialize(),
                success:function(data) {
                    console.log(data);
                    if(data.success == true){
                        $('#message_show').prepend('<div class="ul-widget3-item">\
                                    <div class="ul-widget3-header">\
                                        <div class="ul-widget3-info">\
                                            <a class="__g-widget-username" href="#">\
                                                <span class="t-font-bolder">'+data.name+' </span>\
                                            </a>\
                                            <br>\
                                            <span class="ul-widget-notification-item-time"></span>\
                                        </div>\
                                        <span class="ul-widget3-status text-success t-font-bolder">\
                                        '+data.created_at+'\
                                        </span>\
                                    </div>\
                                    <div class="ul-widget3-body">\
                                        <p>'+data.data+'</p>\
                                    </div>\
                                </div>');
                        swal('Success!', 'Message Sent to Agent', 'success');
                        $('#message').val('');
                    }else{
                        return swal({
                            title:"Error",
                            text: "There is an Error, Plase Contact Administrator",
                            type:"danger"
                        })
                    }
                }
            });

        }, function (dismiss) {
            if (dismiss === 'cancel') {

            }
        });
    })
</script>
<!-- Image Upload -->
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
    $(".message-box-wrapper").mCustomScrollbar({
        setHeight:800,
    });
    $(".message-box-wrapper").mCustomScrollbar("scrollTo", "bottom");
</script>

<script>
    $('form').on('submit', function () {
        let button = $(this).find('button[type="submit"]');
        button.prop('disabled', true);
        button.text('Please wait');
    });
</script>
@endpush
