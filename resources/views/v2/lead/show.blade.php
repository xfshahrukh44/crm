@extends('v2.layouts.app')

@section('title', 'Lead Detail')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brand-detail new-client-detail">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card text-left">
                            <div class="card-body">
                                <h4 class="card-title mb-3">{{$lead->name}} Details</h4>
                                <div class="table-responsive">
                                    <table class="display table table-striped table-bordered" style="width:100%">
                                        <tbody>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{$lead->name}} {{$lead->last_name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{$lead->email}}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{$lead->contact}}</td>
                                        </tr>
                                        <tr>
                                            <th>Brand</th>
                                            <td>{{$lead->_brand->name}}</td>
                                        </tr>
                                        @if($lead->service != null)
                                            <tr>
                                                <th>Service(s)</th>
                                                <td>
                                                    @php
                                                        $service_list = explode(',', $lead->service);
                                                    @endphp
                                                    @for($i = 0; $i < count($service_list); $i++)
                                                        <span class="badge badge-primary badge-pill">{{ $lead->services($service_list[$i])->name }}</span>
                                                    @endfor
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <button class="btn btn-{{lead_status_color_class_map($lead->status)}} btn-sm">
                                                    {{$lead->status}}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>URL</th>
                                            <td>
                                                <a href="{{$lead->url ?? '#'}}">
                                                    {{$lead->url ?? 'N/A'}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Subject</th>
                                            <td>
                                                {{$lead->subject ?? 'N/A'}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Message</th>
                                            <td>
                                                {{$lead->message ?? 'N/A'}}
                                            </td>
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
    </div>

    <!--  Assign Model -->
    <div class="modal fade" id="assignModel" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Assign Agent</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('support.reassign.support') }}" method="post">
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
                <form action="{{ route('assign-pending-project-to-agent') }}" method="post">
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
                            url: "{{ route('admin.client.createauth') }}",
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
                            url: "{{ route('admin.client.updateauth') }}",
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
            });
        });
    </script>
@endsection
