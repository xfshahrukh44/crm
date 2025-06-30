@extends('v2.layouts.app')

@section('title', 'Invoices')

@section('css')
    <style>
        #zero_configuration_table td {
            word-break: break-all;
            max-width: 300px;
            /* adjust as needed */
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
                                    <h1 style="font-weight: 100;">Invoices</h1>
                                </div>
                                {{--                                        <div class="col-md-6 m-auto d-flex justify-content-end"> --}}
                                {{--                                            <a href="{{route('v2.invoices.create')}}" class="btn btn-sm btn-success"> --}}
                                {{--                                                <i class="fas fa-plus"></i> --}}
                                {{--                                                Create --}}
                                {{--                                            </a> --}}
                                {{--                                        </div> --}}
                            </div>

                            <br>

                            <form class="search-invoice" action="{{ route('v2.invoices') }}" method="GET">
                                <input type="text" name="package" placeholder="Search package"
                                    value="{{ request()->get('package') }}">
                                <input type="text" name="invoice" placeholder="Search Invoice Number"
                                    value="{{ request()->get('invoice') }}">
                                <input type="text" name="customer" placeholder="Search Customer Name"
                                    value="{{ request()->get('customer') }}">
                                <input type="text" name="agent" placeholder="Search Agent Name"
                                    value="{{ request()->get('agent') }}">
                                <select name="brand" id="brand" class="select2">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ request()->get('brand') == $brand->id ? 'selected' : ' ' }}>
                                            {{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <select name="status" id="status">
                                    <option value="0" {{ request()->get('status') == '0' ? 'selected' : '' }}>Status
                                    </option>
                                    <option value="2" {{ request()->get('status') == '2' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="1" {{ request()->get('status') == '1' ? 'selected' : '' }}>Unpaid
                                    </option>
                                </select>
                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search
                                    Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>

                              <div class="responsive-table-lap table-responsive">

                            <table id="zero_configuration_table" class="table table-striped" style="width: 100%;">
                                <thead>

                                    <th>ID</th>
                                    <th>Package Name</th>
                                    <th>User Name</th>
                                    <th>Agent Name</th>
                                    <th>Brand</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->id }}</td>
                                            <td>
                                                <a class="p-2 bg-white bg-transparent" href="{{ route('v2.invoices.show', $invoice->id) }}">
                                                    @if ($invoice->package == 0)
                                                        {{ $invoice->custom_package }}
                                                    @else
                                                        {{ $invoice->package }}
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                                {{ $invoice->client->name }} {{ $invoice->client->last_name }}
                                                <br>
                                                <a href="javascript:void(0);"
                                                    class="badge badge-sm bg-dark p-2 text-white btn_click_to_view">
                                                    <i class="fas fa-eye"></i>
                                                    View email
                                                </a>
                                                <span class="content_click_to_view" hidden>
                                                    {{ $invoice->client->email }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($invoice->sales_agent_id != 0)
                                                    {{ $invoice->sale->name }} {{ $invoice->sale->last_name }}
                                                @else
                                                    From Website
                                                @endif
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-info btn-sm">{{ $invoice->brands->name ?? '' }}</button>
                                            </td>
                                            <td>
                                                <b>${{ round($invoice->amount) }}</b>
                                            </td>
                                            <td>
                                                <span class="">
                                                    @if ($invoice->payment_status == 1)
                                                        <span class="text-danger">Unpaid</span>
                                                    @else
                                                        <span class="text-success">Paid</span>
                                                    @endif

                                                    @if ($invoice->payment_status == 1 && !user_is_cs())
                                                        <form method="post"
                                                            action="{{ route('v2.invoices.paid', $invoice->id) }}">
                                                            @csrf
                                                            <button type="submit" class="badge badge-sm badge-danger p-2"
                                                                style="border: 0px;">Mark As Paid</button>
                                                        </form>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($invoice->created_at)->format('d M y h:i A') }}
                                            </td>

                                            <td style="position: relative;">
                                                <!-- Single Action Button -->
                                                @if($invoice->payment_status == 1 && !(user_is_cs() || !v2_acl([2, 6, 4, 0])))
                                                    <a href="{{ route('v2.invoices.edit', $invoice->id) }}" class="badge bg-primary badge-icon badge-sm text-white p-2">
                                                        <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                        <span class="ul-btn__text">
                                                            <i class="fas fa-pencil"></i>
                                                        </span>
                                                    </a>
                                                @endif

                                                <a href="{{ route('v2.invoices.show', $invoice->id) }}"
                                                    class="badge bg-dark badge-icon badge-sm text-white p-2">
                                                    <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                                    <span class="ul-btn__text">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </a>

                                                {{--                                                            <a href="#" onclick="event.preventDefault(); document.getElementById('invoice_delete_form_{{$invoice->id}}').submit();" class="badge bg-danger badge-icon badge-sm text-white p-2"> --}}
                                                {{--                                                                <i class="fas fa-trash"></i> --}}
                                                {{--                                                            </a> --}}
                                                {{--                                                            <form hidden id="invoice_delete_form_{{$invoice->id}}" method="POST" action="{{route('admin.client.destroy', $invoice->id) }}"> --}}
                                                {{--                                                                {{ method_field('DELETE') }} --}}
                                                {{--                                                                {{ csrf_field() }} --}}
                                                {{--                                                            </form> --}}

                                                {{--                                                            <a href="javascript:void(0);" class="badge bg-warning badge-icon badge-sm p-2 btn_open_notes" id="btn_open_notes_{{$invoice->id}}" --}}
                                                {{--                                                               data-id="{{$invoice->id}}" --}}
                                                {{--                                                               data-content="{{$invoice->comments}}" --}}
                                                {{--                                                               data-modifier-check="{{($invoice->comments !== '' && !is_null($invoice->comments_id) && !is_null($invoice->comments_timestamp)) ? '1' : '0'}}" --}}
                                                {{--                                                               data-modifier="{{($invoice->commenter->name ?? '') . ' ' . ($invoice->commenter->last_name ?? '') . ' ('.\Carbon\Carbon::parse($invoice->comments_timestamp)->format('d M Y h:i A').')'}}"> --}}

                                                {{--                                                                <span class="ul-btn__icon"><i class="fas fa-quote-right"></i></span> --}}
                                                {{--                                                            </a> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                              </div>
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

        $(document).ready(function() {
            $('.btn_click_to_view').on('click', function() {
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
            $('.btn_open_notes').on('click', function() {
                current_rec_id = $(this).data('id');
                current_comment = $(this).data('content');
                $('#textarea_notes').val($(this).data('content'));

                $('#label_modifier_info').html($(this).data('modifier'));
                $('#div_modifier_info').prop('hidden', ($(this).data('modifier-check') == '0'));

                setTimeout(function() {
                    $('#textarea_notes').focus();
                }, 500);

                $('#modal_show_notes').modal('show');
            });

            $('#textarea_notes').on('keyup', function() {
                $('#btn_open_notes_' + current_rec_id).data('content', $(this).val());
            });

            $('#textarea_notes').on('focusout', function() {
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
                    url: "{{ route('v2.clients.update.comments') }}",
                    data: {
                        comments: text_value,
                        rec_id: rec_id,
                    },
                    success: function(data) {
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
        $('.auth-create').on('click', function() {
            var auth = $(this).data('auth');
            var id = $(this).data('id');
            var pass = generatePassword();
            if (auth == 0) {
                swal({
                    title: "Enter Password",
                    input: "text",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Enter Password",
                    inputValue: pass
                }).then(function(inputValue) {
                    if (inputValue === false) {
                        return swal({
                            title: "Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type: "danger"
                        })
                    }
                    if (inputValue === "") {
                        return swal({
                            title: "Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type: "danger"
                        })
                    }
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('v2.clients.create.auth') }}",
                        data: {
                            id: id,
                            pass: inputValue
                        },
                        success: function(data) {
                            if (data.success == true) {
                                swal("Auth Created", "Password is : " + inputValue, "success");
                            } else {
                                return swal({
                                    title: "Error",
                                    text: "There is an Error, Plase Contact Administrator",
                                    type: "danger"
                                })
                            }
                        }
                    });
                });
            } else {
                swal({
                    title: "Enter Password",
                    input: "text",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Enter Password",
                    inputValue: pass
                }).then(function(inputValue) {
                    if (inputValue === false) {
                        return swal({
                            title: "Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type: "danger"
                        })
                    }
                    if (inputValue === "") {
                        return swal({
                            title: "Field cannot be empty",
                            text: "Password Not Inserted/Updated because it is Empty",
                            type: "danger"
                        })
                    }
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('v2.clients.update.auth') }}",
                        data: {
                            id: id,
                            pass: inputValue
                        },
                        success: function(data) {
                            if (data.success == true) {
                                swal("Auth Created", "Password is : " + inputValue, "success");
                            } else {
                                return swal({
                                    title: "Error",
                                    text: "There is an Error, Plase Contact Administrator",
                                    type: "danger"
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
