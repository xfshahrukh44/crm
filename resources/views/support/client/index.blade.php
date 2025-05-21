@extends('layouts.app-support')

@section('content')

<div class="breadcrumb row">
    <div class="col-md-6">
        <h1>Clients List</h1>
        <ul>
            <li><a href="#">Clients</a></li>
            <li>Clients List</li>
        </ul>
    </div>
    @if(auth()->user()->is_support_head)
        <div class="col-md-6 text-right">
            <a href="{{ route('support.client.create') }}" class="btn btn-primary">Create Client</a>
        </div>
    @endif
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('support.client.index') }}" method="GET">
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
                            <label for="contact">Search Contact</label>
                            <input type="text" class="form-control" id="contact" name="contact" value="">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="status">Select Status</label>
                            <select class="form-control select2" name="status" id="status">
                                <option value="">Any</option>
                                <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ Request::get('status') == 0 ? 'selected' : '' }}>Deactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="contact">Search by Task ID</label>
                            <input type="text" class="form-control" id="task_id" name="task_id" value="{{request()->get('task_id')}}">
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
                <h4 class="card-title mb-3">Client Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Brand</th>
                                <th>Payment Link</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                            <tr>
                                <td>{{$datas->id}}</td>
                                <td>{{$datas->name}} {{$datas->last_name}}</td>
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
                                <td><span class="btn btn-info btn-sm">{{$datas->brand->name}}</span></td>
                                <td><a href="{{ route('support.client.generate.payment', $datas->id) }}" class="btn btn-primary btn-sm">Generate Payment</a></td>
                                <td>
                                    @if($datas->status == 1)
                                        <span class="btn btn-success btn-sm">Active</span><br>
                                    @else
                                        <span class="btn btn-danger btn-sm">Deactive</span><br>
                                    @endif
                                </td>
                                <td>{!! $datas->priority_badge() !!}</td>
                                <td>
                                    <a href="{{route('support.client.edit', $datas->id)}}" class="btn btn-primary btn-icon btn-sm">
                                        <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                        <span class="ul-btn__text">Edit</span>
                                    </a>

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
                    {{ $data->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div>
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
@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
