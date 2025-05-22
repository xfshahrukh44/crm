@extends('layouts.app-sale')
@section('title', 'Leads')
@push('styles')
<style>
    .select2-container {
        z-index: 9999;
        text-align: left;
    }
</style>
@endpush
@section('content')
<div class="breadcrumb row">
    <div class="col-md-6">
        <h1 class="mr-2">Leads</h1>
    </div>
    <div class="col-md-6 text-right">
            <a href="{{ route('sale.lead.create') }}" class="btn btn-primary">Create Lead</a>
    </div>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('sale.lead.index') }}" method="GET">
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
                            <select name="brand" id="brand" class="form-control select2">
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
                                <option value="Closed">Closed</option>
                                <option value="On Discussion">On Discussion</option>
                                <option value="Onboarded">Onboarded</option>
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

<div class="row">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3 count-card-title">Lead's Details <span> Total: {{ $data->total() }} <span></h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered"  style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Brand</th>
                                <th>Service(s)</th>
                                <th>Status</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $datas)
                            <tr>
                                <td>{{$datas->id}}</td>
                                <td><a href="{{ route('sale.lead.show', $datas->id) }}">{{$datas->name}} {{$datas->last_name}}</a></td>
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
                                    <button class="btn btn-info btn-sm">{{$datas->_brand->name ?? ''}}</button>
                                </td>

                                <td>
                                    @php
                                        $service_list = explode(',', $datas->service);
                                    @endphp
                                    @for($i = 0; $i < count($service_list); $i++)
                                        <span class="btn btn-info btn-sm">{{ $datas->services($service_list[$i])->name }}</span>
                                    @endfor
                                </td>

                                <td>
                                    <button class="btn btn-{{lead_status_color_class_map($datas->status)}} btn-sm">
                                        {{$datas->status}}
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('sale.lead.edit', $datas->id) }}" class="btn btn-primary btn-icon btn-sm">
                                        <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                        <span class="ul-btn__text">Edit</span>
                                    </a>
                                    <a href="{{ route('sale.lead.show', $datas->id) }}" class="btn btn-dark btn-icon btn-sm">
                                        <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                        <span class="ul-btn__text">View</span>
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
</script>
@endpush
