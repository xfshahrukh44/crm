@extends('v2.layouts.app')

@section('title', 'Subtask Detail')

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
                            <p>{{$subtask->task->projects->name}} (ID: {{$subtask->task->id}}) </p>
                            <div class="Search2 justify-content-end">
{{--                                <select name="update-task-value" id="update-task-value" class="form-control w-200" style="border-radius: 30px;">--}}
{{--                                    <option value="">Select task status</option>--}}
{{--                                    <option value="0" {{($subtask->task->status == 0) ? 'selected' : ''}}>Open</option>--}}
{{--                                    <option value="1" {{($subtask->task->status == 1) ? 'selected' : ''}}>Re Open</option>--}}
{{--                                    <option value="2" {{($subtask->task->status == 2) ? 'selected' : ''}}>On Hold</option>--}}
{{--                                    <option value="3" {{($subtask->task->status == 3) ? 'selected' : ''}}>Completed</option>--}}
{{--                                    <option value="4" {{($subtask->task->status == 4) ? 'selected' : ''}}>In Progress</option>--}}
{{--                                    <option value="5" {{($subtask->task->status == 5) ? 'selected' : ''}}>Sent for Approval</option>--}}
{{--                                    <option value="6" {{($subtask->task->status == 6) ? 'selected' : ''}}>Incomplete Brief</option>--}}
{{--                                    <option value="7" {{($subtask->task->status == 7) ? 'selected' : ''}}>Sent for QA</option>--}}
{{--                                </select>--}}

{{--                                --}}{{--                                        <input class="form-control mr-sm-2" placeholder="Search">--}}
{{--                                <button class="btn btn-outline-success my-2 my-sm-0 green-assign" id="update-task">Update</button>--}}
                                {!! $subtask->get_status_badge() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="list-0f new-task-detail">
            <div class="container-fluid">
                <div class="row my-2">
                    <div class="col-md-2">
                        <a href="{{ route('v2.tasks.show', $subtask->task->id) }}" class="badge bg-info badge-sm text-white p-2">View Main Task Details</a>
                    </div>
                    <div class="col-md-10">

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="list-0f-head">
                            <h2>Subtask Details</h2>
                            <p>{!! $subtask->subtask->description !!}</p>
                            <div class="for-date">
                                <p>DUE DATE: {{\Carbon\Carbon::parse($subtask->duadate)->format('d F Y, h:i A')}}</p>
                                <p>CREATED AT: {{\Carbon\Carbon::parse($subtask->created_at)->format('d F Y, h:i A')}}</p>
                            </div>


                        </div>
                        <div class="list-0f-head">
                            <h2>Comments By {{ $subtask->assigned_by_user->name }} {{ $subtask->assigned_by_user->last_name }}</h2>
                            <p>{!! $subtask->comments !!}</p>
                            <div class="for-date">
                                <p>DUE DATE: {{\Carbon\Carbon::parse($subtask->duadate)->format('d F Y, h:i A')}}</p>
                                <p>CREATED AT: {{\Carbon\Carbon::parse($subtask->created_at)->format('d F Y, h:i A')}}</p>
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
                        @if(count($subtask->task->client_files_support))
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

                                    @foreach($subtask->task->client_files_support as $client_file)
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
                        <form action="{{route('v2.subtasks.add.message', $subtask->id)}}" method="POST">
                            @csrf
                            <input type="hidden" name="task_id" value="{{ $subtask->task->id }}">
                            <div class="list-0f-head sub-task-message">
                                <h2>Message</h2>
                                <textarea id="description" rows="5" class="form-control border-primary" name="description" required></textarea>
                                @error('description')
                                <label class="text-danger">{{ $message }}</label>
                                @enderror

                                <div class="sub-task">
                                    <a href="javascript:;" class="password-btn blue-assign" onclick="this.closest('form').submit();">Save</a>
                                </div>
                            </div>
                        </form>

                        @foreach($subtask->sub_tasks_message as $sub_tasks)
                            <div class="list-0f-head msg-client">
                                <div class="assign-task">
                                    <h2>
                                        {{ $sub_tasks->user->name ?? '' }} {{ $sub_tasks->user->last_name ?? '' }}
                                        <span>{{ $sub_tasks->created_at->diffForHumans() }}</span>
                                    </h2>
                                    <span>{{ $sub_tasks->created_at->format('d F Y, h:i A') }}</span>
                                </div>
                                <p>
                                    {!! preg_replace('/<\/?div[^>]*>/', '', nl2br($sub_tasks->messages)) !!}
                                </p>
                            </div>
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

            $('.btn_download_all_files').on('click', function () {
                $('.anchor_test').each((i, item) => {
                    item.click();
                });
            });


            let upload_url = '{{route('v2.tasks.upload.files', 'temp')}}';
            upload_url = upload_url.replace('temp', '{{$subtask->task->id}}');
            $("#image-file").fileinput({
                showUpload: true,
                theme: 'fa',
                dropZoneEnabled: true,
                uploadUrl: upload_url,
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
                }, 500);
            });
        });
    </script>
@endsection
