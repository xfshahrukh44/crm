@extends('v2.layouts.app')

@section('title', 'Client Detail')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brand-detail new-client-detail">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="brand-dela">
                            <div class="up-sell-img">
                                <img src="{{asset('/images/avatar.png')}}" class="img-fluid">
                                <div class="up-sell-content">
                                    <h2>{{$client->name . ' ' . $client->last_name}}</h2>
                                    <p>{{$client->brand?->name}}</p>
                                </div>
                            </div>

                            <div class="brand-email">
                                <div class="email">
                                    <a href="tel:{{$client->contact}}">
                                        <i class="fa-solid fa-phone"></i>
                                        Contact
{{--                                        {{$client->contact}}--}}
                                    </a>
                                </div>
                                <div class="email">
                                    <a href="mailto:{{$client->email}}">
                                        <i class="fa-solid fa-envelope"></i>
                                        Send E-mail
{{--                                        {{$client->email}}--}}
                                    </a>
                                </div>
                                <div class="for-password">
                                    @if($client->user)
                                        <a href="{{ route('v2.messages') }}?clientId={{ $client->user->id }}" class="open-messages">
                                            <img src="{{asset('v2/images/img-msg.jpg')}}" class="img-fluid">
                                        </a>
                                        {{--                                            <a href="javascript:;" class="password-btn">Reset Password</a>--}}
                                        <a href="javascript:;" class="password-btn badge bg-{{ $client->user ? 'success' : 'danger' }} badge-sm auth-create"
                                           data-id="{{ $client->id }}"
                                           data-auth="{{ $client->user ? 1 : 0 }}"
                                           data-password="{{ $client->user ? '' : '' }}">
                                            {{ $client->user ? 'Reset Password' : 'Create Account' }}
                                        </a>
                                    @endif

                                    @if(!user_is_cs())
                                        <a href="{{route('v2.invoices.create', $client->id)}}" class="password-btn blue-assign">Generate Payment</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if(count($invoices) && !user_is_cs())
            <section class="list-0f mt-4">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-0f-head">
                                <h2>Invoices</h2>

                                <table>
                                    <tbody>
                                    <tr>
                                        <th>ID.</th>
                                        <th>Service </th>
                                        <th>Amount </th>
                                        <th>Agent </th>
                                        <th>Status</th>
                                        <th>Link</th>
                                        <th>Created at</th>
                                    </tr>

                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <a class="p-2 bg-white text-dark" href="{{ route('v2.invoices.show', $invoice->id) }}">
                                                    <strong>{{$invoice->id}}</strong>
                                                </a>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                @php
                                                    $service_list = explode(',', $invoice->service);
                                                @endphp
                                                @for($i = 0; $i < count($service_list); $i++)
                                                    @php
                                                        $record = $invoice->services($service_list[$i]);
                                                    @endphp
                                                    @if($record)
                                                        <span class="badge badge-pill badge-primary p-2 mr-1">{{ $invoice->services($service_list[$i])->name }}</span>
                                                    @endif
                                                @endfor
                                            </td>
                                            <td>
                                                <b>{{$invoice->currency_show->sign}}</b>
                                                {{$invoice->amount}}
                                            </td>
                                            <td>
                                                @if ($invoice->sales_agent_id != 0)
                                                    {{ $invoice->sale->name }} {{ $invoice->sale->last_name }}
                                                @else
                                                    From Website
                                                @endif
                                            </td>
                                            @if($invoice->payment_status == 2)
                                                <td class="green">
                                                    Paid
                                                </td>
                                            @else
                                                <td class="red">
                                                    Unpaid
                                                    <form method="post" action="{{route('v2.invoices.paid', $invoice->id)}}">
                                                        @csrf
                                                        <button type="submit" class="badge badge-sm badge-danger p-2" style="border: 0px;">Mark As Paid</button>
                                                    </form>
                                                </td>
                                            @endif
                                            <td>
                                                @if($invoice->is_authorize == 1)
                                                    <a href="javascript:void(0);" class="badge badge-sm badge-warning p-2 btn_copy_authorize_link" data-url="{{route('client.pay.with.authorize', $invoice->id)}}">
                                                        <i class="fas fa-copy"></i>
                                                        Payment link
                                                    </a>
                                                @else
                                                    <b>N/A</b>
                                                @endif
                                            </td>
                                            <td>
                                                {{Carbon\Carbon::parse($invoice->created_at)->format('d F Y')}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if(count($projects))
            <section class="list-0f">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-0f-head">
                                <h2>Services</h2>
                                <table>
                                    <tbody>
                                    <tr>
                                        <th>ID.</th>
                                        <th>Service</th>
                                        <th>Assigned To </th>
                                        <th>Status</th>
                                        <th>Created at</th>
                                        <th>Actions</th>

                                    </tr>

                                    @foreach($projects as $project)
                                        @php
                                            $active_tasks = \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)
                                                                ->when(auth()->user()->is_employee != 2, function ($q) {
                                                                    return $q->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list());
                                                                })
                                                                ->count();
                                            $department_count = count(array_unique(
                                                    \App\Models\Task::where('project_id', $project->id)->where('status', '!=', 3)
                                                        ->when(auth()->user()->is_employee != 2, function ($q) {
                                                            return $q->whereIn('brand_id', \Illuminate\Support\Facades\Auth::user()->brand_list());
                                                        })
                                                        ->get()->pluck('category_id')->toArray()
                                                )) ?? 0;
                                            $no_pending_tasks_left = no_pending_tasks_left($project->id);
                                        @endphp
                                        <tr>
                                            <td>
                                                <a class="p-2 bg-white text-dark" href="{{$no_pending_tasks_left ? 'javascript:void(0);' : (route('v2.tasks') . '?project_id=' . $project->id)}}">
                                                    <strong>{{$project->id}}</strong>
                                                </a>
                                            </td>

                                            <td>{{str_replace($client->name, '', str_replace(' - ', ' ', $project->name))}}</td>
                                            <td>{{$project->added_by->name . ' ' . $project->added_by->last_name}}</td>
                                            <td>
                                                @if($no_pending_tasks_left)
                                                    <span class="badge badge-success">No pending tasks</span>
                                                @endif

                                                @if($department_count > 0)
                                                    <small class="text-muted mt-2 mb-2">{{$active_tasks}} active task(s) in {{$department_count}} department(s)</small>
                                                @endif
                                            </td>
                                            <td>{{Carbon\Carbon::parse($project->created_at)->format('d F Y')}}</td>

                                            <td>
                                                <div style="flex-wrap: wrap;">
                                                    @if(!user_is_cs())
                                                        <a href="javascript:void(0);" class="for-assign btn_assign_project mx-2 px-3"
                                                           data-id="{{$project->id}}"
                                                           data-form="{{$project->form_checker}}"
                                                           data-brand="{{$project->brand_id}}"
                                                        >
                                                            Re Assign
                                                        </a>
                                                    @endif
                                                    @if($project->form_checker != 0)
                                                        <a href="{{route('v2.projects.view.form', [ 'form_id' => $project->form_id , 'check' => $project->form_checker])}}" class="for-assign blue-assign mx-2 px-3">
                                                            View Form
                                                        </a>
                                                    @endif
                                                    <a href="{{route('v2.tasks.create', $project->id)}}" class="for-assign dark-blue-assign mx-2 px-3">Create Task</a>
                                                    @if(!$no_pending_tasks_left)
                                                        <a href="{{route('v2.tasks') . '?project_id=' . $project->id}}" class="for-assign bg-warning mx-2 px-3">View tasks</a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if(count($pending_projects) && !user_is_cs())
            <section class="list-0f">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-0f-head">
                                <h2>Pending projects</h2>


                                <table>
                                    <tbody>
                                    <tr>
                                        <th>Brief Pending</th>
                                        <th>Invoice ID</th>
                                        <th>Actions</th>
                                    </tr>

                                    @foreach($pending_projects as $pending_project)
                                        <tr>
                                            <td>
                                                {{$pending_project['project_type']}}
                                            </td>
                                            <td>
                                                        <span class="badge badge-dark badge-sm">
                                                            #{{$pending_project['invoice_id']}}
                                                        </span>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="for-assign mx-2 px-3" onclick="assignAgentToPending({{$pending_project['id']}}, {{$pending_project['form_number']}}, {{$pending_project['brand_id']}})">
                                                    Assign
                                                </a>
                                                <a href="{{route('v2.projects.view.form', [ 'form_id' => $pending_project['id'] , 'check' => $pending_project['form_number'] ])}}" class="for-assign blue-assign mx-2 px-3">
                                                    View Form
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if(count($briefs_pendings) && !user_is_cs())
            <section class="list-0f">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-0f-head">
                                <h2>Briefs pending</h2>

                                <div class="row m-auto">
                                    @foreach($briefs_pendings as $brief_pending)
                                        <span class="badge badge-pill badge-primary p-2 my-1 mr-2">{{$brief_pending}}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>


    @if(!user_is_cs())
        <!--  Assign Model -->
        <div class="modal fade" id="assignModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('v2.projects.reassign') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="assign_id">
                            <input type="hidden" name="form" id="form_id">
                            <div class="form-group">
                                <label class="col-form-label" for="agent-name-wrapper">Name:</label>
                                <select name="agent_id" id="agent-name-wrapper" class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary ml-2" type="submit">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--  Assign Pending Model -->
        <div class="modal fade" id="assignPendingModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('v2.projects.assign') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="pending_assign_id">
                            <input type="hidden" name="form" id="pending_form_id">
                            <div class="form-group">
                                <label class="col-form-label" for="agent-name-wrapper">Name:</label>
                                <select name="agent_id" id="agent-name-wrapper-2" class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary ml-2" type="submit">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        function copy_authorize_link (url) {
            // Create a temporary textarea to hold the link
            var tempInput = document.createElement("textarea");
            tempInput.value = url; // Assign the link to the textarea
            document.body.appendChild(tempInput); // Append textarea to body (temporarily)

            tempInput.select(); // Select the text
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy"); // Copy to clipboard

            document.body.removeChild(tempInput); // Remove the temporary textarea

            toastr.success('Link copied to clipboard!');
        }
        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }

        function assignAgentToPending(id, form, brand_id){
            $('#agent-name-wrapper-2').html('');
            var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
            url = url.replace('temp', brand_id);
            $.ajax({
                type:'GET',
                url: url,
                success:function(data) {
                    var getData = data.data;
                    for (var i = 0; i < getData.length; i++) {
                        $('#agent-name-wrapper-2').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                    }

                    $('#agent-name-wrapper-2').select2();
                }
            });

            $('#assignPendingModel').find('#pending_assign_id').val(id);
            $('#assignPendingModel').find('#pending_form_id').val(form);
            $('#assignPendingModel').modal('show');
        }

        $(document).ready(() => {

            function assignAgent(id, form, brand_id){
                $('#agent-name-wrapper').html('');
                var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
                url = url.replace('temp', brand_id);
                $.ajax({
                    type:'GET',
                    url: url,
                    success:function(data) {
                        var getData = data.data;
                        for (var i = 0; i < getData.length; i++) {
                            $('#agent-name-wrapper').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                        }

                        $('#agent-name-wrapper').select2();
                    }
                });
                $('#assignModel').find('#assign_id').val(id);
                $('#assignModel').find('#form_id').val(form);
                $('#assignModel').modal('show');
            }

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
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type:'POST',
                            url: "{{ route('v2.clients.create.auth') }}",
                            data: {id: id, pass:inputValue},
                            success:function(data) {
                                if(data.success == true){
                                    swal("Auth Created", "Password is : " + inputValue, "success");

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
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
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            type:'POST',
                            url: "{{ route('v2.clients.update.auth') }}",
                            data: {id: id, pass:inputValue},
                            success:function(data) {
                                if(data.success == true){
                                    swal("Auth Created", "Password is : " + inputValue, "success");

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
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

            $('.btn_assign_project').on('click', function () {
                assignAgent($(this).data('id'), $(this).data('form'), $(this).data('brand'));
            });

            //copy link
            $('.btn_copy_authorize_link').on('click', function () {
                copy_authorize_link($(this).data('url'));

                $(this).html(`<i class="fas fa-check"></i> Copied!`);
                $(this).removeClass('bg-warning');
                $(this).addClass('bg-success');
                $(this).removeClass('text-dark');
                $(this).addClass('text-white');

                let el = $(this);
                setTimeout(function () {
                    el.html(`<i class="fas fa-copy"></i> Payment link`);
                    el.addClass('bg-warning');
                    el.removeClass('bg-success');
                    el.addClass('text-dark');
                    el.removeClass('text-white');
                }, 3000);
            });
        });
    </script>
@endsection
