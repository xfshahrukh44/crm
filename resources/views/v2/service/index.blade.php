@extends('v2.layouts.app')

@section('title', 'Services')

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
        .service-actions-box {
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
        @switch($user_role_id)
            @case(2)
                <section class="list-0f">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing table-responsive">
                                    <div class="row text-left pr-3 pb-2">
                                        <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                            <h1 style="font-weight: 100;">Services</h1>
                                        </div>
                                        <div class="col-md-6 m-auto d-flex justify-content-end">
                                            <a href="{{route('v2.services.create')}}" class="btn btn-sm btn-success">
                                                <i class="fas fa-plus"></i>
                                                Create
                                            </a>
                                        </div>
                                    </div>

                                    <br>

{{--                                    <div class="search-invoice">--}}
{{--                                    <form class="search-invoice" action="{{route('v2.services')}}" method="GET">--}}
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
                                            <th>Name</th>
                                            <th>Form</th>
                                            <th>Action</th>

                                        </thead>
                                        <tbody>
                                            @php
                                                $service_form_map = [
                                                    0 => '<span class="badge bg-secondary text-white p-2 badge-sm">No Form</span>',
                                                    1 => '<span class="badge bg-info text-white p-2 badge-sm">Logo Form</span>',
                                                    2 => '<span class="badge bg-primary text-white p-2 badge-sm">Website Form</span>',
                                                    3 => '<span class="badge bg-dark text-white p-2 badge-sm">Social Media Marketing Form</span>',
                                                    4 => '<span class="badge bg-light text-white p-2 badge-sm">Content Writing Form</span>',
                                                    5 => '<span class="badge bg-success text-white p-2 badge-sm">Search Engine Optimization Form</span>',
                                                    6 => '<span class="badge bg-warning text-white p-2 badge-sm">Book Formatting & Publishing Form</span>',
                                                    7 => '<span class="badge bg-info text-white p-2 badge-sm">Book Writing Form</span>',
                                                    8 => '<span class="badge bg-info text-white p-2 badge-sm">Author Website Form</span>',
                                                    9 => '<span class="badge bg-light text-white p-2 badge-sm">Editing & Proofreading</span>',
                                                    10 => '<span class="badge bg-dark text-white p-2 badge-sm">Book Cover Design</span>',
                                                    11 => '<span class="badge bg-dark text-white p-2 badge-sm">ISBN Form</span>',
                                                    12 => '<span class="badge bg-dark text-white p-2 badge-sm">Book Printing Form</span>',
                                                    13 => '<span class="badge bg-primary text-white p-2 badge-sm">SEO Form</span>',
                                                    14 => '<span class="badge bg-primary text-white p-2 badge-sm">Book Marketing</span>',
                                                    15 => '<span class="badge bg-primary text-white p-2 badge-sm">Social Media Marketing Form (NEW)</span>',
                                                    16 => '<span class="badge bg-primary text-white p-2 badge-sm">Press Release Form</span>',
                                                ];
                                            @endphp
                                            @foreach($services as $service)
                                                <tr>
                                                    <td>{{$service->id}}</td>
                                                    <td>
                                                        {{$service->name}}
                                                    </td>
                                                    <td>
                                                        {!! $service_form_map[$service->form ?? 0] !!}
                                                    </td>
                                                    <td style="position: relative;">
                                                        <a href="{{ route('v2.services.edit', $service->id) }}" class="badge bg-primary badge-icon badge-sm text-white p-2">
                                                            <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                            <span class="ul-btn__text">
                                                                <i class="fas fa-pencil"></i>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
{{--                                    <div class="d-flex justify-content-end mt-2">--}}
{{--                                        {{ $services->appends(request()->query())->links() }}--}}
{{--                                    </div>--}}
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
        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        //     }
        // });

        $(document).ready(function () {

        });
    </script>
@endsection
