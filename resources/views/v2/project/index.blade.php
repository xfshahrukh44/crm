@extends('v2.layouts.app')

@section('title', 'Projects')

@section('css')
    <style>
        #zero_configuration_table td {
            word-break: break-all;
            max-width: 300px; /* adjust as needed */
            white-space: normal;
        }

        /*#zero_configuration_table th,*/
        /*#zero_configuration_table td {*/
        /*    vertical-align: middle;*/
        /*}*/
    </style>

    <style>
        .project-actions-box {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 100;
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="list-0f">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-0f-head for-invoice-listing table-responsive">
                            <div class="row text-left pr-3 pb-2">
                                <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                    <h1 style="font-weight: 100;">Projects</h1>
                                </div>
                                <div class="col-md-6 m-auto d-flex justify-content-end">
                                    {{--                                            <a href="#" class="btn btn-sm btn-success">--}}
                                    {{--                                                <i class="fas fa-plus"></i>--}}
                                    {{--                                                Create--}}
                                    {{--                                            </a>--}}
                                </div>
                            </div>

                            <br>

                            {{--                                    <div class="search-invoice">--}}
                            <form class="search-invoice" action="{{route('v2.projects')}}" method="GET">
                                <select name="brand">
                                    <option value="">Select brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="client" placeholder="Client Name / Email" value="{{ request()->get('client') }}">
                                <input type="text" name="user" placeholder="Agent Name / Email" value="{{ request()->get('user') }}">
                                {{--                                        <select name="category">--}}
                                {{--                                            <option value="">Select category</option>--}}
                                {{--                                            @foreach($categories as $category)--}}
                                {{--                                                <option value="{{$category->id}}" {{ request()->get('category') ==  $category->id ? 'selected' : ' '}}>{{$category->name}}</option>--}}
                                {{--                                            @endforeach--}}
                                {{--                                        </select>--}}
                                <input type="date" name="start_date" placeholder="Start date" value="{{ request()->get('start_date') }}">
                                <input type="date" name="end_date" placeholder="Start date" value="{{ request()->get('end_date') }}">

                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>
                            {{--                                    </div>--}}

                            <table id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Assigned To</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Active</th>

                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>{{$project->id}}</td>
                                        <td>
                                            {!! \Illuminate\Support\Str::limit(strip_tags($project->name), 20, $end='...') !!}
                                        </td>
                                        <td>
                                            {{$project->client->name}} {{$project->client->last_name}}<br>
                                            {{--                                    {{$project->client->email}}--}}

                                            <br>
                                            <span>
                                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark text-white p-2 btn_click_to_view">
                                                                <i class="fas fa-eye mr-1"></i>
                                                                View email
                                                            </a>
                                                            <span class="content_click_to_view" hidden>
                                                                {{$project->client->email}}
                                                            </span>
                                                        </span>

                                            <span>
                                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark text-white p-2 btn_click_to_view">
                                                                <i class="fas fa-eye mr-1"></i>
                                                                View phone
                                                            </a>
                                                            <span class="content_click_to_view" hidden>
                                                                {{$project->client->contact}}
                                                            </span>
                                                        </span>
                                        </td>
                                        <td>
                                            {{$project->added_by->name}} {{$project->added_by->last_name}} <br>
                                            {{$project->added_by->email}}
                                            <br>

                                            <a href="javascript:;" class="badge bg-success text-white badge-icon badge-sm p-2 btn_assign_project"
                                               data-id="{{$project->id}}"
                                               data-form="{{$project->form_checker}}"
                                               data-brand="{{$project->brand_id}}"
                                            >
                                                <span class="ul-btn__icon"><i class="i-Checked-User"></i></span>
                                                <span class="ul-btn__text">Re Assign</span>
                                            </a>
                                        </td>
                                        <td><button class="btn btn-info btn-sm">{{$project->brand->name}}</button></td>
                                        <td>
                                            @if($project->status == 1)
                                                <button class="btn btn-success btn-sm">Active</button>
                                            @else
                                                <button class="btn btn-danger btn-sm">Deactive</button>
                                            @endif
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($project->created_at)->format('d M y h:i A')}}
                                        </td>
                                        <td style="position: relative;">
                                            <!-- Single Action Button -->
                                            <button type="button" class="badge badge-sm bg-light p-2" style="border: 0px;" onclick="toggleProjectActions({{ $project->id }})">
                                                <i class="fas fa-bars"></i>
                                            </button>

                                            <!-- Hidden Popup Box -->
                                            <div id="projectActionsBox_{{ $project->id }}" class="project-actions-box text-center d-none">
                                                <a href="javascript:void(0);" class="badge bg-primary text-white badge-icon badge-sm p-2">
                                                    View Form
                                                </a>
                                                <a href="{{route('v2.tasks.create', $project->id)}}" class="badge bg-dark text-white badge-icon badge-sm p-2">
                                                    Create Task
                                                </a>
                                                <a href="javascript:void(0);" class="badge bg-warning badge-icon badge-sm p-2 btn_open_notes" id="btn_open_notes_{{$project->id}}"
                                                   data-id="{{$project->id}}"
                                                   data-content="{{$project->comments}}"
                                                   data-modifier-check="{{($project->comments !== '' && !is_null($project->comments_id) && !is_null($project->comments_timestamp)) ? '1' : '0'}}"
                                                   data-modifier="{{($project->commenter->name ?? '') . ' ' . ($project->commenter->last_name ?? '') . ' ('.\Carbon\Carbon::parse($project->comments_timestamp)->format('d M Y h:i A').')'}}">

                                                    <span class="ul-btn__icon"><i class="fas fa-quote-right"></i></span>
                                                    Notes
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $projects->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Notes Modal -->
    <div class="modal fade" id="modal_show_notes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Notes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <textarea class="form-control" name="" id="textarea_notes" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="row" id="div_modifier_info" hidden>
                        <div class="col-md-12">
                            <label for="">
                                <b>Last updated by:</b>
                            </label>
                        </div>
                        <div class="col-md-12">
                            <label for="" id="label_modifier_info">
                                asd asd (12-12-12 12:12 AM)
                            </label>
                        </div>
                    </div>
                </div>
                {{--            <div class="modal-footer">--}}
                {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                {{--            </div>--}}
            </div>
        </div>
    </div>

    <!--  Assign Model -->
    <div class="modal fade" id="assignModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('v2.projects.reassign') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="assign_id">
                        <input type="hidden" name="form" id="form_id">
                        <div class="form-group">
                            <label class="col-form-label" for="agent-name-wrapper">Name:</label>
                            <select name="agent_id" id="agent-name-wrapper" class="form-control">

                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary ml-2" type="submit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            function assignAgent(id, form, brand_id){
                $('#agent-name-wrapper').html('');
                var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
                url = url.replace('temp', brand_id);
                $.ajax({
                    type:'GET',
                    url: url,
                    success:function(data) {
                        var getData = data.data;
                        for (var i = 0; i < getData.length; i++) {
                            $('#agent-name-wrapper').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                        }

                        $('#agent-name-wrapper').select2();
                    }
                });
                $('#assignModel').find('#assign_id').val(id);
                $('#assignModel').find('#form_id').val(form);
                $('#assignModel').modal('show');
            }

            $('.btn_click_to_view').on('click', function () {
                $('.btn_click_to_view').each((i, item) => {
                    $(item).prop('hidden', false);
                });

                $('.content_click_to_view').each((i, item) => {
                    $(item).prop('hidden', true);
                });

                $(this).prop('hidden', true);
                $(this).parent().find('.content_click_to_view').prop('hidden', false);
            });

            let current_rec_id;
            let current_comment = '';
            $('.btn_open_notes').on('click', function () {
                current_rec_id = $(this).data('id');
                current_comment = $(this).data('content');
                $('#textarea_notes').val($(this).data('content'));

                $('#label_modifier_info').html($(this).data('modifier'));
                $('#div_modifier_info').prop('hidden', ($(this).data('modifier-check') == '0'));

                setTimeout(function () {
                    $('#textarea_notes').focus();
                }, 500);

                $('#modal_show_notes').modal('show');
            });

            $('#textarea_notes').on('keyup', function () {
                $('#btn_open_notes_' + current_rec_id).data('content', $(this).val());
            });

            $('#textarea_notes').on('focusout', function () {
                let text_value = $(this).val();
                if (text_value == current_comment) {
                    return false;
                }

                let rec_id = current_rec_id;

                $('#modal_show_notes').modal('hide');
                $('#div_modifier_info').prop('hidden', true);

                //ajax
                $.ajax({
                    type: 'POST',
                    url: "{{route('v2.projects.update.comments')}}",
                    data: {
                        comments: text_value,
                        rec_id: rec_id,
                    },
                    success:function(data) {
                        toastr.success(data.message);
                    }
                });
            });

            $('.btn_assign_project').on('click', function () {
                assignAgent($(this).data('id'), $(this).data('form'), $(this).data('brand'));
            });
        });
    </script>

    <script>
        function toggleProjectActions(projectId) {
            const box = document.getElementById(`projectActionsBox_${projectId}`);
            document.querySelectorAll('.project-actions-box').forEach(el => {
                if (el !== box) el.classList.add('d-none');
            });
            box.classList.toggle('d-none');
        }

        // Optional: hide on outside click
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.project-actions-box') && !event.target.closest('button')) {
                document.querySelectorAll('.project-actions-box').forEach(el => el.classList.add('d-none'));
            }
        });
    </script>
@endsection
