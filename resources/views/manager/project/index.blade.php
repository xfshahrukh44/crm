@extends('layouts.app-manager')

@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Projects</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form class="form">
                    <div class="form-body">
                        <div class="row">
                        <div class="col-md-3 col-lg form-group mb-0">
                                <select name="brand" class="form-control select2" >
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg form-group mb-0">
                                <select name="client" class="form-control select2" >
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                    <option value="{{$client->id}}">{{$client->name}} {{$client->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg form-group mb-0">
                                <select name="user" class="form-control select2" >
                                    <option value="">Select Sale Agent</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}} {{$user->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg form-group mb-0">
                                <select name="user" class="form-control select2" >
                                    <option value="">Select Category</option>
                                    @foreach($categorys as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg form-group mb-0">
                                <input type="number" name="invoice_id" id="" class="form-control" placeholder="Invoice ID" value="{{$_GET['invoice_id'] ?? ''}}">
                            </div>
                            <div class="col-md-3 col-lg form-group mb-3">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ Request::get('start_date') }}">
                            </div>
                            <div class="col-md-3 col-lg form-group mb-3">
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ Request::get('end_date') }}">
                            </div>
                            <div class="col-md-3 col-lg form-group mb-0">
                                <button class="btn btn-primary btn-block" type="submit">Search</button>
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
                <h4 class="card-title mb-3">Project Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered" id="zero_configuration_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
{{--                                <th>Invoice ID</th>--}}
                                <th>Name</th>
                                <th>Client</th>
                                <th>Assigned To</th>
                                <th>Brand</th>
                                <th>Reassign</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                            <tr>
                                <td>{{$datas->id}}</td>
{{--                                <td>{{$datas->invoice->id ?? 'N/A'}}</td>--}}
                                <td>{{$datas->name}}</td>
                                <td>
                                    {{$datas->client->name}} {{$datas->client->last_name}}<br>
                                    {{--                                    {{$datas->client->email}}--}}

                                    <br>
                                    <span>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                            <i class="fas fa-eye mr-1"></i>
                                            View email
                                        </a>
                                        <span class="content_click_to_view" hidden>
                                            {{$datas->client->email}}
                                        </span>
                                    </span>

                                    <span>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                            <i class="fas fa-eye mr-1"></i>
                                            View phone
                                        </a>
                                        <span class="content_click_to_view" hidden>
                                            {{$datas->client->contact}}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    {{$datas->added_by->name}} {{$datas->added_by->last_name}} <br>
                                    {{$datas->added_by->email}}
                                </td>
                                <td><button class="btn btn-info btn-sm">{{$datas->brand->name}}</button></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openReassign({{$datas->id}}, '{{$datas->brand->id}}')">Reassign</button>
                                </td>
                                <td>
                                    @if($datas->status == 1)
                                        <button class="btn btn-success btn-sm">Active</button>
                                    @else
                                        <button class="btn btn-danger btn-sm">Deactive</button>
                                    @endif
                                </td>
                                <td>
                                    @if($datas->form_checker == 1)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Logo Form</a>
                                    @elseif($datas->form_checker == 2)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Website Form</a>
                                    @elseif($datas->form_checker == 3)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">SMM Form</a>
                                    @elseif($datas->form_checker == 4)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Content Form</a>
                                    @elseif($datas->form_checker == 5)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">SEO Form</a>
                                    @elseif($datas->form_checker == 6)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Book Formatting & Publishing</a>
                                    @elseif($datas->form_checker == 7)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Book Writing</a>
                                    @elseif($datas->form_checker == 8)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Author Website</a>
                                    @elseif($datas->form_checker == 9)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Editing & Proofreading</a>
                                    @elseif($datas->form_checker == 10)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Book Cover Design</a>
                                    @elseif($datas->form_checker == 11)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">ISBN</a>
                                    @elseif($datas->form_checker == 12)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Book Printing</a>
                                    @elseif($datas->form_checker == 16)
                                    <a href="{{ route('manager.form', ['form_id' => $datas->form_id, 'check' => $datas->form_checker, 'id' => $datas->id]) }}" class="btn btn-secondary btn-sm">Press Release</a>
                                    @endif
                                    <!-- <a href="{{ route('manager.project.edit', $datas->id) }}" class="btn btn-primary btn-icon btn-sm">
                                        <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                        <span class="ul-btn__text">Edit</span>
                                    </a> -->

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
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Assigned To</th>
                                <th>Brand</th>
                                <th>Reassign</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assignModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="{{ route('manager.reassign.support') }}" method="post">
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
                url: "{{route('update.project.comments')}}",
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

    function openReassign(id, brand_id){
        $('#agent-name-wrapper').html('');
        var url = "{{ route('manager.client.agent', ":id") }}";
        url = url.replace(':id', brand_id);
        $.ajax({
            type:'GET',
            url: url,
            success:function(data) {
                var getData = data.data;
                for (var i = 0; i < getData.length; i++) {
                    $('#agent-name-wrapper').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                }
            }
        });
        $('#assignModel').find('#assign_id').val(id);
        $('#assignModel').modal('show');
    }
</script>
@endpush
