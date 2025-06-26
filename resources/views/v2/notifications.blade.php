@extends('v2.layouts.app')

@section('title', 'Notifications')

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

    <style>
        .unread_notification {
            background-color: #2978c21c;
        }
    </style>
@endsection

@section('content')
    @php
        $notifications = auth()->user()->notifications()
        ->when(v2_acl([6]) && in_array(auth()->id(), [3839, 3838, 3837]), function ($q) {
            return $q->where('type', 'App\Notifications\MessageNotification');
        })
        ->when(v2_acl([2]), function ($q) {
            return $q->whereIn('type', ['App\CustomInvoicePaidNotification', 'App\CustomInvoiceNotification']);
        })
        ->latest()->paginate(20);
    @endphp
    <div class="for-slider-main-banner">
        <section class="list-0f">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-0f-head for-invoice-listing table-responsive">
                            <div class="row text-left pr-3 pb-2">
                                <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                    <h1 style="font-weight: 100;">Notifications</h1>
                                </div>
                                <div class="col-md-6 m-auto d-flex justify-content-end">
                                    <a href="{{route('notification.all.read')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bell"></i>
                                        Mark all as read
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
                                    <th>Notification</th>
                                    <th>Detail</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </thead>
                                <tbody>
                                    @php
                                        $notification_map = [
                                            'App\Notifications\LeadNotification' => [
                                                'badge' => '<span class="badge badge-danger">Lead</span>',
                                                'route' => 'v2.leads.show',
                                                'key' => 'id',
                                            ],
                                            'App\Notifications\MessageNotification' => [
                                                'badge' => '<span class="badge badge-warning">Message</span>',
                                                'route' => '',
                                                'key' => '',
                                            ],
                                            'App\Notifications\TaskNotification' => [
                                                'badge' => '<span class="badge badge-primary">Task</span>',
                                                'route' => 'v2.tasks.show',
                                                'key' => 'task_id',
                                            ],
                                            'App\Notifications\AssignProjectNotification' => [
                                                'badge' => '<span class="badge badge-info">Project</span>',
                                                'route' => '',
                                                'key' => '',
                                            ],
                                            'App\Notifications\SubTaskNotification' => [
                                                'badge' => '<span class="badge badge-success">Subtask</span>',
                                                'route' => v2_acl([1]) ? 'v2.tasks.show' : 'v2.subtasks.show',
                                                'key' => v2_acl([1]) ? 'task_id' : 'id',
                                            ],
                                            'App\CustomInvoiceNotification' => [
                                                'badge' => '<span class="badge badge-danger">Invoice</span>',
                                                'route' => 'v2.invoices.show',
                                                'key' => 'invoice_id',
                                            ],
                                            'App\CustomInvoicePaidNotification' => [
                                                'badge' => '<span class="badge badge-success">Invoice</span>',
                                                'route' => 'v2.invoices.show',
                                                'key' => 'invoice_id',
                                            ],
                                        ];
                                    @endphp
                                    @foreach($notifications as $notification)
{{--                                        @dump($notification)--}}
                                        <tr class="{!! is_null($notification->read_at) ? 'unread_notification' : '' !!}" data-id="{{$notification->id}}">
                                            @php
                                                $notification_data = $notification_map[$notification->type];
                                                if (v2_acl([5])) {
                                                    $route = route('v2.subtasks.show', $notification->data['id']);
                                                } else {
                                                    $route = $notification_data['route'] !== '' ? route($notification_data['route'], $notification->data[$notification_data['key']]) : '';
                                                }
                                            @endphp
                                            <td>
                                                <a href="{{$route}}">
                                                    {{ strip_tags($notification->data['text']) }}
                                                </a>
                                            </td>
                                            <td>{{$notification->data['name']}}</td>
                                            <td>{!! $notification_data['badge'] ?? '' !!}</td>
                                            <td>{{Carbon\Carbon::parse($notification->created_at)->format('d F Y, h:i A')}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $notifications->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.unread_notification').on('click', function (e) {
                // e.preventDefault();
                $(this).removeClass('unread_notification');

                $.ajax({
                    url: "{{route('clear-notification')}}",
                    method: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        notification_id: $(this).data('id')
                    },
                    success: (data) => {
                        console.log(data);
                        window.location.href = $(this).find('a:first').prop('href');
                    },
                });


            });
        });
    </script>
@endsection
