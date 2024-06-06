@extends($layout)
@section('title', 'Brand detail')
@push('styles')
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
    </style>
@endpush
@section('content')
<div class="breadcrumb">
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
                    <div class="col-md-6 offset-md-3">
                        <h2>{{$brand->name}}</h2>
                        <p>
                            <i class="fas fa-check-double text-success"></i>
                            Services completed: {{$completed_projects_count}}/{{$total_projects_count}}
                        </p>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <h4>
                            <i class="fas fa-phone"></i>
                            <a href="tel:{{$brand->phone}}">{{$brand->phone}}</a>
                        </h4>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <h4>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{$brand->email}}">{{$brand->email}}</a>
                        </h4>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6 offset-md-3">
                        <h4>
                            <i class="fas fa-link"></i>
                            <a target="_blank" href="{{$brand->url}}">{{$brand->url}}</a>
                        </h4>
                    </div>
                </div>
                @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6]))
                    @if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6)
                        <div class="row text-center">
                            <div class="col-md-6 offset-md-3">
                                <h4>
                                    @php
                                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                                            $route = 'manager.notification';
                                        }
                                    @endphp
                                    <a target="_blank" href="{{route($route, ['brand_id' => $brand->id])}}" class="text-danger">
                                        <i class="fas fa-bell"></i>
                                        <span style="color: black !important;">Brand notifications</span>
                                    </a>
                                </h4>
                            </div>
                        </div>
                    @endif
                    <div class="row text-center">
                        <div class="col-md-6 offset-md-3">
                            <button class="btn btn-success" data-toggle="modal" data-target="#salesFiguresModal">
                                <i class="fas fa-dollar"></i>
                                Sales figures
                            </button>
                        </div>
                    </div>
                @endif
                @if (in_array(\Illuminate\Support\Facades\Auth::user()->is_employee, [2, 6, 0]))
                    @php
                        $briefs_pending_route = null;
                        if (\Illuminate\Support\Facades\Auth::user()->is_employee == 2) {
                            $briefs_pending_route = 'admin.brief.pending';
                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 6) {
                            $briefs_pending_route = 'manager.brief.pending';
                        } else if (\Illuminate\Support\Facades\Auth::user()->is_employee == 0) {
                            $briefs_pending_route = 'sale.brief.pending';
                        }
                    @endphp
                    @if (!is_null($briefs_pending_route))
                        <div class="row text-center mt-2">
                            <div class="col-md-6 offset-md-3">
                                <a class="btn btn-primary" href="{{route($briefs_pending_route, ['brand_id' => $brand->id])}}">
                                    View briefs pending
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="row text-center">
                            <div class="col-md-12">
                                <h6>
                                    <b>BUHs</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($buhs as $buh)
                                        <a href="mailto:{{$buh->email}}">
                                            <h6 class="{!! $buh->id == auth()->id() ? 'text-success' : '' !!}">{{$buh->name . ' ' . $buh->last_name}}</h6>
                                        </a>
                                        <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row text-center">
                            <div class="col-md-12">
                                <h6>
                                    <b>Agents</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($agents as $agent)
                                        <a href="mailto:{{$agent->email}}">
                                            <h6 class="{!! $agent->id == auth()->id()  ? 'text-success' : ''!!}">{{$agent->name . ' ' . $agent->last_name}}</h6>
                                        </a>
                                        <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row text-center">
                            <div class="col-md-12">
                                <h6>
                                    <b>Support heads</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($support_heads as $support_head)
                                        <a href="mailto:{{$support_head->email}}">
                                            <h6 class="{!! $support_head->id == auth()->id() ? 'text-success' : '' !!}">{{$support_head->name . ' ' . $support_head->last_name}}</h6>
                                        </a>
                                        <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row text-center">
                            <div class="col-md-12">
                                <h6>
                                    <b>Customer support</b>
                                </h6>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="row">
                                    @foreach($customer_supports as $customer_support)
                                        <a href="mailto:{{$customer_support->email}}">
                                            <h6 class="{!! $customer_support->id == auth()->id() ? 'text-success' : '' !!}">{{$customer_support->name . ' ' . $customer_support->last_name}}</h6>
                                        </a>
                                        <h6>{!! ($loop->last ? '.' : ",&nbsp;&nbsp;") !!}</h6>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <form action="{{ route('brands.detail', $brand->id) }}" method="GET">
                            <div class="row">
                                <div class="col-md-12 form-group mb-3">
                                    <label for="package">Search client</label>
                                    <input type="text" class="form-control" id="client_name" name="client_name" value="{{ Request::get('client_name') }}" placeholder="Client information">
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
        <div class="row">
            <div class="col-md-12">
                <div class="card text-left">
                    <div class="card-body">
                        <h4 class="card-title mb-3 count-card-title">Clients list <span> Total: {{ $clients->total() }} </span></h4>
                        <div class="table-responsive">
                            <table class="display table table-striped table-bordered" style="width:100%" id="display">
                                <thead>
                                <tr>
{{--                                    <th>ID</th>--}}
                                    <th>Client</th>
                                    <th>Invoices</th>
                                    <th>Projects</th>
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
                                    @endphp
                                    <tr>
{{--                                        <td><span class="btn btn-primary btn-sm">#{{ $client->id }}</span></td>--}}
                                        <td>
                                            <a target="_blank" href="{{route('clients.detail', $client->id)}}">
                                                <span class="btn btn-primary btn-sm">{{$client->name . ' ' . $client->last_name}}</span>
                                            </a>
                                        </td>
                                        <td>{{count($client->invoices)}}</td>
                                        <td>{{$project_count}}</td>
                                        @if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head)
                                            <td>
                                                <a href="javascript:;"
                                                   class="btn btn-{{ $client->user == null ? 'primary' : 'success' }} btn-sm auth-create"
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
                                <tfoot>
                                <tr>
{{--                                    <th>ID</th>--}}
                                    <th>Client</th>
                                    <th>Invoices</th>
                                    <th>Projects</th>
                                    @if (\Illuminate\Support\Facades\Auth::user()->is_employee == 4 && \Illuminate\Support\Facades\Auth::user()->is_support_head)
                                        <th>Create login</th>
                                    @endif
                                </tr>
                                </tfoot>
                            </table>
{{--                            <div class="ajax-loading"><img src="{{ asset('newglobal/images/loader.gif') }}" /></div>--}}
                            {{ $clients->links('pagination::bootstrap-4') }}
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
                            @endphp
                            <thead>
                                @if(count($todays_invoice_totals))
                                    <tr>
                                        <th>Today</th>
                                        @foreach($todays_invoice_totals as $symbol => $total)
                                            <th>
                                                <strong>{{$symbol}}</strong>
                                                <span>{{$total}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($this_weeks_invoice_totals))
                                    <tr>
                                        <th>This week</th>
                                        @foreach($this_weeks_invoice_totals as $symbol => $total)
                                            <th>
                                                <strong>{{$symbol}}</strong>
                                                <span>{{$total}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($this_months_invoice_totals))
                                    <tr>
                                        <th>This month</th>
                                        @foreach($this_months_invoice_totals as $symbol => $total)
                                            <th>
                                                <strong>{{$symbol}}</strong>
                                                <span>{{$total}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                                @if(count($this_years_invoice_totals))
                                    <tr>
                                        <th>This year</th>
                                        @foreach($this_years_invoice_totals as $symbol => $total)
                                            <th>
                                                <strong>{{$symbol}}</strong>
                                                <span>{{$total}}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                @endif
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-12">
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
@endsection
@push('scripts')
{{--<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>--}}
<script>
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    //     }
    // });
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
    {{--var data = {!! json_encode($data->toArray()) !!};--}}
    {{--var data_array = [];--}}
    {{--var data_day = [];--}}
    {{--for(var i = 0; i < data.length; i++){--}}
    {{--    data_array.push(data[i].amount);--}}
    {{--    data_day.push(data[i].invoice_date);--}}
    {{--}--}}

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
                    // $.ajaxSetup({
                    //     headers: {
                    //         'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    //     }
                    // });

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
@endpush