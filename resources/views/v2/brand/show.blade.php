@extends('v2.layouts.app')

@section('title', 'Brand Detail')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="brand-detail new-client-detail">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card text-left">
                                    <div class="card-body">
                                        <h4 class="card-title mb-3">{{$brand->name}} Details</h4>
                                        <div class="table-responsive">
                                            <table class="display table table-striped table-bordered" style="width:100%">
                                                <tbody>
                                                    <tr>
                                                        <th>Image</th>
                                                        <td><img src="{{ asset($brand->logo)}}" width="100"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Name</th>
                                                        <td>{{$brand->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>URL</th>
                                                        <td>{{$brand->url}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>{{$brand->status}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Phone</th>
                                                        <td>{{$brand->phone}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Phone Tel</th>
                                                        <td>{{$brand->phone_tel}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>{{$brand->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Address</th>
                                                        <td>{{$brand->address}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Address Link</th>
                                                        <td>{{$brand->address_link}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="brand-detail new-brand-detail">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="brand-dela">
                                    <div class="up-sell-img">
                                        <img src="{{asset('v2/images/bu-head1.png')}}" class="img-fluid">
                                        <div class="up-sell-content">
                                            <h2>Mackendy Sterling</h2>
                                            <p>The Designs Craft</p>
                                        </div>
                                    </div>

                                    <div class="brand-email">
                                        <div class="email">
                                            <a href="javascript:;">
                                                <i class="fa-solid fa-phone"></i> 13159901109
                                            </a>
                                        </div>
                                        <div class="email">
                                            <a href="javascript:;">
                                                <i class="fa-solid fa-envelope"></i>  info@designsventure.com
                                            </a>
                                        </div>
                                        <div class="for-password">
                                            <a href="javascript:;">
                                                <img src="{{asset('v2/images/img-msg.jpg')}}" class="img-fluid">
                                            </a>
                                            <a href="javascript:;" class="password-btn">Reset Password</a>
                                            <a href="javascript:;" class="password-btn blue-assign">Generate Payment</a>
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
                                    <h2>Invoices</h2>


                                    <table>
                                        <tbody>
                                        <tr>
                                            <th>ID.</th>
                                            <th>Package</th>
                                            <th>Service </th>
                                            <th>Amount </th>
                                            <th>Status</th>
                                            <th>Created at</th>

                                            <th></th>

                                        </tr>

                                        <tr>
                                            <td>387</td>
                                            <td>3 logo Concepts</td>

                                            <td>Logo Design</td>
                                            <td>$987</td>
                                            <td class="green">Paid</td>
                                            <td>24 January, 2025 </td>

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
                                            <td>452</td>
                                            <td>Mobile Software Development</td>

                                            <td>Mobile Application</td>
                                            <td>$15,000</td>
                                            <td class="red">Unpaid</td>
                                            <td>24 January, 2025 </td>
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
                                            <td>999</td>
                                            <td>3 logo Concepts</td>

                                            <td>Logo Design</td>
                                            <td>$1,200</td>
                                            <td class="green">Paid</td>
                                            <td>24 January, 2025 </td>
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
                                            <td>537</td>
                                            <td>Book Publishing</td>

                                            <td>Book Publishing</td>
                                            <td>$488</td>
                                            <td class="red">Unpaid</td>
                                            <td>24 January, 2025 </td>
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

                                            <th></th>

                                        </tr>

                                        <tr>
                                            <td>387</td>

                                            <td>Logo Design</td>
                                            <td>Allen Mathews</td>
                                            <td>In Progress</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign">Re Assign</a></td>

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
                                            <td>452</td>
                                            <td>Mobile Application</td>
                                            <td>Allen Mathews</td>
                                            <td>Send For Approval</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign dark-blue-assign">Create Task</a></td>
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
                                            <td>999</td>
                                            <td>Logo Design</td>
                                            <td>Allen Mathews</td>
                                            <td>Pending Task</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign blue-assign">View Form</a></td>
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
                                            <td>537</td>
                                            <td>Book Publishing</td>
                                            <td>Allen Mathews</td>
                                            <td>Completed</td>
                                            <td>24 January, 2025 </td>
                                            <td><a href="javascript:;" class="for-assign">Re Assign</a></td>
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

@section('script')
    <script>
        $(document).ready(() => {

        });
    </script>
@endsection
