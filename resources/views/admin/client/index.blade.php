@extends('layouts.app-admin')
@section('title', 'Leads')
@push('styles')
    <style>
        .select2-container {
            /*z-index: 9999;*/
            text-align: left;
        }
    </style>
@endpush
@section('content')
    <div class="breadcrumb row">
        <div class="col-md-6">
            <h1 class="mr-2">Clients</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.client.create') }}" class="btn btn-primary">Create Client</a>
        </div>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card text-left">
                <div class="card-body">
                    <form action="{{ route('admin.client.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 form-group mb-3">
                                <label for="name">Search Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ Request::get('name') }}">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="email">Search Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{ Request::get('email') }}">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="brand">Search From Brand</label>
                                <select name="brand" id="brand" class="form-control">
                                    <option value="">Any</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}" {{ Request::get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="status">Select Status</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="">Any</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="email">Start date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ Request::get('start_date') }}">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="email">End date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ Request::get('end_date') }}">
                            </div>
                            <div class="col-md-12">
                                <div class="text-right">
                                    <button class="btn btn-primary">Search Result</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card text-left">
                <div class="card-body">
                    <h4 class="card-title mb-3 count-card-title">Client's Details <span> Total: {{ $data->total() }} <span></h4>
                    <div class="table-responsive">
                        <table class="display table table-striped table-bordered"  style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Create Login</th>
                                <th>Brand</th>
                                <th>Payment Link</th>
                                <th>Status</th>
                                <th>Login</th>
                                <th>Priority</th>
                                <th>Last login IP</th>
                                <th>Device info</th>
                                <th>Active</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $datas)
                                <tr>
                                    <td>{{$datas->id}}</td>
                                    <td><a href="{{ route('admin.client.show', $datas->id) }}">{{$datas->name}} {{$datas->last_name}}</a></td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                        <span class="content_click_to_view" hidden>
                                            {{$datas->email}}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                        <span class="content_click_to_view" hidden>
                                        {{$datas->contact}}
                                    </span>
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-{{ $datas->user_id == null ? 'primary' : 'success' }} btn-sm auth-create" data-id="{{ $datas->id }}" data-auth="{{ $datas->user_id == null ? 0 : 1 }}" data-password="{{ $datas->user_id == null ? '' : '' }}">{{ $datas->user_id == null ? 'Click Here' : 'Reset Pass' }}</a>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm">{{$datas->brand->name ?? ''}}</button>
                                        @if($datas->url != null)
                                            <button class="btn btn-secondary btn-sm">From Website</button>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('admin.invoice.index', $datas->id) }}" class="btn btn-primary btn-sm">Generate Payment</a></td>
                                    <td>
                                        @if($datas->status == 1)
                                            <button class="btn btn-success btn-sm">Active</button><br>
                                        @else
                                            <button class="btn btn-danger btn-sm">Deactive</button><br>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="{{route('admin.login_bypass', ['email' => $datas->email])}}">Login as {{$datas->name}} {{$datas->last_name}}</a>
                                    </td>
                                    <td>{!! $datas->priority_badge() !!}</td>
                                    <td>{{$datas->last_login_ip ?? ''}}</td>
                                    <td>
                                        <i class="fas fa-desktop" style="cursor: pointer; font-size: 20px;" title="{{$datas->last_login_device ?? ''}}"></i>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.client.edit', $datas->id) }}" class="btn btn-primary btn-icon btn-sm">
                                            <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                            <span class="ul-btn__text">Edit</span>
                                        </a>
                                        <a href="{{ route('admin.client.show', $datas->id) }}" class="btn btn-dark btn-icon btn-sm">
                                            <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                            <span class="ul-btn__text">View</span>
                                        </a>
                                        <form method="POST" action="{{route('admin.client.destroy', $datas->id) }}">
                                            {{ method_field('DELETE') }}
                                            {{ csrf_field() }}
                                            <button class="btn btn-danger btn-icon btn-sm" type="submit">
                                                <span class="ul-btn__icon"><i class="i-Delete-File"></i></span>
                                                <span class="ul-btn__text">Delete</span>
                                            </button>
                                        </form>

                                        <a href="javascript:void(0);" class="btn btn-warning btn-icon btn-sm btn_open_notes" id="btn_open_notes_{{$datas->id}}"
                                           data-id="{{$datas->id}}"
                                           data-content="{{$datas->comments}}"
                                           data-modifier-check="{{($datas->comments !== '' && !is_null($datas->comments_id) && !is_null($datas->comments_timestamp)) ? '1' : '0'}}"
                                           data-modifier="{{($datas->commenter->name ?? '') . ' ' . ($datas->commenter->last_name ?? '') . ' ('.\Carbon\Carbon::parse($datas->comments_timestamp)->format('d M Y h:i A').')'}}">

                                            <span class="ul-btn__icon"><i class="fas fa-quote-right"></i></span>
                                            <span class="ul-btn__text">Notes</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        {{ $data->appends(['name' => Request::get('name'), 'email' => Request::get('email'), 'brand' => Request::get('brand'), 'status' => Request::get('status')])->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
@endsection

@push('scripts')
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
                    url: "{{route('update.client.comments')}}",
                    data: {
                        comments: text_value,
                        rec_id: rec_id,
                    },
                    success:function(data) {
                        toastr.success(data.message);
                    }
                });
            });
        });

        let htmlTag = new Promise((resolve) => {
            setTimeout(() => {
                $.ajax({
                    type:'POST',
                    url: "{{ route('admin.client.agent') }}",
                    success:function(data) {
                        var getData = data.data;
                        htmlTag = '<select id="MySelect" class="form-control select2">';
                        for (var i = 0; i < getData.length; i++) {
                            htmlTag += '<option value="'+getData[i].id+'">'+getData[i].name + ' ' + getData[i].last_name +'</option>'
                        }
                        htmlTag += '</select>';
                        resolve(htmlTag);
                    }
                });
            }, 1000)
        })
        function getAgent(){

        }

        function assignAgent(id){
            getAgent()
            console.log(htmlTag);
            swal({
                title: 'Select Agent',
                html: htmlTag,
                showCancelButton: true,
                onOpen: function () {
                    $('.select2').select2();
                },
                inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                        if (value !== '') {
                            resolve();
                        } else {
                            resolve('You need to select a Tier');
                        }
                    });
                }
            }).then(function (result) {
                let agent_id = $('#MySelect option:selected').val();
                $.ajax({
                    type:'POST',
                    url: "{{ route('admin.client.update.agent') }}",
                    data: {id: id, agent_id:agent_id},
                    success:function(data) {
                        if(data.success == true){
                            swal("Agent Assigned", "Page will be loaded in order to reflect data", "success");
                            setTimeout(function () {
                                location.reload(true);
                            }, 3000);
                        }else{
                            return swal({
                                title:"Error",
                                text: "There is an Error, Plase Contact Administrator",
                                type:"danger"
                            })
                        }
                    }
                });
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        html: 'You selected: ' + result.value
                    });
                }
            });
        }
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
                    $.ajax({
                        type:'POST',
                        url: "{{ route('admin.client.createauth') }}",
                        data: {id: id, pass:inputValue},
                        success:function(data) {
                            if(data.success == true){
                                swal("Auth Created", "Password is : " + inputValue, "success");
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
                        url: "{{ route('admin.client.updateauth') }}",
                        data: {id: id, pass:inputValue},
                        success:function(data) {
                            if(data.success == true){
                                swal("Auth Created", "Password is : " + inputValue, "success");
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
@endpush
