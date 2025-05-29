@extends('v2.layouts.app')

@section('title', 'Leads')

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
        .lead-actions-box {
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
        @switch($user_role_id)
            @case(2)
                <section class="list-0f">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing table-responsive">
                                    <div class="row text-left pr-3 pb-2">
                                        <div class="col-md-6 m-auto d-flex justify-content-start pt-2">
                                            <h1 style="font-weight: 100;">Leads</h1>
                                        </div>
                                        <div class="col-md-6 m-auto d-flex justify-content-end">
                                            <a href="{{route('v2.leads.create')}}" class="btn btn-sm btn-success">
                                                <i class="fas fa-plus"></i>
                                                Create
                                            </a>
                                        </div>
                                    </div>

                                    <br>

{{--                                    <div class="search-invoice">--}}
                                    <form class="search-invoice" action="{{route('v2.leads')}}" method="GET">
                                        <input type="text" name="name" placeholder="Search name" value="{{ request()->get('name') }}">
                                        <input type="text" name="email" placeholder="Search email" value="{{ request()->get('email') }}">
                                        <select name="brand" class="select2">
                                            <option value="">Select brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                            @endforeach
                                        </select>
                                        <select name="status">
                                            <option value="">Select status</option>
                                            <option value="Closed" {{ request()->get('status') ==  "Closed" ? 'selected' : ' '}}>Closed</option>
                                            <option value="On Discussion" {{ request()->get('status') == "On Discussion" ? 'selected' : ' '}}>On Discussion</option>
                                            <option value="Onboarded" {{ request()->get('status') ==  "Onboarded" ? 'selected' : ' '}}>Onboarded</option>
                                        </select>

                                        <a href="javascript:;" onclick="document.getElementById('btn_filter_form').click()">Search Result</a>
                                        <button hidden id="btn_filter_form" type="submit"></button>
                                    </form>
{{--                                    </div>--}}

                                    <table id="zero_configuration_table" style="width: 100%;">
                                        <thead>

                                            <th>ID</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Brand</th>
                                            <th>Service(s)</th>
                                            <th>Status</th>
                                            <th>Assigned to</th>
                                            <th>Action</th>

                                        </thead>
                                        <tbody>
                                            @foreach($leads as $lead)
                                                <tr>
                                                    <td>{{$lead->id}}</td>
                                                    <td><a href="{{ route('v2.leads.show', $lead->id) }}">{{$lead->name}} {{$lead->last_name}}</a></td>
                                                    <td>{{$lead->email}}</td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm">{{$lead->_brand->name ?? ''}}</button>
                                                    </td>

                                                    <td>
                                                        @php
                                                            $service_list = explode(',', $lead->service);
                                                        @endphp
                                                        @for($i = 0; $i < count($service_list); $i++)
                                                            <span class="badge badge-primary badge-pill">{{ $lead->services($service_list[$i])->name }}</span>
                                                        @endfor
                                                    </td>

                                                    <td>
                                                        <button class="btn btn-{{lead_status_color_class_map($lead->status)}} btn-sm">
                                                            {{$lead->status}}
                                                        </button>
                                                    </td>
                                                    <td>
                                                        {{ ($lead->assigned_to->name ?? '') . ' ' . ($lead->assigned_to->last_name ?? '') }}
                                                    </td>
                                                    <td style="position: relative;">
                                                        <!-- Single Action Button -->
                                                        <button type="button" class="badge badge-sm bg-light p-2" style="border: 0px;" onclick="toggleLeadActions({{ $lead->id }})">
                                                            <i class="fas fa-bars"></i>
                                                        </button>

                                                        <!-- Hidden Popup Box -->
                                                        <div id="leadActionsBox_{{ $lead->id }}" class="lead-actions-box text-center d-none">
                                                            <a href="{{ route('v2.leads.edit', $lead->id) }}" class="badge bg-primary badge-icon badge-sm text-white p-2">
                                                                <span class="ul-btn__icon"><i class="i-Edit"></i></span>
                                                                <span class="ul-btn__text">
                                                                <i class="fas fa-pencil"></i>
                                                            </span>
                                                            </a>
                                                            <a href="{{ route('v2.leads.show', $lead->id) }}" class="badge bg-dark badge-icon badge-sm text-white p-2">
                                                                <span class="ul-btn__icon"><i class="i-Eyeglasses-Smiley"></i></span>
                                                                <span class="ul-btn__text">
                                                                <i class="fas fa-eye"></i>
                                                            </span>
                                                            </a>

                                                            <a href="#" onclick="event.preventDefault(); document.getElementById('lead_delete_form_{{$lead->id}}').submit();" class="badge bg-danger badge-icon badge-sm text-white p-2">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            <form hidden id="lead_delete_form_{{$lead->id}}" method="POST" action="{{route('admin.lead.destroy', $lead->id) }}">
                                                                {{ method_field('DELETE') }}
                                                                {{ csrf_field() }}
                                                            </form>

                                                            <a href="javascript:void(0);" class="badge bg-warning badge-icon badge-sm p-2 btn_open_notes" id="btn_open_notes_{{$lead->id}}"
                                                               data-id="{{$lead->id}}"
                                                               data-content="{{$lead->comments}}"
                                                               data-modifier-check="{{($lead->comments !== '' && !is_null($lead->comments_id) && !is_null($lead->comments_timestamp)) ? '1' : '0'}}"
                                                               data-modifier="{{($lead->commenter->name ?? '') . ' ' . ($lead->commenter->last_name ?? '') . ' ('.\Carbon\Carbon::parse($lead->comments_timestamp)->format('d M Y h:i A').')'}}">

                                                                <span class="ul-btn__icon"><i class="fas fa-quote-right"></i></span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end mt-2">
                                        {{ $leads->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @break

            @default
                <section class="list-0f">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-0f-head for-invoice-listing">
                                    <table>
                                        <thead>

                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Public key</th>
                                        <th>Secret key</th>
                                        <th>Merchant</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td>123</td>
                                            <td>Merchant 1</td>
                                            <td>
                                                sadasdsadasdsadakjdyihd18272bd871bd82b
                                            </td>
                                            <td>
                                                sadasdsadasdsadakjdyihd18272bd871bd82b
                                            </td>
                                            <td>
                                                <div class="badge badge-sm bg-secondary text-white">STRIPE</div>
                                            </td>
                                            <td>
                                                <div class="badge badge-sm bg-danger text-white">Deactive</div>
                                            </td>
                                            <td>
                                                <a href="#" class="badge bg-primary">
                                                    <i class="fas fa-pencil"></i>
                                                </a>
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

        <!-- Notes Modal -->
        <div class="modal fade" id="modal_show_notes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Notes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <textarea class="form-control" name="" id="textarea_notes" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row" id="div_modifier_info" hidden>
                            <div class="col-md-12">
                                <label for="">
                                    <b>Last updated by:</b>
                                </label>
                            </div>
                            <div class="col-md-12">
                                <label for="" id="label_modifier_info">
                                    asd asd (12-12-12 12:12 AM)
                                </label>
                            </div>
                        </div>
                    </div>
                    {{--            <div class="modal-footer">--}}
                    {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    {{--            </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function toggleLeadActions(leadId) {
            const box = document.getElementById(`leadActionsBox_${leadId}`);
            document.querySelectorAll('.lead-actions-box').forEach(el => {
                if (el !== box) el.classList.add('d-none');
            });
            box.classList.toggle('d-none');
        }

        // Optional: hide on outside click
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.lead-actions-box') && !event.target.closest('button')) {
                document.querySelectorAll('.lead-actions-box').forEach(el => el.classList.add('d-none'));
            }
        });
    </script>
@endsection
