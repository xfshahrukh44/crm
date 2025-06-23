@extends('v2.layouts.app')

@section('title', 'Clients')

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
        .client-actions-box {
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
                                    <h1 style="font-weight: 100;">Clients</h1>
                                </div>
                                <div class="col-md-6 m-auto d-flex justify-content-end">
                                    @if(v2_acl([2, 6, 4, 0]) && !user_is_cs())
                                        <a href="{{route('v2.clients.create')}}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i>
                                            Create
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <br>

                            {{--                                    <div class="search-invoice">--}}
                            <form class="search-invoice" action="{{route('v2.clients')}}" method="GET">
                                <input type="text" name="name" placeholder="Search name" value="{{ request()->get('name') }}">
                                <input type="text" name="email" placeholder="Search email" value="{{ request()->get('email') }}">
                                <select name="brand">
                                    <option value="">Select brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                                <select name="status">
                                    <option value="">Select status</option>
                                    <option value="1" {{ request()->get('status') ==  "1" ? 'selected' : ' '}}>Active</option>
                                    <option value="0" {{ request()->get('status') ==  "0" ? 'selected' : ' '}}>Deactive</option>
                                </select>
                                <input type="text" name="task_id" placeholder="Search by Task ID" id="task_id" value="{{ request()->get('task_id') }}">
                                <input type="date" name="start_date" placeholder="Start date" value="{{ request()->get('start_date') }}">
                                <input type="date" name="end_date" placeholder="Start date" value="{{ request()->get('end_date') }}">
                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>
                            {{--                                    </div>--}}

                            <table id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Create Login</th>
                                <th>Brand</th>
                                <th>Payment Link</th>
                                <th>Status</th>
                                @if(v2_acl([2]))
                                    <th>Login</th>
                                @endif
                                <th>Priority</th>
                                @if(v2_acl([2]))
                                    <th>Last login IP</th>
                                    <th>Device info</th>
                                @endif
                                <th>Action</th>

                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>{{$client->id}}</td>
                                        <td>
                                            <a class="p-2 bg-white" href="{{ route('v2.clients.show', $client->id) }}">
                                                {{$client->name}} {{$client->last_name}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark p-2 text-white btn_click_to_view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <span class="content_click_to_view" hidden>
                                                            {{$client->email}}
                                                        </span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark p-2 text-white btn_click_to_view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <span class="content_click_to_view" hidden>
                                                            {{$client->contact}}
                                                        </span>
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="badge bg-{{ $client->user ? 'success' : 'danger' }} text-white badge-sm p-2 auth-create"
                                               data-id="{{ $client->id }}"
                                               data-auth="{{ $client->user ? 1 : 0 }}"
                                               data-password="{{ $client->user ? '' : '' }}">
                                                {{ $client->user ? 'Reset' : 'Create' }}
                                            </a>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm">{{$client->brand->name ?? ''}}</button>
                                            @if($client->url != null)
                                                <button class="btn btn-secondary btn-sm">From Website</button>
                                            @endif
                                        </td>
                                        <td><a href="{{ route('v2.invoices.create', $client->id) }}" class="badge bg-primary text-white p-2 badge-sm">Generate Payment</a></td>
                                        <td class="text-center">
                                            @if($client->status == 1)
                                                <h5 class="mt-2">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                </h5>
                                            @else
                                                <h5 class="mt-2">
                                                    <i class="fas fa-times-circle text-danger"></i>
                                                </h5>
                                            @endif
                                        </td>
                                        @if(v2_acl([2]))
                                            <td>
                                                <a class="badge bg-primary text-white p-2 badge-sm" href="{{route('v2.admin.login_bypass', ['email' => $client->email])}}">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                    Login
                                                </a>
                                            </td>
                                        @endif
                                        <td class="text-center">{!! $client->priority_badge() !!}</td>
                                        @if(v2_acl([2]))
                                            <td>{{$client->last_login_ip ?? ''}}</td>
                                            <td>
                                                <i class="fas fa-desktop" style="cursor: pointer; font-size: 20px;" title="{{$client->last_login_device ?? ''}}"></i>
                                            </td>
                                        @endif
                                        <td style="position: relative;">
                                            <!-- Single Action Button -->
                                            <button type="button" class="badge badge-sm bg-light p-2" style="border: 0px;" onclick="toggleClientActions({{ $client->id }})">
                                                <i class="fas fa-bars"></i>
                                            </button>

                                            <!-- Hidden Popup Box -->
                                            <div id="clientActionsBox_{{ $client->id }}" class="client-actions-box text-center d-none">
                                                @if(v2_acl([2, 6, 4, 0]))
                                                    <a href="{{ route('v2.clients.edit', $client->id) }}" class="badge bg-primary badge-icon badge-sm text-white p-2">
                                                        <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                        <span class="ul-btn__text">
                                                                    <i class="fas fa-pencil"></i>
                                                                </span>
                                                    </a>
                                                @endif
                                                <a href="{{ route('v2.clients.show', $client->id) }}" class="badge bg-dark badge-icon badge-sm text-white p-2">
                                                    <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                                    <span class="ul-btn__text">
                                                                <i class="fas fa-eye"></i>
                                                            </span>
                                                </a>

                                                @if(v2_acl([2]))
                                                    <a href="#" onclick="event.preventDefault(); document.getElementById('client_delete_form_{{$client->id}}').submit();" class="badge bg-danger badge-icon badge-sm text-white p-2">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <form hidden id="client_delete_form_{{$client->id}}" method="POST" action="{{route('v2.clients.destroy', $client->id) }}">
                                                        {{ method_field('DELETE') }}
                                                        {{ csrf_field() }}
                                                    </form>
                                                @endif

                                                <a href="javascript:void(0);" class="badge bg-warning badge-icon badge-sm p-2 btn_open_notes" id="btn_open_notes_{{$client->id}}"
                                                   data-id="{{$client->id}}"
                                                   data-heading="Client (ID: {{$client->id}}): {{$client->name}} {{$client->last_name}}"
                                                   data-content="{{$client->comments}}"
                                                   data-modifier-check="{{($client->comments !== '' && !is_null($client->comments_id) && !is_null($client->comments_timestamp)) ? '1' : '0'}}"
                                                   data-modifier="{{($client->commenter->name ?? '') . ' ' . ($client->commenter->last_name ?? '') . ' ('.\Carbon\Carbon::parse($client->comments_timestamp)->format('d M Y h:i A').')'}}">

                                                    <span class="ul-btn__icon"><i class="fas fa-quote-right"></i></span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $clients->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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

                            @if(v2_acl([2, 0, 4, 6]))
                                <span id="btn_set_reminder" class="badge bg-danger text-white p-2" style="position:absolute; top: 7%; right: 5%; cursor: pointer" hidden>
                                        <i class="far fa-clock mr-1"></i>
                                        Set reminder
                                </span>
                            @endif
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

        <!-- Set Reminder Modal -->
        <div class="modal fade" id="modal_set_reminder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Set reminder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('v2.reminders.store')}}" method="POST">
                            @csrf
                            <input type="hidden" name="heading" id="reminder_heading">
                            <input type="hidden" name="text" id="reminder_text">

                            <div class="row">
                                <div class="col-md-12 form-group" id="reminder_label">
{{--                                    Reminder: <span><strong>"poopy ahh beyutch"</strong></span>--}}
                                </div>
                                <div class="col-md-12 form-group mb-0">
                                    <label for="">
                                        <strong>Date & time</strong>
                                    </label>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="datetime-local" class="form-control" name="ping_time" id="">
                                </div>
                                <div class="col-md-12 form-group text-center mb-0">
                                    <small><strong>OR</strong></small>
                                </div>
                                <div class="col-md-12 form-group mb-0">
                                    <label for="">
                                        <strong>Hours</strong>
                                    </label>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="number" step="1" class="form-control" name="hours" id="" min="0" max="24" value="0" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <button class="btn btn-primary btn-block" type="submit">Set</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    {{--            <div class="modal-footer">--}}
                    {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    {{--            </div>--}}
                </div>
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
            let heading = '';
            let focus_false_positive = false;
            $('.btn_open_notes').on('click', function () {
                current_rec_id = $(this).data('id');
                current_comment = $(this).data('content');
                heading = $(this).data('heading');
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
                if (focus_false_positive) {
                    focus_false_positive = false;
                    return false;
                }

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
                    url: "{{route('v2.clients.update.comments')}}",
                    data: {
                        comments: text_value,
                        rec_id: rec_id,
                    },
                    success:function(data) {
                        toastr.success(data.message);
                    }
                });
            });

            //#textarea_notes
            $('#textarea_notes').on('mouseup keyup', function () {
                const textarea = $(this)[0];

                let text_selected = (textarea.selectionStart !== textarea.selectionEnd);

                if (text_selected) {
                    $('#btn_set_reminder').fadeIn('slow').prop('hidden', false);
                } else {
                    $('#btn_set_reminder').fadeOut('slow').prop('hidden', true);
                }
            });

            $('body').on('click', '#btn_set_reminder', function () {
                const textarea = $('#textarea_notes')[0];

                let start = textarea.selectionStart;
                let end = textarea.selectionEnd;
                if (start === end) {
                    return false;
                }

                let text = textarea.value.substring(start, end);

                focus_false_positive = true;
                $('#modal_show_notes').modal('hide');
                $(this).fadeOut('slow').prop('hidden', true);

                $('#reminder_label').html(`Reminder: <span><strong>"`+text+`"</strong></span>`);
                $('#reminder_heading').val(heading);
                $('#reminder_text').val(text);
                $('#modal_set_reminder').modal('show');
            });
        });

        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }
        $('.auth-create').on('click', function () {
            var auth = $(this).data('auth');
            var id = $(this).data('id');
            var pass = generatePassword();
            if(auth == 0){
                swal({
                    title: "Enter Password",
                    input: "text",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Enter Password",
                    inputValue: pass
                }).then(function (inputValue) {
                    if (inputValue === false){
                        return swal({
                            title:"Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type:"danger"
                        })
                    }
                    if (inputValue === "") {
                        return swal({
                            title:"Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type:"danger"
                        })
                    }
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type:'POST',
                        url: "{{ route('v2.clients.create.auth') }}",
                        data: {id: id, pass:inputValue},
                        success:function(data) {
                            if(data.success == true){
                                swal("Auth Created", "Password is : " + inputValue, "success");

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }else{
                                return swal({
                                    title:"Error",
                                    text: "There is an Error, Plase Contact Administrator",
                                    type:"danger"
                                })
                            }
                        }
                    });
                });
            }else{
                swal({
                    title: "Enter Password",
                    input: "text",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Enter Password",
                    inputValue: pass
                }).then(function (inputValue) {
                    if (inputValue === false){
                        return swal({
                            title:"Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type:"danger"
                        })
                    }
                    if (inputValue === "") {
                        return swal({
                            title:"Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type:"danger"
                        })
                    }
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type:'POST',
                        url: "{{ route('v2.clients.update.auth') }}",
                        data: {id: id, pass:inputValue},
                        success:function(data) {
                            if(data.success == true){
                                swal("Auth Created", "Password is : " + inputValue, "success");

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }else{
                                return swal({
                                    title:"Error",
                                    text: "There is an Error, Plase Contact Administrator",
                                    type:"danger"
                                })
                            }
                        }
                    });
                });
            }
        });
    </script>

    <script>
        function toggleClientActions(clientId) {
            const box = document.getElementById(`clientActionsBox_${clientId}`);
            document.querySelectorAll('.client-actions-box').forEach(el => {
                if (el !== box) el.classList.add('d-none');
            });
            box.classList.toggle('d-none');
        }

        // Optional: hide on outside click
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.client-actions-box') && !event.target.closest('button')) {
                document.querySelectorAll('.client-actions-box').forEach(el => el.classList.add('d-none'));
            }
        });
    </script>
@endsection
