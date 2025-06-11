@extends('v2.layouts.app')

@section('title', 'Revenue')

@section('css')
    <style>
        .card-body {
            max-height: 600px !important;
            overflow-y: scroll !important;
        }
    </style>
@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="list-0f">
                    <div id="accordion">
                        <div class="row m-auto">
                            <div class="col-md-8 offset-md-2 text-center">
                                <h4 class="mb-0 font-weight-lighter">Select Unit</h4>
                            </div>
                            <div class="col-md-8 offset-md-2">
                                @foreach($buh_data as $key => $buh_data_item)
                                    <div class="card w-100 my-2">
                                        <a class="btn btn-link collapsed" data-toggle="collapse"
                                           data-target="#collapseOne_{{$key}}" aria-expanded="false"
                                           aria-controls="collapseOne_{{$key}}">
                                            <div class="card-header" id="headingOne_{{$key}}">
                                                <h5 class="mb-0 text-center">
                                                    {{$buh_data_item['buh']->pseudo_name ?? ($buh_data_item['buh']->name . ' ' . $buh_data_item['buh']->last_name)}}
                                                </h5>
                                            </div>
                                        </a>

                                        <div id="collapseOne_{{$key}}" class="collapse"
                                             aria-labelledby="headingOne_{{$key}}" data-parent="#accordion">
                                            <div class="card-body">
                                                @php
                                                    $daily_data = $buh_data_item['daily_data'];
                                                    $monthly_data = $buh_data_item['monthly_data'];
                                                @endphp

                                                @if(count($daily_data))
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="list-0f-head">
                                                                    <h2>Today</h2>
                                                                    <table>
                                                                        <tbody>
                                                                        <tr>
                                                                            <th>Name</th>
                                                                            <th>Target </th>
                                                                            <th>Gross Achieved </th>
                                                                            <th>Refund</th>
                                                                            <th>Printing Cost</th>
                                                                            <th>Net Achieved</th>
                                                                            <th>Target Achieved in %</th>

                                                                            {{--                                                                <th></th>--}}

                                                                        </tr>

                                                                        @php
                                                                            $total_daily_target = 0.00;
                                                                            $total_daily_net_achieved = 0.00;
                                                                            $total_daily_printing_costs = 0.00;
                                                                            $total_daily_refunds = 0.00;
                                                                            $total_daily_achieved = 0.00;
                                                                            $total_daily_target_achieved_in_percentage = 0.00;
                                                                        @endphp
                                                                        @foreach($daily_data as $daily_data_item)
                                                                            @php
                                                                                $total_daily_target += $daily_data_item['daily_target'];
                                                                                $total_daily_net_achieved += $daily_data_item['daily_net_achieved'];
                                                                                $total_daily_printing_costs += $daily_data_item['daily_printing_costs'];
                                                                                $total_daily_refunds += $daily_data_item['daily_refunds'];
                                                                                $total_daily_achieved += $daily_data_item['daily_achieved'];
                                                                                $total_daily_target_achieved_in_percentage += $daily_data_item['daily_target_achieved_in_percentage'];
                                                                            @endphp

                                                                            <tr>
                                                                                <td>
                                                                                    <div class="name-img">
                                                                                        <img src="{{asset('images/avatar.png')}}" class="img-fluid">
                                                                                        {{$daily_data_item['user_body']->name . ' ' . $daily_data_item['user_body']->last_name}}
                                                                                    </div>
                                                                                </td>
                                                                                <td>${{number_format($daily_data_item['daily_target'], 0)}}</td>
                                                                                <td>
                                                                                    <div class="prog prog1">
                                                                                        ${{number_format($daily_data_item['daily_net_achieved'], 0)}}
                                                                                        <div class="progress">

                                                                                            <div class="progress-done" data-done="{{round($daily_data_item['daily_target_achieved_in_percentage'])}}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="{{$daily_data_item['daily_refunds'] > 0.00 ? 'red' : 'green'}}">${{number_format($daily_data_item['daily_refunds'], 0)}}</td>
                                                                                <td class="{{$daily_data_item['daily_printing_costs'] > 0.00 ? 'red' : 'green'}}">${{number_format($daily_data_item['daily_printing_costs'], 0)}}</td>
                                                                                <td> <strong>${{number_format($daily_data_item['daily_achieved'], 0)}}</strong> </td>
                                                                                <td>{{round($daily_data_item['daily_target_achieved_in_percentage'])}}%</td>

                                                                                {{--                                                                    <td>--}}
                                                                                {{--                                                                        <div class="edit-pare">--}}
                                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                                                {{--                                                                            <div class="dropdown user-name">--}}
                                                                                {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                                                {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                                                {{--                                                                                    <div class="dropdown-header">--}}
                                                                                {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                                                {{--                                                                                    </div>--}}
                                                                                {{--                                                                                </div>--}}
                                                                                {{--                                                                            </div>--}}
                                                                                {{--                                                                        </div>--}}
                                                                                {{--                                                                    </td>--}}
                                                                            </tr>
                                                                        @endforeach
                                                                        @php
                                                                            $total_daily_target_achieved_in_percentage = number_format((($total_daily_net_achieved / $total_daily_target) * 100), 2);
                                                                        @endphp
                                                                        <tr class="bg-warning">
                                                                            <td>
                                                                                Total
                                                                            </td>
                                                                            <td>${{number_format($total_daily_target, 0)}}</td>
                                                                            <td>
                                                                                <div class="prog prog1">
                                                                                    ${{number_format($total_daily_net_achieved, 0)}}
                                                                                    <div class="progress">

                                                                                        <div class="progress-done" data-done="{{round($total_daily_target_achieved_in_percentage)}}">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="{{$total_daily_refunds > 0.00 ? 'red' : 'green'}}">${{number_format($total_daily_refunds, 0)}}</td>
                                                                            <td class="{{$total_daily_printing_costs > 0.00 ? 'red' : 'green'}}">${{number_format($total_daily_printing_costs, 0)}}</td>
                                                                            <td> <strong>${{number_format($total_daily_achieved, 0)}}</strong> </td>
                                                                            <td>{{round($total_daily_target_achieved_in_percentage)}}%</td>

                                                                            {{--                                                                    <td>--}}
                                                                            {{--                                                                        <div class="edit-pare">--}}
                                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                                            {{--                                                                            <div class="dropdown user-name">--}}
                                                                            {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                                            {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                                            {{--                                                                                    <div class="dropdown-header">--}}
                                                                            {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                                            {{--                                                                                    </div>--}}
                                                                            {{--                                                                                </div>--}}
                                                                            {{--                                                                            </div>--}}
                                                                            {{--                                                                        </div>--}}
                                                                            {{--                                                                    </td>--}}
                                                                        </tr>

                                                                        </tbody>
                                                                    </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(count($monthly_data))
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="list-0f-head">
                                                                    <h2>This Month</h2>
                                                                    <table>
                                                                        <tbody>
                                                                        <tr>
                                                                            <th>Name</th>
                                                                            <th>Target </th>
                                                                            <th>Gross Achieved </th>
                                                                            <th>Refund</th>
                                                                            <th>Printing Cost</th>
                                                                            <th>Net Achieved</th>
                                                                            <th>Target Achieved in %</th>

                                                                            {{--                                                                <th></th>--}}

                                                                        </tr>

                                                                        @php
                                                                            $total_monthly_target = 0.00;
                                                                            $total_monthly_net_achieved = 0.00;
                                                                            $total_monthly_printing_costs = 0.00;
                                                                            $total_monthly_refunds = 0.00;
                                                                            $total_monthly_achieved = 0.00;
                                                                            $total_monthly_target_achieved_in_percentage = 0.00;
                                                                        @endphp
                                                                        @foreach($monthly_data as $monthly_data_item)
                                                                            @php
                                                                                $total_monthly_target += $monthly_data_item['monthly_target'];
                                                                                $total_monthly_net_achieved += $monthly_data_item['monthly_net_achieved'];
                                                                                $total_monthly_printing_costs += $monthly_data_item['monthly_printing_costs'];
                                                                                $total_monthly_refunds += $monthly_data_item['monthly_refunds'];
                                                                                $total_monthly_achieved += $monthly_data_item['monthly_achieved'];
                                                                                $total_monthly_target_achieved_in_percentage += $monthly_data_item['monthly_target_achieved_in_percentage'];
                                                                            @endphp

                                                                            <tr>
                                                                                <td>
                                                                                    <div class="name-img">
                                                                                        <img src="{{asset('images/avatar.png')}}" class="img-fluid">
                                                                                        {{$monthly_data_item['user_body']->name . ' ' . $monthly_data_item['user_body']->last_name}}
                                                                                    </div>
                                                                                </td>
                                                                                <td>${{number_format($monthly_data_item['monthly_target'], 0)}}</td>
                                                                                <td>
                                                                                    <div class="prog prog1">
                                                                                        ${{number_format($monthly_data_item['monthly_net_achieved'], 0)}}
                                                                                        <div class="progress">

                                                                                            <div class="progress-done" data-done="{{round($monthly_data_item['monthly_target_achieved_in_percentage'])}}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="{{$monthly_data_item['monthly_refunds'] > 0.00 ? 'red' : 'green'}}">${{number_format($monthly_data_item['monthly_refunds'], 0)}}</td>
                                                                                <td class="{{$monthly_data_item['monthly_printing_costs'] > 0.00 ? 'red' : 'green'}}">${{number_format($monthly_data_item['monthly_printing_costs'], 0)}}</td>
                                                                                <td> <strong>${{number_format($monthly_data_item['monthly_achieved'], 0)}}</strong> </td>
                                                                                <td>{{round($monthly_data_item['monthly_target_achieved_in_percentage'])}}%</td>

                                                                                {{--                                                                    <td>--}}
                                                                                {{--                                                                        <div class="edit-pare">--}}
                                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                                                {{--                                                                            <div class="dropdown user-name">--}}
                                                                                {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                                                {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                                                {{--                                                                                    <div class="dropdown-header">--}}
                                                                                {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                                                {{--                                                                                    </div>--}}
                                                                                {{--                                                                                </div>--}}
                                                                                {{--                                                                            </div>--}}
                                                                                {{--                                                                        </div>--}}
                                                                                {{--                                                                    </td>--}}
                                                                            </tr>
                                                                        @endforeach
                                                                        @php
                                                                            $total_monthly_target_achieved_in_percentage = number_format((($total_monthly_net_achieved / $total_monthly_target) * 100), 2);
                                                                        @endphp
                                                                        <tr class="bg-warning">
                                                                            <td>
                                                                                Total
                                                                            </td>
                                                                            <td>${{number_format($total_monthly_target, 0)}}</td>
                                                                            <td>
                                                                                <div class="prog prog1">
                                                                                    ${{number_format($total_monthly_net_achieved, 0)}}
                                                                                    <div class="progress">

                                                                                        <div class="progress-done" data-done="{{round($total_monthly_target_achieved_in_percentage)}}">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="{{$total_monthly_refunds > 0.00 ? 'red' : 'green'}}">${{number_format($total_monthly_refunds, 0)}}</td>
                                                                            <td class="{{$total_monthly_printing_costs > 0.00 ? 'red' : 'green'}}">${{number_format($total_monthly_printing_costs, 0)}}</td>
                                                                            <td> <strong>${{number_format($total_monthly_achieved, 0)}}</strong> </td>
                                                                            <td>{{round($total_monthly_target_achieved_in_percentage)}}%</td>

                                                                            {{--                                                                    <td>--}}
                                                                            {{--                                                                        <div class="edit-pare">--}}
                                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                                            {{--                                                                            <div class="dropdown user-name">--}}
                                                                            {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                                            {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                                            {{--                                                                                    <div class="dropdown-header">--}}
                                                                            {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                                            {{--                                                                                    </div>--}}
                                                                            {{--                                                                                </div>--}}
                                                                            {{--                                                                            </div>--}}
                                                                            {{--                                                                        </div>--}}
                                                                            {{--                                                                    </td>--}}
                                                                        </tr>

                                                                        </tbody>
                                                                    </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @case(6)
                <section class="list-0f">
                    <div id="accordion">
                        <div class="row m-auto">
                            <div class="col-md-8 offset-md-2">
                                <h2 class="font-weight-lighter ml-4">Revenue</h2>
                            </div>
                            <div class="col-md-8 offset-md-2">
                                @if(count($daily_data))
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="list-0f-head">
                                                    <h2>Today</h2>
                                                    <table>
                                                        <tbody>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Target </th>
                                                            <th>Gross Achieved </th>
                                                            <th>Refund</th>
                                                            <th>Printing Cost</th>
                                                            <th>Net Achieved</th>
                                                            <th>Target Achieved in %</th>

                                                            {{--                                                                <th></th>--}}

                                                        </tr>

                                                        @php
                                                            $total_daily_target = 0.00;
                                                            $total_daily_net_achieved = 0.00;
                                                            $total_daily_printing_costs = 0.00;
                                                            $total_daily_refunds = 0.00;
                                                            $total_daily_achieved = 0.00;
                                                            $total_daily_target_achieved_in_percentage = 0.00;
                                                        @endphp
                                                        @foreach($daily_data as $daily_data_item)
                                                            @php
                                                                $total_daily_target += $daily_data_item['daily_target'];
                                                                $total_daily_net_achieved += $daily_data_item['daily_net_achieved'];
                                                                $total_daily_printing_costs += $daily_data_item['daily_printing_costs'];
                                                                $total_daily_refunds += $daily_data_item['daily_refunds'];
                                                                $total_daily_achieved += $daily_data_item['daily_achieved'];
                                                                $total_daily_target_achieved_in_percentage += $daily_data_item['daily_target_achieved_in_percentage'];
                                                            @endphp

                                                            <tr>
                                                                <td>
                                                                    <div class="name-img">
                                                                        <img src="{{asset('images/avatar.png')}}" class="img-fluid">
                                                                        {{$daily_data_item['user_body']->name . ' ' . $daily_data_item['user_body']->last_name}}
                                                                    </div>
                                                                </td>
                                                                <td>${{number_format($daily_data_item['daily_target'], 0)}}</td>
                                                                <td>
                                                                    <div class="prog prog1">
                                                                        ${{number_format($daily_data_item['daily_net_achieved'], 0)}}
                                                                        <div class="progress">

                                                                            <div class="progress-done" data-done="{{round($daily_data_item['daily_target_achieved_in_percentage'])}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="{{$daily_data_item['daily_refunds'] > 0.00 ? 'red' : 'green'}}">${{number_format($daily_data_item['daily_refunds'], 0)}}</td>
                                                                <td class="{{$daily_data_item['daily_printing_costs'] > 0.00 ? 'red' : 'green'}}">${{number_format($daily_data_item['daily_printing_costs'], 0)}}</td>
                                                                <td> <strong>${{number_format($daily_data_item['daily_achieved'], 0)}}</strong> </td>
                                                                <td>{{round($daily_data_item['daily_target_achieved_in_percentage'])}}%</td>

                                                                {{--                                                                    <td>--}}
                                                                {{--                                                                        <div class="edit-pare">--}}
                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                                {{--                                                                            <div class="dropdown user-name">--}}
                                                                {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                                {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                                {{--                                                                                    <div class="dropdown-header">--}}
                                                                {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                                {{--                                                                                    </div>--}}
                                                                {{--                                                                                </div>--}}
                                                                {{--                                                                            </div>--}}
                                                                {{--                                                                        </div>--}}
                                                                {{--                                                                    </td>--}}
                                                            </tr>
                                                        @endforeach
                                                        @php
                                                            $total_daily_target_achieved_in_percentage = number_format((($total_daily_net_achieved / $total_daily_target) * 100), 2);
                                                        @endphp
                                                        <tr class="bg-warning">
                                                            <td>
                                                                Total
                                                            </td>
                                                            <td>${{number_format($total_daily_target, 0)}}</td>
                                                            <td>
                                                                <div class="prog prog1">
                                                                    ${{number_format($total_daily_net_achieved, 0)}}
                                                                    <div class="progress">

                                                                        <div class="progress-done" data-done="{{round($total_daily_target_achieved_in_percentage)}}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="{{$total_daily_refunds > 0.00 ? 'red' : 'green'}}">${{number_format($total_daily_refunds, 0)}}</td>
                                                            <td class="{{$total_daily_printing_costs > 0.00 ? 'red' : 'green'}}">${{number_format($total_daily_printing_costs, 0)}}</td>
                                                            <td> <strong>${{number_format($total_daily_achieved, 0)}}</strong> </td>
                                                            <td>{{round($total_daily_target_achieved_in_percentage)}}%</td>

                                                            {{--                                                                    <td>--}}
                                                            {{--                                                                        <div class="edit-pare">--}}
                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                            {{--                                                                            <div class="dropdown user-name">--}}
                                                            {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                            {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                            {{--                                                                                    <div class="dropdown-header">--}}
                                                            {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                            {{--                                                                                    </div>--}}
                                                            {{--                                                                                </div>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </td>--}}
                                                        </tr>

                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(count($monthly_data))
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="list-0f-head">
                                                    <h2>This Month</h2>
                                                    <table>
                                                        <tbody>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Target </th>
                                                            <th>Gross Achieved </th>
                                                            <th>Refund</th>
                                                            <th>Printing Cost</th>
                                                            <th>Net Achieved</th>
                                                            <th>Target Achieved in %</th>

                                                            {{--                                                                <th></th>--}}

                                                        </tr>

                                                        @php
                                                            $total_monthly_target = 0.00;
                                                            $total_monthly_net_achieved = 0.00;
                                                            $total_monthly_printing_costs = 0.00;
                                                            $total_monthly_refunds = 0.00;
                                                            $total_monthly_achieved = 0.00;
                                                            $total_monthly_target_achieved_in_percentage = 0.00;
                                                        @endphp
                                                        @foreach($monthly_data as $monthly_data_item)
                                                            @php
                                                                $total_monthly_target += $monthly_data_item['monthly_target'];
                                                                $total_monthly_net_achieved += $monthly_data_item['monthly_net_achieved'];
                                                                $total_monthly_printing_costs += $monthly_data_item['monthly_printing_costs'];
                                                                $total_monthly_refunds += $monthly_data_item['monthly_refunds'];
                                                                $total_monthly_achieved += $monthly_data_item['monthly_achieved'];
                                                                $total_monthly_target_achieved_in_percentage += $monthly_data_item['monthly_target_achieved_in_percentage'];
                                                            @endphp

                                                            <tr>
                                                                <td>
                                                                    <div class="name-img">
                                                                        <img src="{{asset('images/avatar.png')}}" class="img-fluid">
                                                                        {{$monthly_data_item['user_body']->name . ' ' . $monthly_data_item['user_body']->last_name}}
                                                                    </div>
                                                                </td>
                                                                <td>${{number_format($monthly_data_item['monthly_target'], 0)}}</td>
                                                                <td>
                                                                    <div class="prog prog1">
                                                                        ${{number_format($monthly_data_item['monthly_net_achieved'], 0)}}
                                                                        <div class="progress">

                                                                            <div class="progress-done" data-done="{{round($monthly_data_item['monthly_target_achieved_in_percentage'])}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="{{$monthly_data_item['monthly_refunds'] > 0.00 ? 'red' : 'green'}}">${{number_format($monthly_data_item['monthly_refunds'], 0)}}</td>
                                                                <td class="{{$monthly_data_item['monthly_printing_costs'] > 0.00 ? 'red' : 'green'}}">${{number_format($monthly_data_item['monthly_printing_costs'], 0)}}</td>
                                                                <td> <strong>${{number_format($monthly_data_item['monthly_achieved'], 0)}}</strong> </td>
                                                                <td>{{round($monthly_data_item['monthly_target_achieved_in_percentage'])}}%</td>

                                                                {{--                                                                    <td>--}}
                                                                {{--                                                                        <div class="edit-pare">--}}
                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                                {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                                {{--                                                                            <div class="dropdown user-name">--}}
                                                                {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                                {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                                {{--                                                                                    <div class="dropdown-header">--}}
                                                                {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                                {{--                                                                                    </div>--}}
                                                                {{--                                                                                </div>--}}
                                                                {{--                                                                            </div>--}}
                                                                {{--                                                                        </div>--}}
                                                                {{--                                                                    </td>--}}
                                                            </tr>
                                                        @endforeach
                                                        @php
                                                            $total_monthly_target_achieved_in_percentage = number_format((($total_monthly_net_achieved / $total_monthly_target) * 100), 2);
                                                        @endphp
                                                        <tr class="bg-warning">
                                                            <td>
                                                                Total
                                                            </td>
                                                            <td>${{number_format($total_monthly_target, 0)}}</td>
                                                            <td>
                                                                <div class="prog prog1">
                                                                    ${{number_format($total_monthly_net_achieved, 0)}}
                                                                    <div class="progress">

                                                                        <div class="progress-done" data-done="{{round($total_monthly_target_achieved_in_percentage)}}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="{{$total_monthly_refunds > 0.00 ? 'red' : 'green'}}">${{number_format($total_monthly_refunds, 0)}}</td>
                                                            <td class="{{$total_monthly_printing_costs > 0.00 ? 'red' : 'green'}}">${{number_format($total_monthly_printing_costs, 0)}}</td>
                                                            <td> <strong>${{number_format($total_monthly_achieved, 0)}}</strong> </td>
                                                            <td>{{round($total_monthly_target_achieved_in_percentage)}}%</td>

                                                            {{--                                                                    <td>--}}
                                                            {{--                                                                        <div class="edit-pare">--}}
                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-pencil"></i></a>--}}
                                                            {{--                                                                            <a href="#"><i class="fa-solid fa-trash"></i></a>--}}

                                                            {{--                                                                            <div class="dropdown user-name">--}}
                                                            {{--                                                                                <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
                                                            {{--                                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
                                                            {{--                                                                                    <div class="dropdown-header">--}}
                                                            {{--                                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
                                                            {{--                                                                                    </div>--}}
                                                            {{--                                                                                </div>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </td>--}}
                                                        </tr>

                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="brand-1">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="brand-button">
                                    <ul>
                                        <li>
                                            <a href="#">
                                                <img src="{{asset('v2/images/button-img1.png')}}" class="img-fluid">
                                                Manage <strong>Clients</strong>
                                                <i class="fa-solid fa-angle-right"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <img src="{{asset('v2/images/button-img1.png')}}" class="img-fluid">
                                                Manage <strong>Users</strong>
                                                <i class="fa-solid fa-angle-right"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <img src="{{asset('v2/images/button-img1.png')}}" class="img-fluid">
                                                Manage <strong>Inovices</strong>
                                                <i class="fa-solid fa-angle-right"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <img src="{{asset('v2/images/button-img1.png')}}" class="img-fluid">
                                                Manage <strong>Brands</strong>
                                                <i class="fa-solid fa-angle-right"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <img src="{{asset('v2/images/button-img1.png')}}" class="img-fluid">
                                                Manage <strong>Revenue</strong>
                                                <i class="fa-solid fa-angle-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="brand-detail">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="brand-dela">
                                    <img src="{{asset('v2/images/brand-logo4.png')}}" class="img-fluid">

                                    <div class="brand-email">
                                        <div class="email">
                                            <a href="#">
                                                <i class="fa-solid fa-phone"></i> 13159901109
                                            </a>
                                        </div>
                                        <div class="email">
                                            <a href="#">
                                                <i class="fa-solid fa-envelope"></i>  info@designsventure.com
                                            </a>
                                        </div>
                                        <div class="email">
                                            <a href="#">
                                                <i class="fa-solid fa-globe"></i> www.thedesignscraft.com
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="client-serach clientpaa">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="client-serach-pare">
                                    <p>Search Client</p>

                                    <div class="Search2">
                                        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="list-0f">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head">
                                    <h2>List Members</h2>


                                    <table>
                                        <tbody>
                                        <tr>
                                            <th>NO.</th>
                                            <th>Name</th>
                                            <th>Target </th>
                                            <th>Gross Achieved </th>
                                            <th>Refund</th>
                                            <th>Not Achieved</th>
                                            <th>Target Achieved in %</th>

                                            <th></th>

                                        </tr>

                                        <tr>
                                            <td>1.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>




                                            <td>$12,000</td>

                                            <td>
                                                <div class="prog prog1">
                                                    $8,000
                                                    <div class="progress">

                                                        <div class="progress-done" data-done="60">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="red">$2000</td>
                                            <td> <strong>$2000</strong> </td>
                                            <td>70%</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="#"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>2.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>




                                            <td>$22,000</td>

                                            <td>
                                                <div class="prog prog1">
                                                    $18,000
                                                    <div class="progress">

                                                        <div class="progress-done" data-done="60">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="red">$500</td>
                                            <td> <strong>$500</strong> </td>
                                            <td>60%</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="#"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>3.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>
                                            <td>$15,000</td>
                                            <td>
                                                <div class="prog prog1">
                                                    $8,000
                                                    <div class="progress">
                                                        <div class="progress-done" data-done="60">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="red">$300</td>
                                            <td> <strong>$300</strong> </td>
                                            <td>45%</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="#"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>4.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>
                                            <td>$8,000</td>
                                            <td>
                                                <div class="prog prog1">
                                                    $7,000
                                                    <div class="progress">
                                                        <div class="progress-done" data-done="60">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="green">$0</td>
                                            <td> <strong>$0</strong> </td>
                                            <td>75%</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="#"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>5.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>
                                            <td>$15,000</td>
                                            <td>
                                                <div class="prog prog1">
                                                    $5,000
                                                    <div class="progress">
                                                        <div class="progress-done" data-done="60">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="red">$5,000</td>
                                            <td> <strong>$5,000</strong> </td>
                                            <td>75%</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="#"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>6.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>
                                            <td>$10,000</td>
                                            <td>
                                                <div class="prog prog1">
                                                    $6,000
                                                    <div class="progress">
                                                        <div class="progress-done" data-done="60">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="green">$0</td>
                                            <td> <strong>$0</strong> </td>
                                            <td>75%</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="#"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="#"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="#" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">
                                                            <div class="dropdown-header">
                                                                <i class="i-Lock-User mr-1"></i> Joan Zaidi
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('.progress-done').each(function (i, item) {
                $(item).css('width', $(this).data('done') + '%');
                $(item).css('opacity', '1');
            });
        });
    </script>
@endsection

