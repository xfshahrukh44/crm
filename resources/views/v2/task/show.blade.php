@extends('v2.layouts.app')

@section('title', 'Client Detail')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('global/css/fileinput.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('newglobal/css/image-uploader.min.css') }}">
@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brand-detail new-task-detail">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="client-serach-pare">
                            <p>{{$task->projects->name}} (ID: {{$task->id}}) </p>
                            <div class="Search2">
                                <select name="update-task-value" id="update-task-value" class="form-control w-200" style="border-radius: 30px;">
                                    <option value="">Select task status</option>
                                    <option value="0" {{($task->status == 0) ? 'selected' : ''}}>Open</option>
                                    <option value="1" {{($task->status == 1) ? 'selected' : ''}}>Re Open</option>
                                    <option value="2" {{($task->status == 2) ? 'selected' : ''}}>On Hold</option>
                                    <option value="3" {{($task->status == 3) ? 'selected' : ''}}>Completed</option>
                                    <option value="4" {{($task->status == 4) ? 'selected' : ''}}>In Progress</option>
                                    <option value="5" {{($task->status == 5) ? 'selected' : ''}}>Sent for Approval</option>
                                    <option value="6" {{($task->status == 6) ? 'selected' : ''}}>Incomplete Brief</option>
                                    <option value="7" {{($task->status == 7) ? 'selected' : ''}}>Sent for QA</option>
                                </select>

                                {{--                                        <input class="form-control mr-sm-2" placeholder="Search">--}}
                                <button class="btn btn-outline-success my-2 my-sm-0 green-assign" id="update-task">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="list-0f new-task-detail">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-7">
                        <div class="list-0f-head">
                            <h2>Task Details</h2>
                            <p>{!! $task->description !!}</p>
                            <div class="for-date">
                                <p>DUE DATE: {{\Carbon\Carbon::parse($task->duedate)->format('d F Y, h:i A')}}</p>
                                <p>CREATED AT: {{\Carbon\Carbon::parse($task->created_at)->format('d F Y, h:i A')}}</p>
                            </div>


                        </div>
                        <div class="client-serach-pare for-drag-new row m-0 mt-4">
                            <p>Upload Files</p>
                            <div class="Search2" style="display: block !important;">
                                {{--                                        <input name="file1" type="file" class="dropify" data-height="10">--}}
                                <input id="image-file" type="file" name="images[]" multiple
                                       data-browse-on-zone-click="true">
                            </div>
                        </div>
                        @if(count($task->client_files_support))
                            <div class="list-0f-head new-task-list">
                                <div class="assign-task">
                                    <h2>Attachments & Files</h2>
                                    <div class="sub-task">
                                        <a href="javascript:;" class="password-btn blue-assign btn_download_all_files">Downlaod All Files</a>
                                    </div>
                                </div>
                                {{--                                    <div class="show-items">--}}
                                {{--                                        <div class="search-invoice">--}}
                                {{--                                            <select>--}}
                                {{--                                                <option value="">Show 10 entries</option>--}}
                                {{--                                                <option value="1">Status 1</option>--}}
                                {{--                                                <option value="2">Status 2</option>--}}
                                {{--                                                <option value="3">Status 3</option>--}}
                                {{--                                            </select>--}}
                                {{--                                        </div>--}}
                                {{--                                        <div class="serach">--}}
                                {{--                                            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">--}}
                                {{--                                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}


                                <table>
                                    <tbody>
                                    <tr>
                                        <th>ID.</th>
                                        <th>File</th>
                                        <th>Uploaded By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>

                                    @foreach($task->client_files_support as $client_file)
                                        @php
                                            $temp = explode('.',$client_file->path);
                                            $extension = end($temp);
                                            $extension_check = in_array($extension, ['jpeg', 'jpg', 'png', 'webp', 'gif', 'avif', 'bmp']);

                                            $file_src = asset('files/'.$client_file->path);
                                            $image_src = $extension_check ? $file_src : asset('images/file-transparent.png');
                                            $file_name = (limitTextAtWord($client_file->name, 20)) . '.' . $extension;
                                            $file_name = (substr($client_file->name, 0, 15)) . '....' . $extension;
                                        @endphp
                                        <tr>
                                            <td>{{$client_file->id}}</td>
                                            <td>
                                                <img src="{{$image_src}}" class="img-fluid" height="64" style="max-width: 100px; max-height: 64px; object-fit: contain;">

                                                <br>
                                                {{$file_name}}

                                            </td>
                                            <td>
                                                <div class="for-date">
                                                    <p>{{$client_file->user->name}} {{$client_file->user->last_name}}</p>
                                                </div>
                                            </td>
                                            <td>{{$client_file->created_at->format('d F Y, h:i A') }}</td>
                                            <td>
                                                <a href="{{$file_src}}" class="for-assign anchor_test" download>Download</a>
                                                {{--                                                        <a href="javascript:;" class="for-assign red-assign">Delete</a>--}}
                                            </td>
                                        </tr>
                                    @endforeach







                                    </tbody>
                                </table>

                            </div>
                        @endif
                    </div>

                    <div class="col-md-5">
                        <form action="{{route('v2.subtasks.store')}}" method="POST">
                            @csrf
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <div class="list-0f-head sub-task-message">
                                <h2>Due Date</h2>
                                <input class="form-control" type="date" name="duedate" id="duedate" value="" required="">
                                @error('duedate')
                                <label class="text-danger">{{ $message }}</label>
                                @enderror

                                <br>

                                <h2>Sub Task Message</h2>
                                <textarea rows="6" name="description" placeholder="Sub Task Message Details"></textarea>
                                @error('description')
                                <label class="text-danger">{{ $message }}</label>
                                @enderror

                                <div class="sub-task">
                                    <a href="javascript:;" class="password-btn blue-assign" onclick="this.closest('form').submit();">Save</a>
                                </div>
                            </div>
                        </form>

                        @foreach($task->sub_tasks as $sub_tasks)
                            <div class="list-0f-head msg-client">
                                <div class="assign-task">
                                    <h2>
                                        {{ $sub_tasks->user->name ?? '' }} {{ $sub_tasks->user->last_name ?? '' }}
                                        <span>{{ $sub_tasks->created_at->diffForHumans() }}</span>
                                    </h2>
                                    <span>{{ $sub_tasks->created_at->format('d F Y, h:i A') }}</span>
                                </div>
                                <p>
                                    {!! preg_replace('/<\/?div[^>]*>/', '', nl2br($sub_tasks->description)) !!}
                                </p>
                            </div>
                            @foreach($sub_tasks->subtask_message as $subtask_message)
                                <div class="list-0f-head msg-client">
                                    <div class="assign-task">
                                        <h2>
                                            {{ $subtask_message->user->name ?? '' }} {{ $subtask_message->user->last_name ?? '' }}
                                            <span>{{ $subtask_message->created_at->diffForHumans() }}</span>
                                        </h2>
                                        <span>{{ $subtask_message->created_at->format('d F Y, h:i A') }}</span>
                                    </div>
                                    <p>
                                        {!! preg_replace('/<\/?div[^>]*>/', '', nl2br($subtask_message->description)) !!}
                                    </p>
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="{{ asset('newglobal/js/image-uploader.min.js') }}"></script>
    <script src="{{ asset('global/js/fileinput.js') }}"></script>
    <script src="{{ asset('global/js/fileinput-theme.js') }}"></script>
    <script>
        $(document).ready(() => {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#update-task').click(function () {
                var value = $('#update-task-value').val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('v2.tasks.update.status', $task->id) }}",
                    data: {value: value},
                    success: function (response) {
                        if (response.status) {
                            toastr.success(response.message, '', {timeOut: 5000})
                        } else {
                            toastr.error(response.message, '', {timeOut: 5000})
                        }
                    }
                });
            });

            $('.btn_download_all_files').on('click', function () {
                $('.anchor_test').each((i, item) => {
                    item.click();
                });
            });


            $("#image-file").fileinput({
                showUpload: true,
                theme: 'fa',
                dropZoneEnabled: true,
                uploadUrl: "{{ url('/v2/tasks/upload-files') }}/{{$task->id}}",
                overwriteInitial: false,
                maxFileSize: 20000000,
                maxFilesNum: 20,
            });

            $("#image-file").on('fileuploaded', function (event, data, previewId, index, fileId) {
                $("#image-file").fileinput('refresh');
                $("#image-file").fileinput('reset');
                toastr.success('Image Updated Successfully', '', {timeOut: 5000})

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        });
    </script>
@endsection
