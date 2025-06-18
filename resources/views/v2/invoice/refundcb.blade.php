@extends('v2.layouts.app')

@section('title', 'Refund/CB')

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
        .client-actions-box {
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
        <section class="list-0f">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-0f-head for-invoice-listing table-responsive">
                            <div class="row text-left pr-3 pb-2">
                                <div class="col-md-12 m-auto d-flex justify-content-start pt-2">
                                    <h1 style="font-weight: 100;">Refund/CB</h1>
                                </div>
                                {{--                                        <div class="col-md-6 m-auto d-flex justify-content-end">--}}
                                {{--                                            <a href="{{route('v2.invoices.create')}}" class="btn btn-sm btn-success">--}}
                                {{--                                                <i class="fas fa-plus"></i>--}}
                                {{--                                                Create--}}
                                {{--                                            </a>--}}
                                {{--                                        </div>--}}
                            </div>

                            <br>

                            <form class="search-invoice" action="{{route('v2.invoices.refund.cb')}}" method="GET">
                                <input type="text" name="invoice_number" placeholder="Search invoice number" value="{{ request()->get('invoice_number') }}">
                                <input step=".01" type="number" id="refunded_cb" class="form-control" placeholder="Refunded CB" name="refunded_cb" value="{{ request()->get('invoice_number') }}">
                                <input type="date" id="refund_cb_date" class="form-control" placeholder="Refunded CB date" name="refund_cb_date" value="{{ request()->get('refund_cb_date') ? \Carbon\Carbon::parse(request()->get('refund_cb_date'))->format('Y-m-d') : '' }}">
                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>

                            <table id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                <th>ID</th>
                                <th>Invoice number</th>
                                <th>Package Name</th>
                                <th>User</th>
                                <th>Agent</th>
                                <th>Brand</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Refund/CB</th>
                                <th>Refund/CB Date</th>
                                <th>Date</th>

                                </thead>
                                <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            {{ $invoice->id }}
                                        </td>
                                        <td>
                                            #{{ $invoice->invoice_number }}
                                        </td>
                                        <td>
                                            @if($invoice->package == 0)
                                                {{ $invoice->custom_package }}
                                            @else
                                                {{ $invoice->package }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $invoice->name }}
                                            <br>
                                            <a href="javascript:void(0);" class="badge badge-sm bg-dark p-2 text-white btn_click_to_view">
                                                <i class="fas fa-eye"></i>
                                                View email
                                            </a>
                                            <span class="content_click_to_view" hidden>
                                                            {{ $invoice->email }}
                                                        </span>
                                        </td>
                                        <td>{{ $invoice->sale->name ?? '' }} {{ $invoice->sale->last_name ?? '' }}</td>
                                        <td>
                                                        <span class="badge badge-primary badge-sm p-2">
                                                            {{ $invoice->brands->name }}
                                                        </span>
                                        </td>
                                        <td>
                                            @php
                                                $service_list = explode(',', $invoice->service);
                                            @endphp
                                            @for($i = 0; $i < count($service_list); $i++)
                                                @if($service_list[$i])
                                                    @php
                                                        $service_list_name = '';
                                                        $var_check = $invoice->services($service_list[$i]);
                                                        $words = $var_check ? explode(" ", $var_check->name) : [];
                                                    @endphp
                                                    @for($j = 0; $j < count($words); $j++)
                                                        @php
                                                            $service_list_name .= $words[$j][0];
                                                        @endphp
                                                    @endfor
                                                    <span class="badge badge-info badge-sm p-2 mb-1">{{ $service_list_name }}</span>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                                        <span class="">
                                                            @if($invoice->payment_status == 1)
                                                                <span class="text-danger">Unpaid</span>
                                                            @else
                                                                <span class="text-success">Paid</span>
                                                            @endif
                                                        </span>
                                        </td>
                                        <td>{{ $invoice->currency_show->sign }}{{ $invoice->amount }}</td>
                                        <td class="text-danger">${{$invoice->refunded_cb}}</td>
                                        <td class="text-danger">{{\Carbon\Carbon::parse($invoice->refund_cb_date)->format('d M y')}}</td>
                                        <td>
                                            {{ date('d M y', strtotime($invoice->created_at)) }}
                                            {{--                                                        <br>--}}
                                            {{--                                                        {{ date('g:i a', strtotime($invoice->created_at)) }}--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $invoices->appends(request()->query())->links() }}
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $('.btn_click_to_view').on('click', function () {
                $('.btn_click_to_view').each((i, item) => {
                    $(item).prop('hidden', false);
                });

                $('.content_click_to_view').each((i, item) => {
                    $(item).prop('hidden', true);
                });

                $(this).prop('hidden', true);
                $(this).parent().find('.content_click_to_view').prop('hidden', false);
            });

            let current_rec_id;
            let current_comment = '';
            $('.btn_open_notes').on('click', function () {
                current_rec_id = $(this).data('id');
                current_comment = $(this).data('content');
                $('#textarea_notes').val($(this).data('content'));

                $('#label_modifier_info').html($(this).data('modifier'));
                $('#div_modifier_info').prop('hidden', ($(this).data('modifier-check') == '0'));

                setTimeout(function () {
                    $('#textarea_notes').focus();
                }, 500);

                $('#modal_show_notes').modal('show');
            });

            $('#textarea_notes').on('keyup', function () {
                $('#btn_open_notes_' + current_rec_id).data('content', $(this).val());
            });

            $('#textarea_notes').on('focusout', function () {
                let text_value = $(this).val();
                if (text_value == current_comment) {
                    return false;
                }

                let rec_id = current_rec_id;

                $('#modal_show_notes').modal('hide');
                $('#div_modifier_info').prop('hidden', true);

                //ajax
                $.ajax({
                    type: 'POST',
                    url: "{{route('v2.clients.update.comments')}}",
                    data: {
                        comments: text_value,
                        rec_id: rec_id,
                    },
                    success:function(data) {
                        toastr.success(data.message);
                    }
                });
            });
        });

        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
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
                    $.ajax({
                        type:'POST',
                        url: "{{ route('v2.clients.create.auth') }}",
                        data: {id: id, pass:inputValue},
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
    </script>

    <script>
        function toggleClientActions(clientId) {
            const box = document.getElementById(`clientActionsBox_${clientId}`);
            document.querySelectorAll('.client-actions-box').forEach(el => {
                if (el !== box) el.classList.add('d-none');
            });
            box.classList.toggle('d-none');
        }

        // Optional: hide on outside click
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.client-actions-box') && !event.target.closest('button')) {
                document.querySelectorAll('.client-actions-box').forEach(el => el.classList.add('d-none'));
            }
        });
    </script>
@endsection
