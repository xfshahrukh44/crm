@extends('v2.layouts.app')

@section('title', 'Brands')

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
        .brand-actions-box {
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

    <style>
        /*td a {*/
        /*    color: unset !important;*/
        /*    background-color: unset !important;*/
        /*    padding: unset !important;*/
        /*    font-size: unset !important;*/
        /*    border-radius: unset !important;*/
        /*    font-weight: unset !important;*/
        /*}*/
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="list-0f">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing table-responsive">
                                    <div class="row text-left pr-3 pb-2">
                                        <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                            <h1 style="font-weight: 100;">Brands</h1>
                                        </div>
                                        <div class="col-md-6 m-auto d-flex justify-content-end">
                                            <a href="{{route('v2.brands.create')}}" class="btn btn-sm btn-success">
                                                <i class="fas fa-plus"></i>
                                                Create
                                            </a>
                                        </div>
                                    </div>

                                    <br>

{{--                                    <div class="search-invoice">--}}
{{--                                    <form class="search-invoice" action="{{route('v2.brands')}}" method="GET">--}}
{{--                                        <input type="text" name="name" placeholder="Search name" value="{{ request()->get('name') }}">--}}
{{--                                        <input type="text" name="email" placeholder="Search email" value="{{ request()->get('email') }}">--}}
{{--                                        <select name="brand">--}}
{{--                                            <option value="">Select brand</option>--}}
{{--                                            @foreach($brands as $brand)--}}
{{--                                                <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                        <select name="status">--}}
{{--                                            <option value="">Select status</option>--}}
{{--                                            <option value="1" {{ request()->get('status') ==  "1" ? 'selected' : ' '}}>Active</option>--}}
{{--                                            <option value="0" {{ request()->get('status') ==  "0" ? 'selected' : ' '}}>Deactive</option>--}}
{{--                                        </select>--}}
{{--                                        <input type="date" name="start_date" placeholder="Start date" value="{{ request()->get('start_date') }}">--}}
{{--                                        <input type="date" name="end_date" placeholder="Start date" value="{{ request()->get('end_date') }}">--}}
{{--                                        <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>--}}
{{--                                        <button hidden id="btn_filter_form" type="submit"></button>--}}
{{--                                    </form>--}}
{{--                                    </div>--}}

                                    <table id="zero_configuration_table" style="width: 100%;">
                                        <thead>

                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Url</th>
                                            <th>Auth Key</th>
                                            <th>Status</th>
                                            <th>Action</th>

                                        </thead>
                                        <tbody>
                                            @foreach($brands as $brand)
                                                <tr>
                                                    <td>{{$brand->id}}</td>
                                                    <td><img src="{{ asset($brand->logo)}}" width="100"></td>
                                                    <td><a href="{{ route('v2.brands.show', $brand->id) }}">{{$brand->name}}</a></td>
                                                    <td>{{$brand->phone}}</td>
                                                    <td>{{$brand->email}}</td>
                                                    <td>{{$brand->url}}</td>
                                                    <td><button class="badge badge-sm bg-info text-white p-2" style="border: 0px;" onclick="authKeyFunction(this)"><input type="hidden" value="{{$brand->auth_key}}">Copy</button></td>
                                                    <td>
                                                        @if($brand->status == 1)
                                                            <button class="badge bg-success badge-icon badge-sm text-white p-2" style="border: 0px;">Active</button>
                                                        @else
                                                            <button class="badge bg-danger badge-icon badge-sm text-white p-2" style="border: 0px;">Deactive</button>
                                                        @endif
                                                    </td>
                                                    <td style="position: relative;">
                                                        <a href="{{ route('v2.brands.edit', $brand->id) }}" class="badge bg-primary badge-icon badge-sm text-white p-2">
                                                            <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                            <span class="ul-btn__text">
                                                                <i class="fas fa-pencil"></i>
                                                            </span>
                                                        </a>
                                                        <a href="{{ route('v2.brands.show', $brand->id) }}" class="badge bg-dark badge-icon badge-sm text-white p-2">
                                                            <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                                            <span class="ul-btn__text">
                                                                <i class="fas fa-eye"></i>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end mt-2">
                                        {{ $brands->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="list-0f">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing">
                                    <table>
                                        <thead>

                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Public key</th>
                                        <th>Secret key</th>
                                        <th>Merchant</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td>123</td>
                                            <td>Merchant 1</td>
                                            <td>
                                                sadasdsadasdsadakjdyihd18272bd871bd82b
                                            </td>
                                            <td>
                                                sadasdsadasdsadakjdyihd18272bd871bd82b
                                            </td>
                                            <td>
                                                <div class="badge badge-sm bg-secondary text-white">STRIPE</div>
                                            </td>
                                            <td>
                                                <div class="badge badge-sm bg-danger text-white">Deactive</div>
                                            </td>
                                            <td>
                                                <a href="#" class="badge bg-primary">
                                                    <i class="fas fa-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
        @endswitch
    </div>
@endsection

@section('script')
    <script>
        function authKeyFunction(a) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(a).find('input').val()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.success("", "Auth Key Copied", {
                timeOut: "50000"
            });
        }
    </script>
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
