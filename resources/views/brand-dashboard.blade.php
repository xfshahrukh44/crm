@extends('layouts.app-admin')
@section('title', 'Brands dashboard')
@push('styles')
    <style>
        img {
            max-width: 100px;
        }

        .card-body.text-center {
            min-height: 150px;
        }

        p.text-muted.mt-2.mb-2 {
            font-size: 15px;
        }

        .card-body.text-center:hover {
            box-shadow: 0px 0px 15px 8px #00aeee1a;
        }
    </style>
@endpush
@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Brands dashboard</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <!-- CARD ICON-->
        <div class="row">
            @foreach($brands as $brand)
                <div class="col-lg-2 col-md-6 col-sm-6">

                    <a target="_blank" href="{{route('brands.detail', $brand->id)}}">
                        <div class="card card-icon mb-4">
                            <div class="card-body text-center">
                                <img src="{{asset($brand->logo)}}" alt="">
                                <p class="text-muted mt-2 mb-2">{{$brand->name}}</p>
                                <small class="text-muted mt-2 mb-2">Clients: {{count($brand->clients)}} | Projects: {{count($brand->projects)}}</small>
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{$brand->name}}</p>--}}
                            </div>
                        </div>
                    </a>

                </div>
            @endforeach
        </div>
    </div>
{{--    <div class="col-lg-12 col-md-12">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-12 col-md-12">--}}
{{--                <div class="card card-chart-bottom o-hidden mb-4">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="text-muted">123, 456 Sales</div>--}}
{{--                        <p class="mb-4 text-primary text-24">$123,456</p>--}}
{{--                    </div>--}}
{{--                    <div id="echartBar" style="height: 400px;"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
@endsection
@push('scripts')
<script>
    {{--var data = {!! json_encode($data->toArray()) !!};--}}
    {{--var data_array = [];--}}
    {{--var data_day = [];--}}
    {{--for(var i = 0; i < data.length; i++){--}}
    {{--    data_array.push(data[i].amount);--}}
    {{--    data_day.push(data[i].invoice_date);--}}
    {{--}--}}

    $(document).ready(function() {

    });
</script>
@endpush