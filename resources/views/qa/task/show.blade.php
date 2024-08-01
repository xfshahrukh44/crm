@extends('layouts.app-qa')
@section('title', $task->projects->name)
@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('global/css/fileinput.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.1.1/css/all.min.css" integrity="sha512-ioRJH7yXnyX+7fXTQEKPULWkMn3CqMcapK0NNtCN8q//sW7ZeVFcbMJ9RvX99TwDg6P8rAH2IqUSt2TLab4Xmw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .cke_top{
        display:none !important;
    }
    .assign-wrapper .select2-container .select2-selection--single {
        height: 34px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }
    .assign-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        padding-left: 15px;
    }
    .assign-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
        top: 1px;
        right: 4px;
    }
    .assign-wrapper .input-group{
        flex-wrap: nowrap;
    }
    button.btn.btn-default.btn-secondary.fileinput-remove.fileinput-remove-button {
        background-color: #f44336 !important;
        color: white !important;
        border-right: 2px solid white;
    }

    a.btn.btn-default.btn-secondary.fileinput-upload.fileinput-upload-button {
        background-color: #003473 !important;
        color: white !important;
        border-right: 2px solid white;
    }
</style>
@endpush
@section('content')
<div class="breadcrumb row" style="align-items: flex-start;">
    <div class="col-md-9 pl-0">
        <h1 class="mr-2">{{$task->projects->name}} (ID: {{$task->id}})</h1>
        <ul>
            <li>Tasks</li>
            <li>Show Task</li>
        </ul>
        <br>
        <div class="member-wrapper mt-2">
            
            <!--<a href="javascript:;" class="btn btn-info btn-sm add-member" data-task="{{ $task->id }}" data-category="{{ $task->category->id }}">Add Member</a>-->
            
            <ul id="member-box">
                @foreach($task->member_list as $key => $value)
                <li>
                    <div class="member-box">
                        <a href="javascript:;" title="{{ $value->user->name }} {{ $value->user->last_name }}">
                            <i class="fa fa-times" onclick="removeMember(this, {{$value->user->id}}, {{ $task->id }})"></i>
                            @if($value->user->image != null)
                            <img src="{{ asset('global/img/user.png') }}">
                            @else
                            <span>{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $value->user->name . ' ' . $value->user->last_name))) }}</span>
                            @endif
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

    </div>
{{--    <div class="content-header-right col-md-3 col-12 pr-0">--}}
{{--        <div class="btn-group float-md-right w-100">--}}
{{--            <div class="task-page w-100">--}}
{{--                <fieldset>--}}
{{--                    <div class="input-group">--}}
{{--                        <select name="update-task-value" id="update-task-value" class="form-control w-200">--}}
{{--                            <option value="">Select task status</option>--}}
{{--                            <option value="0" {{($task->status == 0) ? 'selected' : ''}} disabled>Open</option>--}}
{{--                            <option value="1" {{($task->status == 1) ? 'selected' : ''}}>Re Open</option>--}}
{{--                            <option value="4" {{($task->status == 4) ? 'selected' : ''}}>In Progress</option>--}}
{{--                            <option value="2" {{($task->status == 2) ? 'selected' : ''}}>On Hold</option>--}}
{{--                            <option value="5" {{($task->status == 5) ? 'selected' : ''}}>Sent for Approval</option>--}}
{{--                            <option value="6" {{($task->status == 6) ? 'selected' : ''}}>Incomplete Brief</option>--}}
{{--                            <option value="3" {{($task->status == 3) ? 'selected' : ''}}>Completed</option>--}}
{{--                        </select>--}}
{{--                        <div class="input-group-append">--}}
{{--                            <button class="btn btn-primary" type="button" id="update-task">Update</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </fieldset>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
<div class="separator-breadcrumb border-top"></div>

<section id="basic-form-layouts">
    <div class="row">
        <div class="col-12">
            @if (\Session::has('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{!! \Session::get('success') !!}</li>
                </ul>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Task Details</h4>
                    <div class="separator-breadcrumb border-top mb-3"></div>
                    <ul class="task_main_list">
                        <li>
                            <p class="mb-0">{!! $task->description !!}</p>
                        </li>
                        <li>
                            <div class="task_main_list-wrapper">
                                <button class="btn btn-dark btn-sm">Due Date: {{ date('d M, y', strtotime($task->duedate)) }}</button>
                                <button class="btn btn-dark btn-sm">Created At: {{ $task->created_at->format('d M, y | h:i A') }}</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Update Files</h4>
                    <div class="separator-breadcrumb border-top mb-3"></div>
                    <input id="image-file" type="file" name="images[]" multiple data-browse-on-zone-click="true">
                </div>
            </div>
            <div class="card">
                <div class="card-body pl-0 pr-0">
                    <h4 class="card-title mb-3" style="margin: 0 1.25em;">Files</h4>
                    @if(count($task->client_files))
                        <button type="button" class="btn-primary btn-sm btn_download_all_files ml-4 mb-4">Download all files</button>
                    @endif
                    <div class="separator-breadcrumb border-top mb-3"></div>
                    <div class="">
                        <table class="display table table-striped table-bordered files" id="zero_configuration_table" style="width:100%">
                            <thead>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Uploaded</th>
                                <th>Date</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            @foreach($task->client_files as $client_files)
                                <tr class="file-tr-{{$client_files->id}}">
                                    <td>{{$client_files->id}}</td>
                                    <td>
                                        @php
                                            $temp= explode('.',$client_files->path);
                                            $extension = end($temp);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'png') || (($extension == 'jpeg')))
                                        <a href="{{asset('files/'.$client_files->path)}}" target="_blank">
                                            <img src="{{asset('files/'.$client_files->path)}}" alt="{{$client_files->name}}" width="100">
                                        </a>
                                        @else
                                        <a href="{{asset('files/'.$client_files->path)}}" target="_blank">
                                            {{$client_files->name}}.{{$extension}}
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-dark btn-sm">{{$client_files->name}}</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary btn-sm">{{$client_files->user->name}} {{$client_files->user->last_name}}</button><br>
                                        @if($client_files->production_check == 0)
                                        <button onclick="showToAgent(this, {{ $client_files->subtask_id }}, {{ $client_files->id }})" class="mt-2 btn btn-dark btn-sm">Show to Agent</button>
                                        @endif
                                    </td>
                                    <td><button class="btn btn-primary btn-sm">{{$client_files->created_at->format('d M, y') }}</button></td>
                                    <td>
                                        <div class="btn-group float-md-right ml-1">
                                            <button class="btn btn-info dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="i-Edit"></i></button>
                                            <div class="dropdown-menu arrow">
                                                <a class="dropdown-item" href="{{asset('files/'.$client_files->path)}}" target="_blank"> View</a>
                                                <a class="dropdown-item anchor_test" href="{{asset('files/'.$client_files->path)}}" download> Download</a>
                                                <a class="dropdown-item" href="javascript:;" onclick="deleteFile({{$client_files->id}})"> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body card_body_qa_feedback">
                    <h4 class="card-title mb-3">QA Feedback</h4>

                    @if(count($task->qa_feedbacks))
                        @foreach($task->qa_feedbacks as $qa_feedback)
                            <div class="row" id="feedback_wrapper_row">
                                <div class="col-md-4 p-0">
                                    <button type="submit" name="status" value="0" class="btn btn-{!! $qa_feedback->status == '3' ? 'success' : 'danger' !!} mx-3">{{get_task_status_text($qa_feedback->status)}}</button>
                                </div>
                                <div class="col-md-4">

                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="row">
                                        <div class="col-12">
                                                <span>
                                                    <b class="text-info">{{$qa_feedback->user->name . ' ' . $qa_feedback->user->last_name}}</b>
                                                </span>
                                        </div>
                                        <div class="col-12">
                                                <small class="text-info">
                                                    {{\Carbon\Carbon::parse($qa_feedback->created_at)->format('d M Y, h:i A')}}
{{--                                                    12 Dec 2024, 12:12 PM--}}
                                                </small>
                                        </div>
                                    </div>
                                </div>

                                @if($qa_feedback->message)
                                    <div class="col-12 mt-3">
                                        <label for="">
                                            <b class="text-15">Message</b>
                                        </label>
                                    </div>
                                    <div class="col-md-12">
                                        <p>
                                            {{$qa_feedback->message}}
                                        </p>
                                    </div>
                                @endif

                                @if(count($qa_feedback->qa_files))
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <label for="">
                                                    <b class="text-15">
                                                        Attached files
                                                        @if(count($qa_feedback->qa_files) > 1)
                                                            <a href="#" class="anchor_download__all_qa_files">
                                                                <small>(Download all)</small>
                                                            </a>
                                                        @endif
                                                    </b>
                                                </label>
                                            </div>
                                            <ul>
                                                @foreach($qa_feedback->qa_files as $qa_file)
                                                    <li>
                                                        <a class="anchor_download_qa_file" download href="{{asset($qa_file->path)}}">{{$qa_file->name}}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <div class="separator-breadcrumb border-top mb-3"></div>
                    <form action="{{route('qa.update.task', $task->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{auth()->id()}}">
                        <div id="feedback_form_row" class="row">
                            <div class="col-md-12 form-group">
                                <label for="message">
                                    <b>Message</b>
                                </label>
                                <textarea name="message" id="message" cols="30" rows="5" class="form-control" required></textarea>
                            </div>
    {{--                        <div class="col-md-12 form-group">--}}
    {{--                            <label for="message">--}}
    {{--                                <b>Rating</b>--}}
    {{--                            </label>--}}
    {{--                            <div class="row rating_row mx-2">--}}
    {{--                                <div class="col-md-4 text-center rating_column">--}}
    {{--                                    <i class="far fa-frown text-danger"></i>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-md-4 text-center rating_column">--}}
    {{--                                    <i class="far fa-meh text-info"></i>--}}
    {{--                                </div>--}}
    {{--                                <div class="col-md-4 text-center rating_column">--}}
    {{--                                    <i class="far fa-smile text-success"></i>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
                            <div class="col-md-12 form-group">
                                <label for="">
                                    <b>Attach files</b>
                                </label>
                                <input id="image-file" type="file" name="files[]" multiple data-browse-on-zone-click="true">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="">
                                    <b>Update status</b>
                                </label>
                                <div class="row">
                                    @if($task->qa_feedbacks->where('status', 1)->count() <= 2)
                                        <button type="submit" name="status" value="1" class="btn-sm btn-danger mx-3">Re Open</button>
                                    @endif
                                    <button type="submit" name="status" value="3" class="btn-sm btn-success mx-3">Complete</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
<script>
    function deleteFile(id){
        $.ajax({
            type: "POST",
            url: "{{ route('delete.files') }}",
            data: {id:id},
            success: function(response) {
                if(response.success == true){
                    $('.files').DataTable().row('.file-tr-'+id).remove().draw(false);
                    toastr.success(response.data, 'Success', {timeOut: 5000})   
                }else{
                    toastr.error('Please Contact your Administrator', 'Error Occured', {timeOut: 5000})
                }
            }
        });
    }
    $(document).ready(function(){
        $('.btn_download_all_files').on('click', function () {
            $('.anchor_test').each((i, item) => {
                item.click();
            });
        });

        $('.anchor_download__all_qa_files').on('click', function () {
            $('.anchor_download_qa_file').each((i, item) => {
                item.click();
            });
        });

        $('.btn_edit_subtask').on('click', function () {
            console.log($(this).parent().parent().find('.p_comment_editable'));
            let comment_para = $(this).parent().parent().find('.p_comment_editable');
            // comment_para.prop('contenteditable', !(comment_para.prop('contenteditable') == 'true'));
            comment_para.prop('contenteditable', true);
            $(this).hide();
            comment_para.focus();
        });

        $('.p_comment_editable').on('keyup', function (e) {
            $(this).parent().find('.hidden_input_comments').val($(this).text());

            if(e.which == 13 && $(this).text() != '') {
                $(this).text($(this).text().replaceAll('\n', ''));
                $(this).parent().parent().find('form').submit();
            }
        });

        if($('#zero_configuration_table').length != 0){
            $('#zero_configuration_table').DataTable({
                order: [[0, "desc"]],
                responsive: true,
            });
        }
        CKEDITOR.replace('description');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#assign-sub-task-form').on('submit', function(e){
            e.preventDefault();
            var action = $(this).attr('action');
            $.ajax({
                type: "POST",
                url: action,
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success == true){
                        toastr.success(response.data, 'Success', {timeOut: 5000})   
                    }else{
                        toastr.error('Please Contact your Administrator', 'Error Occured', {timeOut: 5000})
                    }
                }
            });
        })

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
                        $('#subtask_show').prepend('<div class="card mb-3">\
                                <div class="card-body">\
                                    <div class="card-content collapse show">\
                                        <div class="ul-widget__body">\
                                            <div class="ul-widget3">\
                                                <div class="ul-widget3-item">\
                                                    <div class="ul-widget3-header">\
                                                        <div class="ul-widget3-img">\
                                                            <img id="userDropdown" src="http://127.0.0.1:8000/global/img/user.png" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
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
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
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
            uploadUrl: "{{ route('insert.files', $task->id) }}",
            overwriteInitial: false,
            maxFileSize:20000000,
            maxFilesNum: 20,
            uploadExtraData: function() {
                return {
                    created_at: $('.created_at').val()
                };
            }
        });
        $("#image-file").on('fileuploaded', function(event, data, previewId, index, fileId) {
            var month_names_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var response = data.response;
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
                var newDateTime = d + " " + m + ", " + y;
                $('#zero_configuration_table tbody').prepend('<tr>\
                                        <td>'+response.files[i].id+'</td>\
                                        <td>\
                                            <a href="/files/'+response.files[i].path+'" target="_blank">\
                                                <img src="/files/'+response.files[i].path+'" alt="'+response.files[i].name+'" width="100">\
                                            </a>\
                                        </td>\
                                        <td><button class="btn btn-dark btn-sm">'+response.files[i].name+'</button></td>\
                                        <td><button class="btn btn-secondary btn-sm">'+response.uploaded_by+'</button></td>\
                                        <td><button class="btn btn-primary btn-sm">'+newDateTime+'</button></td>\
                                        <td>\
                                            <div class="btn-group float-md-right ml-1">\
                                                <button class="btn btn-info dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="i-Edit"></i></button>\
                                                <div class="dropdown-menu arrow">\
                                                    <a class="dropdown-item" href="/files/'+response.files[i].path+'" target="_blank"> View</a>\
                                                    <a class="dropdown-item anchor_test" href="/files/'+response.files[i].path+'" download> Download</a>\
                                                    <a class="dropdown-item" href="#" onclick="deleteFile('+response.files[i].id+')"> Delete</a>\
                                                </div>\
                                            </div>\
                                        </td>\
                                    </tr>');
            }
            $("#image-file").fileinput('refresh');
            $("#image-file").fileinput('reset');
            toastr.success('Image Updated Successfully', '', {timeOut: 5000})
            $('.files').DataTable({
                order:[[0,"desc"]],
                responsive: true,
            });
        });
    });
    var start = new Date;
    setInterval(function() {
        var d = new Date();
        var date = ( '0' + (d.getDate()) ).slice( -2 );
        var month = ( '0' + (d.getMonth()+1) ).slice( -2 );
        var year = d.getFullYear();
        var seconds = ( '0' + (d.getSeconds()) ).slice( -2 );
        var minutes = ( '0' + (d.getMinutes()) ).slice( -2 );
        var hour = ( '0' + (d.getHours()) ).slice( -2 );
        var dateStr = year + "-" + month + "-" + date + ' ' + hour + ':' + minutes + ':' + seconds;
        $('.created_at').val(dateStr);
    }, 1000);
