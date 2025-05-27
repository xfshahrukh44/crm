<div>
{{--    @include('livewire.loader')--}}
    <style>
        .table h5 {
            margin: 0px !important;
            line-height: 33px !important;
        }

        tbody {
            display: block;
            max-height: 400px;
            overflow-y: auto;
        }

        thead, tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
    </style>

    <div class="breadcrumb">
        <h1 class="mr-2">Revenue</h1>
    </div>

    <div class="separator-breadcrumb border-top"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <!-- CARD ICON-->
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>Today</h3>
                </div>
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        Stats {{\Carbon\Carbon::parse(\Carbon\Carbon::today())->format('d F Y')}}
                                    </tr>
                                    <tr style="background-color: #ffff0044;">
                                        <th>
                                            <h5><b>ASSOCIATE</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>TARGET</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Net Achieved</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Printing Cost</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Refunds</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Achieved</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Target Achieved in %</b></h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                                <h5><b>{{$daily_data_item['user_body']->name . ' ' . $daily_data_item['user_body']->last_name}}</b></h5>
                                            </td>
                                            <td>
                                                <h5><b>${{$daily_data_item['daily_target']}}</b></h5>
{{--                                                <input class="form-control daily_target" type="number" name="" value="{{$daily_data_item['daily_target']}}" step=".01">--}}
                                            </td>
                                            <td>
                                                <h5><b>${{$daily_data_item['daily_net_achieved']}}</b></h5>
                                            </td>
                                            <td>
                                                <h5><b>${{$daily_data_item['daily_printing_costs']}}</b></h5>
{{--                                                <input class="form-control" type="number" name="" id="" value="{{$daily_data_item['daily_printing_costs']}}" step=".01">--}}
                                            </td>
                                            <td>
                                                <h5><b>${{$daily_data_item['daily_refunds']}}</b></h5>
                                            </td>
                                            <td style="background-color: {!! $daily_data_item['daily_achieved'] < $daily_data_item['daily_target'] ? '#ff000044' : '#90ee9044' !!};">
                                                <h5><b>${{$daily_data_item['daily_achieved']}}</b></h5>
                                            </td>
                                            <td>
                                                <h5><b>{{$daily_data_item['daily_target_achieved_in_percentage']}}%</b></h5>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $total_daily_target_achieved_in_percentage = number_format((($total_daily_net_achieved / $total_daily_target) * 100), 2);
                                    @endphp
                                    <tr style="background-color: #add8e644;">
                                        <td>
                                            <h5><b>TOTAL</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_daily_target}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_daily_net_achieved}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_daily_printing_costs}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_daily_refunds}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_daily_achieved}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>{{$total_daily_target_achieved_in_percentage}}%</b></h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">
                    <h3>This month</h3>
                </div>
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        Stats {{\Carbon\Carbon::parse(\Carbon\Carbon::today())->format('F Y')}}
                                    </tr>
                                    <tr style="background-color: #ffff0044;">
                                        <th>
                                            <h5><b>ASSOCIATE</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>TARGET</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Net Achieved</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Printing Cost</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Refunds</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Achieved</b></h5>
                                        </th>
                                        <th>
                                            <h5><b>Target Achieved in %</b></h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                                <h5><b>{{$monthly_data_item['user_body']->name . ' ' . $monthly_data_item['user_body']->last_name}}</b></h5>
                                            </td>
                                            <td>
                                                <h5><b>${{$monthly_data_item['monthly_target']}}</b></h5>
{{--                                                <input class="form-control daily_target" type="number" name="" value="{{$monthly_data_item['monthly_target']}}" step=".01">--}}
                                            </td>
                                            <td>
                                                <h5><b>${{$monthly_data_item['monthly_net_achieved']}}</b></h5>
                                            </td>
                                            <td>
                                                <h5><b>${{$monthly_data_item['monthly_printing_costs']}}</b></h5>
{{--                                                <input class="form-control" type="number" name="" id="" value="{{$monthly_data_item['monthly_printing_costs']}}" step=".01">--}}
                                            </td>
                                            <td>
                                                <h5><b>${{$monthly_data_item['monthly_refunds']}}</b></h5>
                                            </td>
                                            <td style="background-color: {!! $monthly_data_item['monthly_achieved'] < $monthly_data_item['monthly_target'] ? '#ff000044' : '#90ee9044' !!};">
                                                <h5><b>${{$monthly_data_item['monthly_achieved']}}</b></h5>
                                            </td>
                                            <td>
                                                <h5><b>{{$monthly_data_item['monthly_target_achieved_in_percentage']}}%</b></h5>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $total_monthly_target_achieved_in_percentage = number_format((($total_monthly_net_achieved / $total_monthly_target) * 100), 2);
                                    @endphp
                                    <tr style="background-color: #add8e644;">
                                        <td>
                                            <h5><b>TOTAL</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_monthly_target}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_monthly_net_achieved}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_monthly_printing_costs}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_monthly_refunds}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>${{$total_monthly_achieved}}</b></h5>
                                        </td>
                                        <td>
                                            <h5><b>{{$total_monthly_target_achieved_in_percentage}}%</b></h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
