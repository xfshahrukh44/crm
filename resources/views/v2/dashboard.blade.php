@extends('v2.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="for-slider-main-banner">
        <section class="brand-1">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="brand-button">
                            <ul>
                                <li>
                                    <a href="{{route('v2.clients')}}">
                                        <img src="{{asset('v2/images/client-icon.png')}}" class="img-fluid">
                                        Manage <strong>Clients</strong>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <img src="{{asset('v2/images/users-icon.png')}}" class="img-fluid">
                                        Manage <strong>Users</strong>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('v2.invoices')}}">
                                        <img src="{{asset('v2/images/billing-icon.png')}}" class="img-fluid">
                                        Manage <strong>Inovices</strong>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('v2.brands')}}">
                                        <img src="{{asset('v2/images/brand-icon.png')}}" class="img-fluid">
                                        Manage <strong>Brands</strong>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('v2.revenue')}}">
                                        <img src="{{asset('v2/images/dollar-icon.png')}}" class="img-fluid">
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

        @switch($user_role_id)
            @case(2)
                @php
                    $current_month_label = \Carbon\Carbon::now()->monthName;
                    $total_user_count = \Illuminate\Support\Facades\DB::table('users')
                        ->where(['status' => 1, 'block' => 0])
                        ->where('is_employee', '!=', 3)
                        ->count();

                    $revenue = \Illuminate\Support\Facades\DB::table('invoices')
                        ->select(\Illuminate\Support\Facades\DB::raw('SUM(amount) as amount'),  'invoice_date')
                        ->whereDate('updated_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->where(['payment_status' => 2, 'currency' => 1])
                        ->orderBy('invoice_date', 'ASC')
                        ->sum('amount');

                    $refunds = \Illuminate\Support\Facades\DB::table('invoices')
                        ->whereNotNull('refund_cb_date')
                        ->select(\Illuminate\Support\Facades\DB::raw('SUM(refunded_cb) as refunded_cb'),  'invoice_date')
                        ->whereDate('updated_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->where(['payment_status' => 2, 'currency' => 1])
                        ->orderBy('invoice_date', 'ASC')
                        ->sum('refunded_cb');

                    $leads_count = \Illuminate\Support\Facades\DB::table('leads')
                        ->whereDate('created_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->count();
                @endphp
                <section class="revenu-sec">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="revnue-main">
                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Revenue</p>
                                            <h3>${{round($revenue, 0)}}</h3>
                                            <span>
                                                From 1st {{$current_month_label}} to till date
                                            </span>
                                        </div>

{{--                                        <div class="rev-bench">--}}
{{--                                            <p>Benchmark</p>--}}
{{--                                            <h3>$1,000,763</h3>--}}

{{--                                        </div>--}}
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Refunds/Chargebacks</p>
                                            <h3>${{round($refunds, 0)}}</h3>
                                            <span>From 1st {{$current_month_label}} to till date</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Net Sales</p>
                                            <h3>${{round(($revenue - $refunds), 0)}}</h3>
                                            <span>rom 1st {{$current_month_label}} to till date</span>
                                        </div>


                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Total Members</p>
                                            <h3>{{$total_user_count}}</h3>
                                            <span class="text-white">Data per {{$current_month_label}} 2025</span>
                                        </div>


                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Leads by Sales</p>
                                            <h3>{{$leads_count}}</h3>
                                            <span>From 1st {{$current_month_label}} to till date</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @case(6)
                @php
                    $current_month_label = \Carbon\Carbon::now()->monthName;
                    $total_user_count = \Illuminate\Support\Facades\DB::table('users')
                        ->where(['status' => 1, 'block' => 0])
                        ->where('is_employee', '!=', 3)
                        ->count();

                    $revenue = \Illuminate\Support\Facades\DB::table('invoices')
                        ->select(\Illuminate\Support\Facades\DB::raw('SUM(amount) as amount'),  'invoice_date')
                        ->whereDate('updated_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->where(['payment_status' => 2, 'currency' => 1])
                        ->whereIn('brand', auth()->user()->brand_list())
                        ->orderBy('invoice_date', 'ASC')
                        ->sum('amount');

                    $refunds = \Illuminate\Support\Facades\DB::table('invoices')
                        ->whereNotNull('refund_cb_date')
                        ->select(\Illuminate\Support\Facades\DB::raw('SUM(refunded_cb) as refunded_cb'),  'invoice_date')
                        ->whereDate('updated_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->where(['payment_status' => 2, 'currency' => 1])
                        ->whereIn('brand', auth()->user()->brand_list())
                        ->orderBy('invoice_date', 'ASC')
                        ->sum('refunded_cb');

                    $total_members = \App\Models\User::whereIn('is_employee', [0, 4])->whereHas('brands', function ($q) {
                            return $q->whereIn('id', auth()->user()->brand_list());
                        });

                    $leads_count = \Illuminate\Support\Facades\DB::table('leads')
                        ->whereDate('created_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->whereIn('brand', auth()->user()->brand_list())
                        ->count();
                @endphp
                <section class="revenu-sec">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="revnue-main">
                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Revenue</p>
                                            <h3>${{round($revenue, 0)}}</h3>
                                            <span>
                                                From 1st {{$current_month_label}} to till date
                                            </span>
                                        </div>

{{--                                        <div class="rev-bench">--}}
{{--                                            <p>Benchmark</p>--}}
{{--                                            <h3>$1,000,763</h3>--}}

{{--                                        </div>--}}
                                    </div>


                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Refunds/Chargebacks</p>
                                            <h3>${{round($refunds, 0)}}</h3>
                                            <span>From 1st {{$current_month_label}} to till date</span>
                                        </div>
                                    </div>


                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Net Sales</p>
                                            <h3>${{round(($revenue - $refunds), 0)}}</h3>
                                            <span>From 1st {{$current_month_label}} to till date</span>
                                        </div>


                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Total Members</p>
                                            <h3>{{$total_members->count()}}</h3>
                                            <span>Data per {{$current_month_label}} {{\Carbon\Carbon::now()->year}}</span>
                                        </div>


                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Leads by Sales</p>
                                            <h3>{{$leads_count}}</h3>
                                            <span>From 1st {{$current_month_label}} to till date</span>
                                        </div>
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
{{--                                                <th>Target Progress</th>--}}
                                                <th>Email</th>
                                                <th>Contact</th>
{{--                                                <th>Title</th>--}}
{{--                                                <th>Team</th>--}}
{{--                                                <th></th>--}}

                                            </tr>

                                            @foreach($total_members->get() as $key => $user)
                                                <tr>
                                                    <td>{{$key + 1}}.</td>
                                                    <td>
                                                        <div class="name-img">
                                                            <img src="{{asset('images/avatar.png')}}" class="img-fluid">
                                                            {{$user->name}} {{$user->last_name}}
                                                        </div>
                                                    </td>
{{--                                                    <td><div class="prog"> $12,000<div class="progress"><div class="progress-done" data-done="60"></div></div></div></td>--}}
                                                    <td>{{$user->email}}</td>
                                                    <td>{{$user->contact}}</td>
{{--                                                    <td>Logo Design</td>--}}
{{--                                                    <td>The Designs Planet</td>--}}
{{--                                                    <td>--}}
{{--                                                        <div class="edit-pare">--}}
{{--                                                            <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>--}}
{{--                                                            <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>--}}

{{--                                                            <div class="dropdown user-name">--}}
{{--                                                                <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>--}}
{{--                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(41px, 36px, 0px);">--}}
{{--                                                                    <div class="dropdown-header">--}}
{{--                                                                        <i class="i-Lock-User mr-1"></i> Joan Zaidi--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </td>--}}
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="revenu-sec">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="revnue-main">
                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Revenue</p>
                                            <h3>$528,763</h3>
                                            <span>From 1st December to till date</span>
                                        </div>

                                        <div class="rev-bench">
                                            <p>Benchmark</p>
                                            <h3>$1,000,763</h3>

                                        </div>
                                    </div>


                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Refunds/Chargebacks</p>
                                            <h3>$128,763</h3>
                                            <span>From 1st December to till date</span>
                                        </div>
                                    </div>


                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Net Sales</p>
                                            <h3>$228,763</h3>
                                            <span>rom 1st December to till date</span>
                                        </div>


                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Total Members</p>
                                            <h3>102</h3>
                                            <span>Data per December 2024</span>
                                        </div>


                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Leads by Sales</p>
                                            <h3>140</h3>
                                            <span>From 1st December to till date</span>
                                        </div>
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
                                            <th>Target Progress</th>
                                            <th>Email</th>
                                            <th>Number</th>
                                            <th>Title</th>
                                            <th>Team</th>
                                            <th></th>

                                        </tr>

                                        <tr>
                                            <td>1.</td>
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> Mark Anderson</div></td>
                                            <td><div class="prog"> $12,000<div class="progress"><div class="progress-done" data-done="60"></div></div></div></td>
                                            <td>Mark.anderson@gmail.com</td>
                                            <td>987-654-321-00</td>
                                            <td>Logo Design</td>
                                            <td>The Designs Planet</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
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
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> John Anderson</div></td>
                                            <td><div class="prog"> $12,000<div class="progress"><div class="progress-done" data-done="60"></div></div></div></td>
                                            <td>Mark.anderson@gmail.com</td>
                                            <td>987-654-321-00</td>
                                            <td>Book Design</td>
                                            <td>The Designs Planet</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
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
                                            <td><div class="prog"> $12,000<div class="progress"><div class="progress-done" data-done="60"></div></div></div></td>
                                            <td>Mark.anderson@gmail.com</td>
                                            <td>987-654-321-00</td>
                                            <td>Book Design</td>
                                            <td>The Designs Planet</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
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
                                            <td><div class="name-img"><img src="{{asset('v2/images/upsell2.png')}}" class="img-fluid"> John Anderson</div></td>
                                            <td><div class="prog"> $12,000<div class="progress"><div class="progress-done" data-done="60"></div></div></div></td>
                                            <td>Mark.anderson@gmail.com</td>
                                            <td>987-654-321-00</td>
                                            <td>Book Design</td>
                                            <td>The Designs Planet</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
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
                                            <td><div class="prog"> $12,000<div class="progress"><div class="progress-done" data-done="60"></div></div></div></td>
                                            <td>Mark.anderson@gmail.com</td>
                                            <td>987-654-321-00</td>
                                            <td>Book Design</td>
                                            <td>The Designs Planet</td>
                                            <td>
                                                <div class="edit-pare">
                                                    <a href="javascript:;"><i class="fa-solid fa-pencil"></i></a>
                                                    <a href="javascript:;"><i class="fa-solid fa-trash"></i></a>

                                                    <div class="dropdown user-name">
                                                        <a href="javascript:;" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></a>
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