</script>
<script>
    function showToAgent(a, subtask_id, file_id){
        var button_obj = $(a);
        swal({
            title: 'Are you sure?',
            text: "You want to show this file to Agent!",
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
                url: "{{ route('production.agent.file.show') }}",
                data: {id: file_id, subtask_id:subtask_id},
                success:function(data) {
                    if(data.success == true){
                        $(button_obj).removeClass('show-to-client btn-dark');
                        $(button_obj).addClass('btn-info');
                        $(button_obj).text('Shown');
                        swal('Success!', 'File has Shown to Agent', 'success');
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
    }
    $('.show-to-agent').click(function(){
        var button_obj = $(this);
        var file_id = $(this).data('id');
        var subtask_id = $(this).data('subtask');
        swal({
            title: 'Are you sure?',
            text: "You want to show this file to Agent!",
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
                url: "{{ route('production.agent.file.show') }}",
                data: {id: file_id, subtask_id:subtask_id},
                success:function(data) {
                    if(data.success == true){
                        $(button_obj).removeClass('show-to-client btn-dark');
                        $(button_obj).addClass('btn-info');
                        $(button_obj).text('Shown');
                        swal('Success!', 'File has Shown to Agent', 'success');
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
    });
    $('#update-task').click(function(){
        var value = $('#update-task-value').val();
        $.ajax({
            type: "POST",
            url: "{{ route('qa.update.task', $task->id) }}",
            data: { value:value },
            success: function(response) {
                if(response.status){
                    toastr.success(response.message, '', {timeOut: 5000})
                }
            }
        });
    });

    $('.change_duadate').on('submit', function(e) {
        e.preventDefault();
        var action = $(this).attr('action');
        // var duedate = $(this).find('[name=duedate]').val();
        // var subtask_id = $(this).find('[name=subtask_id]').val();
        $.ajax({
            type: "POST",
            url: action,
            data: $(this).serialize(),
            success: function(response) {
                if(response.success == true){
                    toastr.success(response.data, 'Success', {timeOut: 5000});
                    $('#'+response.id).find('.subtask_duedate').text(response.duedate);
                }else{
                    toastr.error('Please Contact your Administrator', 'Error Occured', {timeOut: 5000})
                }
            }
        });
    })

    $('.add-member').click(function(){
        var task_id = $(this).data('task');
        var category_id = $(this).data('category');
        var myArrayOfThings = [];
        $.ajax({
            type: "POST",
            url: "{{ route('category.member.list') }}",
            data: {
                category_id: category_id
            },
            success: function(response) {
                if(response.success == true){
                    for(i = 0; i < response.data.length; i++){
                        myArrayOfThings.push(response.data[i]);
                    }
                    var options = {};
                    $.map(myArrayOfThings,
                        function(o) {
                            options[o.id] = o.name + ' ' + o.last_name;
                        });

                    swal({
                        type: 'info',
                        title: 'Select Member',
                        confirmButtonColor: '#0CC27E',
                        cancelButtonColor: '#FF586B',
                        input: 'select',
                        inputOptions: options,
                        showCancelButton: true,
                        animation: 'slide-from-top',
                        inputPlaceholder: 'Please select',
                        confirmButtonClass: 'btn btn-lg btn-success mr-5',
                        cancelButtonClass: 'btn btn-lg btn-danger',
                        confirmButtonText: 'Add Member',
                        cancelButtonText: 'Cancel',
                        buttonsStyling: false
                    }).then(function (inputValue) {
                        if (inputValue) {
                            $.ajax({
                                type:'POST',
                                url: "{{ route('category.member.list.add') }}",
                                data: {user_id: inputValue, task_id:task_id},
                                success:function(data) {
                                    if(data.success == true){
                                        var image = "{{ asset('global/img/user.png') }}";
                                        if(data.data.image != null){
                                            image = "{{ asset('"+ data.data.image +"') }}"
                                        }
                                        $('#member-box').append('<li><div class="member-box"><a href="#" title="'+data.data.name+' '+ data.data.last_name +'"><img src="'+image+'"></a></div></li>');
                                        swal('Success!', 'Member Added to this Task', 'success');
                                    }else{
                                        return swal({
                                            title:"Information",
                                            text: data.data,
                                            type:"warning",
                                            showCancelButton: false,
                                        })
                                    }
                                }
                            });

                        }
                    });
                }
            }
        });
    })

    function removeMember(a, user_id, task_id){
        var remove_obj = $(a);
        swal({
            title: 'Are you sure?',
            text: "You want to Remove this Member?",
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
                url: "{{ route('category.member.list.remove') }}",
                data: {user_id: user_id, task_id:task_id},
                success:function(data) {
                    if(data.success == true){
                        $(remove_obj).parent().parent().parent().remove();
                        swal('Success!', 'Member Removed', 'success');
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
    }
</script>
@endpush