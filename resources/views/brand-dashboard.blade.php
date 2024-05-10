@extends($layout)
@section('title', 'Brands dashboard')
@push('styles')
    <style>
        img {
            height: 50px;
            object-fit: contain;
        }

        .card-body.text-center {
            min-height: 160px;
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
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <form action="{{ route('brands.dashboard') }}" method="GET">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="package">Search brand</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" value="{{ Request::get('brand_name') }}" placeholder="Brand information">
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
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <!-- CARD ICON-->
        <div class="row">
            @foreach($brands as $brand)
                <div class="col-lg-2 col-md-6 col-sm-6">

                    <a target="_blank" href="{{route('brands.detail', $brand->id)}}">
                        <div class="card card-icon mb-4" style="height: 180px;">
                            <div class="card-body text-center">
                                <div class="preview">
                                    @php
                                        $curl_handle=curl_init();
                                        curl_setopt($curl_handle, CURLOPT_URL, asset($brand->logo));
                                        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'test');
                                        $query = curl_exec($curl_handle);
                                        curl_close($curl_handle);
                                        $logo = (boolean)$query ? asset($brand->logo) : asset('images/noimg.png');
                                    @endphp
                                    <img style="" src="{{$logo}}" alt="">
                                </div>
                                <p class="text-muted mt-2 mb-2">{{$brand->name}}</p>
                                <small class="text-muted mt-2 mb-2">Clients: {{count($brand->clients)}} | Projects: {{count($brand->projects)}}</small>
{{--                                <p class="text-primary text-24 line-height-1 m-0">{{$brand->name}}</p>--}}
                            </div>
                        </div>
                    </a>

                </div>
            @endforeach
        </div>
            {{ $brands->links('pagination::bootstrap-4') }}
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