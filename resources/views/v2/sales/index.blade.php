@extends('v2.layouts.app')

@section('title', 'Sales')

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
                                <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                    <h1 style="font-weight: 100;">Sale Agent</h1>
                                </div>
                                <div class="col-md-6 m-auto d-flex justify-content-end">
                                    <a href="{{ route('v2.users.sales.create') }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i>
                                        Create
                                    </a>
                                </div>
                            </div>

                            <br>

                            {{--                                    <div class="search-invoice"> --}}
                            <form class="search-invoice" action="{{ route('v2.users.sales') }}" method="GET">
                                <input type="text" name="search" placeholder="Search"
                                    value="{{ request()->get('search') }}">

                                <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search
                                    Result</a>
                                <button hidden id="btn_filter_form" type="submit"></button>
                            </form>
                            {{--                                    </div> --}}

                            <table id="zero_configuration_table" style="width: 100%;">
                                <thead>

                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Brand</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Reset Password</th>
                                    @if (v2_acl([2]))
                                        <th>Login</th>
                                        <th>Last login IP</th>
                                        <th>Device info</th>
                                    @endif
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-center">
                                                <img src="{{$user->image ? asset($user->image) : asset('images/avatar.png')}}" style="border-radius: 25px; object-fit: cover;" width="40">
                                                <br />
                                                <span class="badge badge-dark p1">
                                                                <small>{{ $user->id }}</small>
                                                            </span>
                                            </td>
                                            <td>{{ $user->name }} {{ $user->last_name }}</td>
                                            <td>
                                                <button
                                                    class="badge {{ $user->is_employee == 1 ? 'bg-dark' : 'bg-light' }} {{ $user->is_employee == 1 ? 'text-white' : '' }} p-2 btn-sm"
                                                    style="border: 0px;">{{ $user->get_role() }}</button>
                                            </td>
                                            <td style="
                                                display: flex;
                                                align-items: center;
                                                gap: 10px;
                                                flex-wrap: wrap;
                                            ">
                                                @if ($user->brands != null)
                                                    @foreach ($user->brands as $brand)
                                                        <button class="badge bg-info badge-sm text-white p-2"
                                                            style="border: 0px;">{{ $brand->name }}</button>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->status == 1)
                                                    <button class="badge bg-success text-white p-2 badge-sm"
                                                        style="border: 0px;">Active</button>
                                                @else
                                                    <button class="badge bg-danger text-white p-2 badge-sm"
                                                        style="border: 0px;">Deactive</button>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="badge bg-danger badge-sm update-password text-white p-2"
                                                    style="border: 0px;" data-id="{{ $user->id }}">Reset
                                                    Password</button>
                                            </td>
                                            @if (v2_acl([2]))
                                                <td>
                                                    <a class="badge bg-primary badge-sm text-white p-2" style="border: 0px;"
                                                        href="{{ route('v2.admin.login_bypass', ['email' => $user->email]) }}">Login
                                                        as {{ $user->name }} {{ $user->last_name }}</a>
                                                </td>
                                                <td>{{ $user->last_login_ip ?? '' }}</td>
                                                <td>
                                                    @php
                                                        $device = '';
                                                    @endphp
                                                    @if (!is_null($user->last_login_device))
                                                        @php
                                                            $device = explode(' (', $user->last_login_device)[1] ?? '';
                                                            $device = explode(') ', $device)[0] ?? '';
                                                        @endphp
                                                    @endif
                                                    {{ $device }}
                                                </td>
                                            @endif
                                            <td style="position: relative;">
                                                <a href="{{ route('v2.users.sales.edit', $user->id) }}"
                                                    class="badge bg-primary badge-icon badge-sm text-white p-2">
                                                    <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                    <span class="ul-btn__text">
                                                        <i class="fas fa-pencil"></i>
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('update.user.update.password') }}" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="" id="user_id">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel1">Update Status?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <label for="password">Password <span>*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Button on right" id="password"
                                        name="password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button"
                                            onclick="copyToClipboard()">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
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

        function copyToClipboard() {
            var copyText = document.getElementById("password");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            M.toast({
                html: 'Password Copied'
            })
            // alert("Copied the text: " + copyText.value);
        }
        $(document).ready(function() {
            $('body').on('click', '.update-password', function() {
                var id = $(this).data('id');
                $('#user_id').val(id);
                // $('#default').modal('toggle');
                $('#default').modal('show');
                $('#password').val(generatePassword());
            });
        });
    </script>
@endsection
