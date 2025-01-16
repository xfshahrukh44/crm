@extends('layouts.app-sale')

@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">{{$lead->name}} (ID: {{$lead->id}})</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-md-6">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title mb-3">{{$lead->name}} Details</h4>
                <div class="table-responsive">
                    <table class="display table table-striped table-bordered" style="width:100%">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <td>{{$lead->name}} {{$lead->last_name}}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{$lead->email}}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{$lead->contact}}</td>
                            </tr>
                            <tr>
                                <th>Brand</th>
                                <td>{{$lead->_brand->name}}</td>
                            </tr>
                            @if($lead->service != null)
                            <tr>
                                <th>Service(s)</th>
                                <td>
                                    @php
                                        $service_list = explode(',', $lead->service);
                                    @endphp
                                    @for($i = 0; $i < count($service_list); $i++)
                                        <span class="btn btn-info btn-sm">{{ $lead->services($service_list[$i])->name }}</span>
                                    @endfor
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Status</th>
                                <td>
                                    <button class="btn btn-{{lead_status_color_class_map($lead->status)}} btn-sm">
                                        {{$lead->status}}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

@endpush
