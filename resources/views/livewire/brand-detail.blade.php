<div>
    @include('livewire.loader')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /*img {*/
        /*    max-width: 50px;*/
        /*}*/

        .card-body.text-center {
            min-height: 150px;
        }

        p.text-muted.mt-2.mb-2 {
            font-size: 15px;
        }

        .card-body.text-center:hover {
            box-shadow: 0px 0px 15px 8px #00aeee1a;
        }
        /* Mouse over link */
        a {text-decoration: none; color: black;}   /* Mouse over link */

        /*.invoices_wrapper::-webkit-scrollbar {*/
        /*    display: none;*/
        /*}*/
        /*.invoices_wrapper {*/
        /*    -ms-overflow-style: none;  !* IE and Edge *!*/
        /*    scrollbar-width: none;  !* Firefox *!*/
        /*}*/
    </style>

    <div class="breadcrumb">
        <a href="#" class="btn btn-info btn-sm mr-2" wire:click="back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="mr-2">Brand detail</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            {{--brand detail--}}
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="row text-center">
                        <div class="col-md-6 offset-md-3">
                            <img src="{{asset($brand->logo)}}" alt="">
                        </div>
                    </div>
                    <div class="row text-center mb-4">
                        <div class="col-md-8 offset-md-2">
                            <h2>{{$brand->name}}</h2>
                            <div class="col-12">
                                <i class="fas fa-phone text-primary"></i>
                                <a href="tel:{{$brand->phone}}">{{$brand->phone}}</a>
                            </div>

                            <div class="col-12">
                                <i class="fas fa-envelope text-primary"></i>
                                <a href="mailto:{{$brand->email}}">{{$brand->email}}</a>
                            </div>

                            <div class="col-12">
                                <i class="fas fa-link text-primary"></i>
                                <a target="_blank" href="{{$brand->url}}">{{$brand->url}}</a>
                            </div>

                            <div class="row mt-4">
                                <div class="col"><p style="font-size: medium;">
                                        <i class="fas fa-check-double text-success"></i>
                                        <br />
                                        Services completed: {{$completed_projects_count}}/{{$total_projects_count}}
                                    </p></div>

                                @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6]))
                                    @if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6)
                                        <div class="col"><p style="font-size: medium;">
                                                <a href="#" wire:click="set_active_page('manager_notification-{{$brand->id}}')">
                                                    <i class="fas fa-bell text-danger"></i>
                                                    <br />
                                                    Notifications
                                                </a>
                                            </p></div>
                                    @endif

                                    <div class="col"><p style="font-size: medium;">
                                            <a href="#" data-toggle="modal" data-target="#salesFiguresModal">
                                                <i class="fas fa-dollar text-success"></i>
                                                <br />
                                                Sales figures
                                            </a>
                                        </p></div>
                                @endif


                                @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head))
                                    @php
                                        $briefs_pending_string = null;
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                            $briefs_pending_string = 'admin_brief_pending';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $briefs_pending_string = 'manager_brief_pending';
                                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                            $briefs_pending_string = 'sale_brief_pending';
                                        } else if (auth()->user()->is_employee == 4 && auth()->user()->is_support_head) {
                                            $briefs_pending_string = 'support_brief_pending';
                                        }
                                    @endphp
                                    <div class="col"><p style="font-size: medium;">
                                            <a href="#" wire:click="set_active_page('{{$briefs_pending_string}}-{{$brand->id}}')">
                                                <i class="i-Folder-Close text-primary"></i>
                                                <br />
                                                Briefs pending
                                            </a>
                                        </p></div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-4">
                <div class="col-md-6 offset-md-3 text-center" style="border: 1px solid #b7b7b7; background-color: #F3F3F3;">
                    <b>
                        <i class="fas fa-users"></i>
                        Team
                    </b>
                </div>
                <div class="col-md-6 offset-md-3" style="border: 1px solid #b7b7b7; border-top: 0px; background-color: #F3F3F3;">
                    @if(count($buhs))
                        <b>BUH</b>
                        <br />
                        <div class="row m-auto">
                            @foreach($buhs as $buh)
                                <a href="mailto:{{$buh->email}}">
                                    <h6 class="{!! $buh->id == auth()->id() ? 'text-success' : '' !!}">
                                        @if($buh->id == auth()->id())<b>@endif

                                            {{$buh->name . ' ' . $buh->last_name}}

                                            @if($buh->id == auth()->id())</b>@endif
                                    </h6>
                                </a>
                                <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                            @endforeach
                        </div>
                    @endif

                    @if(count($agents))
                        <b>Sales agents</b>
                        <br />
                        <div class="row m-auto">
                            @foreach($agents as $agent)
                                <a href="mailto:{{$agent->email}}">
                                    <h6 class="{!! $agent->id == auth()->id()  ? 'text-success' : ''!!}">
                                        @if($agent->id == auth()->id())<b>@endif

                                            {{$agent->name . ' ' . $agent->last_name}}

                                            @if($agent->id == auth()->id())</b>@endif
                                    </h6>
                                </a>
                                <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                            @endforeach
                        </div>
                    @endif

                    @if(count($support_heads))
                        <b>Support heads</b>
                        <br />
                        <div class="row m-auto">
                            @foreach($support_heads as $support_head)
                                <a href="mailto:{{$support_head->email}}">
                                    <h6 class="{!! $support_head->id == auth()->id() ? 'text-success' : '' !!}">
                                        @if($support_head->id == auth()->id())<b>@endif

                                            {{$support_head->name . ' ' . $support_head->last_name}}

                                            @if($support_head->id == auth()->id())</b>@endif
                                    </h6>
                                </a>
                                <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                            @endforeach
                        </div>
                    @endif

                    @if(count($customer_supports))
                        <b>Customer Support + Upsell</b>
                        <br />
                        <div class="row m-auto">
                            @foreach($customer_supports as $customer_support)
                                <a href="mailto:{{$customer_support->email}}">
                                    <h6 class="{!! $customer_support->id == auth()->id() ? 'text-success' : '' !!}">
                                        @if($customer_support->id == auth()->id())<b>@endif

                                            {{$customer_support->name . ' ' . $customer_support->last_name}}

                                            @if($customer_support->id == auth()->id())</b>@endif
                                    </h6>
                                </a>
                                <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="breadcrumb">
                <h1 class="mr-2">Clients</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card text-left">
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="package">Search client</label>
                                        <input type="text" class="form-control" id="client_name" name="client_name" wire:model.debounce.500ms="client_name" placeholder="Client information">
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card text-left">
                        <div class="card-body">
                            <h4 class="card-title mb-3 count-card-title">
                                Clients list
                                <span>
                                 Total: {{ $clients->total() }}
                                <br>
                                @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]) || (auth()->user()->is_employee == 4 && auth()->user()->is_support_head))
                                        @php
                                            if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                                                $create_client_string = 'admin_client_create';
                                            } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                                $create_client_string = 'manager_client_create';
                                            } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                                                $create_client_string = 'sale_client_create';
                                            } else if (auth()->user()->is_employee == 4 && auth()->user()->is_support_head) {
                                                $create_client_string = 'support_client_create';
                                            }
                                        @endphp
                                        <a class="btn btn-sm btn-success text-white" href="javascript:void(0)" wire:click="set_active_page('{{$create_client_string}}-{{$brand->id}}')">
                                        <i class="fas fa-plus"></i>
                                        Create client
                                    </a>
                                    @endif
                            </span>
                            </h4>
                            <div class="table-responsive">
                                <table class="display table table-striped tmble-bordered" style="width:100%" id="display">
                                    <thead>
                                    <tr>
                                        {{--                                    <th>ID</th>--}}
                                        <th>Client</th>
                                        <th>Invoices</th>
                                        <th>Services</th>
                                        <th>Added by</th>
                                        @if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head)
                                            <th>Create login</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clients as $client)
                                        @php
                                            $client_user = \App\Models\User::where('client_id', $client->id)->first();
                                            $project_count = $client_user ? count($client_user->projects) : 0;
                                            $client_projects = $client_user ? $client_user->recent_projects : [];
                                        @endphp
                                        <tr>
                                            {{--                                        <td><span class="btn btn-primary btn-sm">#{{ $client->id }}</span></td>--}}
                                            <td>
                                                <a href="#" wire:click="set_active_page('clients_detail-{{$client->id}}')">
                                                    <span class="btn btn-primary btn-sm">{{$client->name . ' ' . $client->last_name}}</span>
                                                </a>
                                            </td>
                                            <td>{{count($client->invoices)}}</td>
                                            <td>
                                                @foreach($client_projects as $client_project)
                                                    <a href="javascript:void(0)" wire:click="set_active_page('projects_detail-{{$client_project->id}}')" class="btn- btn btn-{!! no_pending_tasks_left($client_project->id) ? 'success' : 'warning' !!} btn-sm">{{str_replace($client_user->name, '', str_replace(' - ', '', $client_project->name))}}</a>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($client->added_by)
                                                    {{$client->added_by->name . ($client->added_by->last_name ? (' ' . $client->added_by->last_name) : '' )}}
                                                @endif
                                                <small class="text-success ml-2">
                                                    <b>{{\Carbon\Carbon::parse($client->created_at)->format('d M Y, h:i A')}}</b>
                                                </small>
                                            </td>
                                            @if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head)
                                                <td>
                                                    <a href="javascript:;"
                                                       class="btn btn-{{ $client->user == null ? 'primary auth_create' : 'success auth_update' }} btn-sm"
                                                       data-id="{{ $client->id }}"
                                                       data-auth="{{ $client->user == null ? 0 : 1 }}"
                                                       data-password="{{ $client->user == null ? '' : $client->user->password }}"
                                                    >
                                                        {{ $client->user == null ? 'Click Here' : 'Reset Pass' }}
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                                {{--                            <div class="ajax-loading"><img src="{{ asset('newglobal/images/loader.gif') }}" /></div>--}}
                                {{ $clients->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--sales figures modal--}}
    <div class="modal fade" id="salesFiguresModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Brand sales figures</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-striped">
                                @php
                                    $todays_invoice_ids = \App\Models\Invoice::where([
                                        'payment_status' => 2,
                                        'brand' => $brand->id,
                                    ])->where('updated_at', '>=', \Carbon\Carbon::now()->startOfDay())
                                    ->where('updated_at', '<=', \Carbon\Carbon::now()->endOfDay())
                                    ->pluck('id');
                                    $todays_invoice_totals = get_invoice_totals($todays_invoice_ids);

                                    $this_weeks_invoice_ids = \App\Models\Invoice::where([
                                        'payment_status' => 2,
                                        'brand' => $brand->id,
                                    ])->where('updated_at', '>=', \Carbon\Carbon::now()->startOfWeek())
                                    ->where('updated_at', '<=', \Carbon\Carbon::now()->endOfWeek())
                                    ->pluck('id');
                                    $this_weeks_invoice_totals = get_invoice_totals($this_weeks_invoice_ids);

                                    $this_months_invoice_ids = \App\Models\Invoice::where([
                                        'payment_status' => 2,
                                        'brand' => $brand->id,
                                    ])->where('updated_at', '>=', \Carbon\Carbon::now()->startOfMonth())
                                    ->where('updated_at', '<=', \Carbon\Carbon::now()->endOfMonth())
                                    ->pluck('id');
                                    $this_months_invoice_totals = get_invoice_totals($this_months_invoice_ids);

                                    $this_years_invoice_ids = \App\Models\Invoice::where([
                                        'payment_status' => 2,
                                        'brand' => $brand->id,
                                    ])->where('updated_at', '>=', \Carbon\Carbon::now()->startOfYear())
                                    ->where('updated_at', '<=', \Carbon\Carbon::now()->endOfYear())
                                    ->pluck('id');
                                    $this_years_invoice_totals = get_invoice_totals($this_years_invoice_ids);

                                    $last_years_invoice_ids = \App\Models\Invoice::where([
                                        'payment_status' => 2,
                                        'brand' => $brand->id,
                                    ])->where('updated_at', '>=', \Carbon\Carbon::now()->subYear(1)->startOfYear())
                                    ->where('updated_at', '<=', \Carbon\Carbon::now()->subYear(1)->endOfYear())
                                    ->pluck('id');
                                    $last_years_invoice_totals = get_invoice_totals($last_years_invoice_ids);
                                @endphp
                                <thead>
                                @if(count($todays_invoice_totals))
                                    <tr>
                                        <th>Today</th>
                                        @foreach($todays_invoice_totals as $symbol => $total)
                                            <th colspan="{{$loop->last ? '999' : ''}}">
                                                <strong>{{$symbol}}</strong>
                                                <span>{{number_format($total, 0)}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($this_weeks_invoice_totals))
                                    <tr>
                                        <th>This week</th>
                                        @foreach($this_weeks_invoice_totals as $symbol => $total)
                                            <th colspan="{{$loop->last ? '999' : ''}}">
                                                <strong>{{$symbol}}</strong>
                                                <span>{{number_format($total, 0)}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($this_months_invoice_totals))
                                    <tr>
                                        <th>This month</th>
                                        @foreach($this_months_invoice_totals as $symbol => $total)
                                            <th colspan="{{$loop->last ? '999' : ''}}">
                                                <strong>{{$symbol}}</strong>
                                                <span>{{number_format($total, 0)}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($this_years_invoice_totals))
                                    <tr>
                                        <th>This year</th>
                                        @foreach($this_years_invoice_totals as $symbol => $total)
                                            <th colspan="{{$loop->last ? '999' : ''}}">
                                                <strong>{{$symbol}}</strong>
                                                <span>{{number_format($total, 0)}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($last_years_invoice_totals))
                                    <tr>
                                        <th>Last year</th>
                                        @foreach($last_years_invoice_totals as $symbol => $total)
                                            <th colspan="{{$loop->last ? '999' : ''}}">
                                                <strong>{{$symbol}}</strong>
                                                <span>{{number_format($total, 0)}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                </thead>
                            </table>
                        </div>
                        <div class="col-md-12 invoices_wrapper" style="overflow-y: scroll; max-height: 500px;">
                            <h6>Search invoices</h6>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="start_date">Start date</label>
                                    <input type="date" class="form-control" id="start_date">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="end_date">End date</label>
                                    <input type="date" class="form-control" id="end_date">
                                </div>
                                <div class="form-group col-md-4">
                                    <button class="btn btn-primary btn-block mt-4" id="btn_search_invoices">Search</button>
                                </div>
                            </div>
                            <table id="table_invoices" class="table table-sm table-striped" hidden>
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Package Name</th>
                                    <th>User</th>
                                    <th>Agent</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody id="table_invoices_wrapper">

                                </tbody>
                            </table>
                            <div id="no-items" hidden>
                                <h6>No invoices found.</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.auth-create').on('click', function () {
                var auth = $(this).data('auth');
                var id = $(this).data('id');
                var pass = generatePassword();
                if(auth == 0){
                    swal({
                        title: "Enter Password",
                        input: "text",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        inputPlaceholder: "Enter Password",
                        inputValue: pass
                    }).then(function (inputValue) {
                        if (inputValue === false){
                            return swal({
                                title:"Field cannot be empty",
                                text: "Password Not Inserted/Updated because it is Empty",
                                type:"danger"
                            })
                        }
                        if (inputValue === "") {
                            return swal({
                                title:"Field cannot be empty",
                                text: "Password Not Inserted/Updated because it is Empty",
                                type:"danger"
                            })
                        }
                        $.ajax({
                            type:'POST',
                            url: "{{ route('support.client.createauth') }}",
                            data: {id: id, pass:inputValue, "_token": "{{csrf_token()}}"},
                            success:function(data) {
                                if(data.success == true){
                                    swal("Auth Created", "Password is : " + inputValue, "success");
                                }else{
                                    return swal({
                                        title:"Error",
                                        text: "There is an Error, Plase Contact Administrator",
                                        type:"danger"
                                    })
                                }
                            }
                        });
                    });
                }else{
                    swal({
                        title: "Enter Password",
                        input: "text",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        inputPlaceholder: "Enter Password",
                        inputValue: pass
                    }).then(function (inputValue) {
                        if (inputValue === false){
                            return swal({
                                title:"Field cannot be empty",
                                text: "Password Not Inserted/Updated because it is Empty",
                                type:"danger"
                            })
                        }
                        if (inputValue === "") {
                            return swal({
                                title:"Field cannot be empty",
                                text: "Password Not Inserted/Updated because it is Empty",
                                type:"danger"
                            })
                        }

                        $.ajax({
                            type:'POST',
                            url: "{{ route('support.client.updateauth') }}",
                            data: {id: id, pass:inputValue, "_token": "{{csrf_token()}}"},
                            success:function(data) {
                                if(data.success == true){
                                    swal("Auth Created", "Password is : " + inputValue, "success");
                                }else{
                                    return swal({
                                        title:"Error",
                                        text: "There is an Error, Plase Contact Administrator",
                                        type:"danger"
                                    })
                                }
                            }
                        });
                    });
                }
            });

            $('#btn_search_invoices').on('click', function () {
                $(this).text('Please wait');
                $(this).prop('disabled', true);
                $('#table_invoices_wrapper').html('');

                let data = {};
                data.brand = '{{$brand->id}}';
                if ($('#start_date').val()) {
                    data.start_date = $('#start_date').val();
                }
                if ($('#end_date').val()) {
                    data.end_date = $('#end_date').val();
                }

                $.ajax({
                    url: '{{route('get-invoices')}}',
                    method: 'GET',
                    data: data,
                    success: (data) => {
                        console.log(data);

                        let status_colors = [
                            'warning',
                            'danger',
                            'success',
                            'info',
                            'danger'
                        ];

                        let payment_statuses = [
                            'Drafted',
                            'Unpaid',
                            'Paid',
                            'Partially Paid',
                            'Cancelled',
                        ];
                        $('#table_invoices').prop('hidden', !(data.length > 0));
                        $('#no-items').prop('hidden', (data.length > 0));

                        let string = '';
                        for (const item of data) {
                            string += '<tr>';
                            string += '<td><span class="btn btn-sm btn-dark">#'+item.invoice_number+'</span></td>';
                            string += '<td>'+(item.package == 0 ? (item.custom_package ? item.custom_package : '') : (item.package ? item.package : ''))+'</td>';
                            string += '<td>'+item.name+'<br>'+item.email+'</td>';
                            string += '<td>'+item.sale.name+' '+item.sale.last_name+'</td>';
                            string += '<td><span class="btn btn-'+status_colors[item.payment_status]+' btn-sm">'+payment_statuses[item.payment_status]+'</span></td>';
                            string += '<td>'+item.currency_show.sign+''+item.amount+'</td>';
                            string += '</tr>';
                        }

                        $('#table_invoices_wrapper').html(string);
                        $(this).text('Search');
                        $(this).prop('disabled', false);
                    },
                    error: (e) => {
                        alert(e);
                        $('#table_invoices').prop('hidden', true);
                        $('#table_invoices').prop('hidden', false);
                        $(this).text('Search');
                        $(this).prop('disabled', false);
                    }
                });
            });
        });
    </script>
</div>