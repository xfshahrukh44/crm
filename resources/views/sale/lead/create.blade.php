@extends('layouts.app-sale')

@section('content')

<div class="breadcrumb">
    <h1>Create Lead</h1>
    <ul>
        <li><a href="#">Leads</a></li>
        <li>Create Lead</li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Lead Form</div>
                <form class="form" action="{{route('sale.lead.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
{{--                    @if(request()->has('brand_id'))--}}
{{--                        <input type="hidden" name="redirect_to_client_detail" value="1">--}}
{{--                    @endif--}}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="name">First Name <span>*</span></label>
                                <input type="text" id="name" class="form-control" value="{{old('name')}}" placeholder="First Name" name="name" required="required">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="last_name">Last Name <span>*</span></label>
                                <input type="text" id="last_name" class="form-control" value="{{old('last_name')}}" placeholder="Last Name" name="last_name" required="required">
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="email">Email <span>*</span></label>
                                <input type="email" id="email" class="form-control" value="{{old('email')}}" placeholder="Email" name="email" required="required">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="contact">Contact</label>
                                <input type="text" id="contact" class="form-control" value="{{old('contact')}}" placeholder="Contact" name="contact">
                                @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="brand">Brand<span>*</span></label>
                                <select name="brand" id="brand" class="form-control select2" required>
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {!! request()->has('brand') && request()->get('brand') == $brand->id ? 'selected' : '' !!}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="service">Service <span>*</span></label>
                                <select name="service[]" id="service" class="form-control select2" multiple required>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="status">Select Status <span>*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Closed">Closed</option>
                                    <option value="On Discussion">On Discussion</option>
                                    <option value="Onboarded">Onboarded</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit">Save Lead</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- External Lead Modal -->
<div class="modal fade" id="externalLeadModal" tabindex="-1" role="dialog" aria-labelledby="externalLeadModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Warning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Lead already exists.
            </div>
            <div class="modal-footer">
{{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                <a href="#" type="button" class="btn btn-primary" id="btn_edit_external_client">Edit Lead</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        function check_if_external_client () {
            if ($('#email').val() == '' || $('#brand').val() == '') {
                return false;
            }

            $.ajax({
                url: '{{route("check-if-external-client")}}',
                method: 'GET',
                data: {
                    email: $('#email').val(),
                    brand_id: $('#brand').val(),
                },
                success: (data) => {
                    if (data == '') {
                        return false;
                    } else {
                        $('#btn_edit_external_client').prop('href', data);
                        $('#externalLeadModal').modal('show');
                    }
                },
                error: (e) => {
                    alert(e);
                },
            });
        }

        $(document).ready(() => {
            $('#email').on('change', () => {
                check_if_external_client();
            });

            $('#brand').on('change', () => {
                check_if_external_client();
            });
        });
    </script>
    <script>
{{--        @if(session()->has('success'))--}}
{{--            toastr.success('{{session()->get('success')}}');--}}
{{--        @endif--}}
{{--        @if(session()->has('error'))--}}
{{--            toastr.error('{{session()->get('error')}}');--}}
{{--        @endif--}}
        $(document).ready(function(){
            $('#password').val(generatePassword());
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
        $('#contact').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
                event.preventDefault(); //stop character from entering input
            }
        });
    </script>
@endpush
