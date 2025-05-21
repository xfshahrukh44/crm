@extends('layouts.app-manager')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Tasks List</h3>
        <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a>
                    </li>
                    <li class="breadcrumb-item">Tasks</li>
                    <li class="breadcrumb-item">Tasks List</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('manager.task.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 form-group mb-3">
                            <label for="project">Search By Project Name</label>
                            <input type="text" class="form-control" id="project" name="project" value="{{ Request::get('project') }}">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="customer">Search Customer Name or Email</label>
                            <input type="text" class="form-control" id="customer" name="customer" value="{{ Request::get('customer') }}">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="agent">Search Agent Name or Email</label>
                            <input type="text" class="form-control" id="agent" name="agent" value="{{ Request::get('agent') }}">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="brand">Select Brand</label>
                            <select class="form-control select2" name="brand" id="brand">
                                <option value="0" {{ Request::get('brand') == 0 ? 'selected' : ''}} >Any</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ Request::get('brand') == $brand->id ? 'selected' : ''}} >{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="status">Select Status</label>
                            <select class="form-control select2" name="status" id="status">
                                <option value="" {{ Request::get('status') == null ? 'selected' : ''}} >Any</option>
                                <option value="0" {{ Request::get('status') == 0 && Request::get('status') != null ? 'selected' : '' }}>Open</option>
                                <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>Re Open</option>
                                <option value="2" {{ Request::get('status') == 2 ? 'selected' : '' }}>Hold</option>
                                <option value="3" {{ Request::get('status') == 3 ? 'selected' : '' }}>Completed</option>
                                <option value="4" {{ Request::get('status') == 4 ? 'selected' : '' }}>In Progress</option>
                                <option value="5" {{ Request::get('status') == 5 ? 'selected' : '' }}>Sent for Approval</option>
                                <option value="6" {{ Request::get('status') == 6 ? 'selected' : '' }}>Incomplete Brief</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="category">Select Category</label>
                            <select class="form-control select2" name="category" id="category">
                                <option value="0" {{ Request::get('category') == 0 ? 'selected' : ''}} >Any</option>
                                @foreach($categorys as $category)
                                <option value="{{ $category->id }}" {{ Request::get('category') == $category->id ? 'selected' : ''}} >{{ $category->name }}</option>
                                @endforeach
                            </select>
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

    @if(count($notify_data) != 0)
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card text-left">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Notification Task Details</h4>
                        <div class="table-responsive">
                            <table class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Active</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notify_data as $datas)
                                    <tr class="selected">
                                        <td>{{$datas->id}}</td>
                                        <td>
                                            <a href="{{route('manager.task.show', $datas->id)}}">{!! \Illuminate\Support\Str::limit(preg_replace("/<\/?a( [^>]*)?>/i", "", strip_tags($datas->description)), 30, $end='...') !!}</a>
                                        </td>
                                        <td>{{$datas->projects->name}}</td>
                                        <td>
                                            {{ $datas->projects->client->name }} {{ $datas->projects->client->last_name }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" title="{{ $datas->brand->name }}">{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $datas->brand->name))) }}</button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-dark" title="{{ $datas->category->name }}">{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $datas->category->name))) }}</button>
                                            @if(count($datas->member_list) != 0)
                                                <ul id="member-box" class="task-list-member-box">
                                                    @foreach($datas->member_list as $key => $member)
                                                        <li><div class="member-box"><a href="javascript:;" title="{{ $member->user->name . ' ' . $member->user->last_name }}"><span>{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $member->user->name . ' ' . $member->user->last_name))) }}</span></a></div></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>{!! $datas->project_status() !!}</td>
                                        <td>
                                            <a href="{{route('manager.task.show', $datas->id)}}" class="btn btn-primary btn-icon btn-sm">
                                                <span class="ul-btn__text">Details</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Active</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        {{$notify_data->links("pagination::bootstrap-4")}}
                    </div>
                </div>
            </div>
        </div>
    @endif

<div class="row">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">Task Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered" id="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Assigned to</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                            <tr>
                                <td>{{$datas->id}}</td>
                                <td><a href="{{route('manager.task.show', $datas->id)}}">{!! \Illuminate\Support\Str::limit(strip_tags($datas->description), 30, $end='...') !!}</a></td>
                                <td>{{$datas->projects->name}}</td>
                                <td>
                                    {{ $datas->projects->client->name }} {{ $datas->projects->client->last_name }} <br>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-info btn_click_to_view">
                                        <i class="fas fa-eye mr-1"></i>
                                        View
                                    </a>
                                    <span class="content_click_to_view" hidden>
                                        {{ $datas->projects->client->email }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm">{{ $datas->user->name }} {{ $datas->user->last_name }}</button>

                                    <?php if(auth()->user()->id == "33"){ ?>

                                    <button class="btn btn-primary btn-sm" onclick="openReassign({{$datas->id}}, '{{$datas->brand->id}}')">Reassign</button>

                                    <?php } ?>

                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm">{{ implode('', array_map(function($v) { return $v[0]; }, explode(' ', $datas->brand->name))) }}</button>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm">{{$datas->category->name}}</button>
                                </td>
                                <td>{!! $datas->project_status() !!}</td>
                                <td>
                                    <a href="{{route('manager.task.show', $datas->id)}}" class="btn btn-primary btn-icon btn-sm">
                                        <span class="ul-btn__text">Details</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Assigned to</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="ajax-loading"><img src="{{ asset('newglobal/images/loader.gif') }}" /></div>
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
            <form action="{{ route('manager.reassign.support.taskid') }}" method="post">

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

@push('scripts')
<script>
    $(document).ready(function(){
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
    });

    var SITEURL = "{{ url('/') }}";
    var page = 2;
    load_more(page);
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 1) {
            page++;
            load_more(page); //load content
        }
    });
    function load_more(page){
        $.ajax({
            url: SITEURL + "/manager/task?"+window.location.search.substr(1)+'&page='+ page,
            type: "get",
            datatype: "html",
            beforeSend: function(){
                $('.ajax-loading').show();
            }
        })
        .done(function(data){
            if(data.length == 0){
                $('.ajax-loading').html("No more records!");
                return;
            }
            $('.ajax-loading').hide();
            $("#display tbody").append(data);
        })
        .fail(function(jqXHR, ajaxOptions, thrownError){
            alert('No response from server');
        });
    }



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
