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
                                @if(v2_acl([2, 6, 4, 0]))
                                    <li>
                                        <a href="{{route('v2.clients')}}">
                                            <img src="{{asset('v2/images/client-icon.png')}}" class="img-fluid">
                                            Manage <strong>Clients</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(v2_acl([2, 6]))
                                    <li>
                                        <a href="{{route('v2.users.sales')}}">
                                            <img src="{{asset('v2/images/users-icon.png')}}" class="img-fluid">
                                            Manage <strong>Users</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(v2_acl([2, 6, 4, 0]))
                                    <li>
                                        <a href="{{route('v2.invoices')}}">
                                            <img src="{{asset('v2/images/billing-icon.png')}}" class="img-fluid">
                                            Manage <strong>Inovices</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(v2_acl([2]))
                                    <li>
                                        <a href="{{route('v2.brands')}}">
                                            <img src="{{asset('v2/images/brand-icon.png')}}" class="img-fluid">
                                            Manage <strong>Brands</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(v2_acl([2, 6]))
                                    <li>
                                        <a href="{{route('v2.revenue')}}">
                                            <img src="{{asset('v2/images/dollar-icon.png')}}" class="img-fluid">
                                            Manage <strong>Revenue</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(v2_acl([1]))
                                    <li>
                                        <a href="{{route('v2.tasks')}}">
                                            <img src="{{asset('v2/images/task-icon.png')}}" class="img-fluid">
                                            Manage <strong>Tasks</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(v2_acl([5]))
                                    <li>
                                        <a href="{{route('v2.subtasks')}}">
                                            <img src="{{asset('v2/images/task-icon.png')}}" class="img-fluid">
                                            Manage <strong>Subtasks</strong>
                                            <i class="fa-solid fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
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
                        ->whereDate('created_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->where(['payment_status' => 2, 'currency' => 1])
                        ->orderBy('invoice_date', 'ASC')
                        ->sum('amount');

                    $refunds = \Illuminate\Support\Facades\DB::table('invoices')
                        ->whereNotNull('refund_cb_date')
                        ->select(\Illuminate\Support\Facades\DB::raw('SUM(refunded_cb) as refunded_cb'),  'invoice_date')
                        ->whereDate('created_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->where(['payment_status' => 2, 'currency' => 1])
                        ->orderBy('invoice_date', 'ASC')
                        ->sum('refunded_cb');

                    $leads_count = \Illuminate\Support\Facades\DB::table('leads')
                        ->whereDate('created_at', '>=', Carbon\Carbon::now()->startOfMonth())
                        ->count();

                    $revenue_array = [];
                    for ($month_number = 1; $month_number <= \Carbon\Carbon::now()->month; $month_number++) {
                        $revenue_array []= \Illuminate\Support\Facades\DB::table('invoices')
                            ->whereMonth('created_at', $month_number)
                            ->whereYear('created_at', \Carbon\Carbon::now()->year)
                            ->where(['payment_status' => 2, 'currency' => 1])
                            ->sum('amount');
                    }

                    $cb_array = [];
                    for ($month_number = 1; $month_number <= \Carbon\Carbon::now()->month; $month_number++) {
                        $cb_array []= \Illuminate\Support\Facades\DB::table('invoices')
                            ->whereNotNull('refund_cb_date')
                            ->whereMonth('created_at', $month_number)
                            ->whereYear('created_at', \Carbon\Carbon::now()->year)
                            ->where(['payment_status' => 2, 'currency' => 1])
                            ->sum('refunded_cb');
                    }

                    $net_array = [];
                    $month_map = [
                        "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"
                    ];
                    $month_labels_array = [];

                    foreach ($revenue_array as $key => $item) {
                        $net_array []= ($item - $cb_array[$key]);
                        $month_labels_array []= $month_map[$key];
                    }
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
                                            <span class="text-white">Data per {{$current_month_label}} {{\Carbon\Carbon::now()->year}}</span>
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


                <section class="revenu-sec">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 pt-4">
                                <div style="width: 80%; margin: auto;">
                                    <canvas id="myChart"></canvas>
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

            @case(0)
                @php
                    $clients_count = \Illuminate\Support\Facades\DB::table('clients')->where('user_id', auth()->id())->count();
                    $paid_invoice = \Illuminate\Support\Facades\DB::table('invoices')->where('payment_status', 2)->where('sales_agent_id', auth()->id())->count();
                    $un_paid_invoice = \Illuminate\Support\Facades\DB::table('invoices')->where('payment_status', 1)->where('sales_agent_id', auth()->id())->count();
                    $briefs_pending_count = 0;
                    $auth_id = auth()->id();
                    foreach ([
                        'no_forms',
                        'logo_forms',
                        'web_forms',
                        'smm_forms',
                        'content_writing_forms',
                        'seo_forms',
                        'book_formattings',
                        'book_writings',
                        'author_websites',
                        'proofreadings',
                        'book_covers',
                        'isbnforms',
                        'bookprintings',
                        'seo_briefs',
                        'new_s_m_m_s',
                        'press_release_forms',
                    ] as $table) {
                        $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);

                        if (!$columns[1]) {
                            continue; // skip if no such column
                        }

                        // Query: where agent_id = auth()->id() and firstNonIdColumn is null
                        $exist_count = \Illuminate\Support\Facades\DB::table($table)
                            ->where('agent_id', $auth_id)
                            ->whereNull($columns[1])
                            ->count();

                        $briefs_pending_count += $exist_count;
                    }
                @endphp
                <section class="revenu-sec">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="revnue-main">
                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Clients</p>
                                            <h3 style="color: #059bd4;">{{$clients_count}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Paid invoices</p>
                                            <h3 style="color: #059bd4;">{{$paid_invoice}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Unpaid invoices</p>
                                            <h3 style="color: #059bd4;">{{$un_paid_invoice}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Briefs pending</p>
                                            <h3 style="color: #059bd4;">{{$briefs_pending_count}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @case(1)
                @php
                    $auth_category_ids = auth()->user()->category_list();
                    $total_task = \Illuminate\Support\Facades\DB::table('tasks')->whereIn('category_id', $auth_category_ids)->count();
                    $open_task = \Illuminate\Support\Facades\DB::table('tasks')->whereIn('category_id', $auth_category_ids)->where('status', 0)->count();
                    $reopen_task = \Illuminate\Support\Facades\DB::table('tasks')->whereIn('category_id', $auth_category_ids)->where('status', 1)->count();
                    $hold_task = \Illuminate\Support\Facades\DB::table('tasks')->whereIn('category_id', $auth_category_ids)->where('status', 2)->count();
                    $completed_task = \Illuminate\Support\Facades\DB::table('tasks')->whereIn('category_id', $auth_category_ids)->where('status', 3)->count();
                    $in_progress_task = \Illuminate\Support\Facades\DB::table('tasks')->whereIn('category_id', $auth_category_ids)->where('status', 4)->count();
                @endphp
                <section class="revenu-sec">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="revnue-main">
                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Total tasks</p>
                                            <h3 style="color: #059bd4;">{{$total_task}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Open</p>
                                            <h3 style="color: #059bd4;">{{$open_task}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Revision</p>
                                            <h3 style="color: #059bd4;">{{$reopen_task}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>In progress</p>
                                            <h3 style="color: #059bd4;">{{$in_progress_task}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>Completed</p>
                                            <h3 style="color: #059bd4;">{{$completed_task}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                    <div class="revnye-parent">
                                        <div class="rev-suv">
                                            <p>On hold</p>
                                            <h3 style="color: #059bd4;">{{$hold_task}}</h3>
                                            <span class="text-white">Bottom text</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default

        @endswitch
    </div>
@endsection

@if(v2_acl([2]))
    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const Utils = {
                CHART_COLORS: {
                    blue: 'rgb(54, 162, 235)',
                    red: 'rgb(255, 99, 132)',
                    green: 'rgb(75, 192, 192)'
                }
            };

            const DATA_COUNT = 12;
            const labels = @json($month_labels_array);
            const data = {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: @json($revenue_array),
                        borderColor: Utils.CHART_COLORS.blue,
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'CB',
                        data: @json($cb_array),
                        borderColor: Utils.CHART_COLORS.red,
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4
                    },
                    {
                        label: 'Net',
                        data: @json($net_array),
                        borderColor: Utils.CHART_COLORS.green,
                        fill: false,
                    }
                ]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Sales Chart'
                        }
                    },
                    interaction: {
                        intersect: false
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Year {{\Carbon\Carbon::now()->year}}',
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                // text: 'Value'
                            },
                            suggestedMin: -10,
                            suggestedMax: 200
                        }
                    }
                }
            };

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        </script>
    @endsection
@endif
