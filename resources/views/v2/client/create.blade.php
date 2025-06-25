@extends('v2.layouts.app')

@section('title', 'Create client')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        <section class="brief-pg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brief-info">
                            <h2 class="mt-4">Client Form</h2>
                            <form action="{{route('v2.clients.store')}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>First name *</label>
                                            <input type="text" class="form-control" name="name" value="{{old('name') ?? ''}}" required>
                                            @error('name')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Last name *</label>
                                            <input type="text" class="form-control" name="last_name" value="{{old('last_name') ?? ''}}" required>
                                            @error('last_name')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" name="email" id="email" value="{{old('email') ?? ''}}" required>
                                            @error('email')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Contact</label>
                                            <input type="text" class="form-control" name="contact" value="{{old('contact') ?? ''}}" required>
                                            @error('contact')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Brand *</label>
                                            <select class="form-control select2" name="brand_id" id="brand_id">
                                                <option value="">Select brand</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Select status *</label>
                                            <select name="status" class="form-control">
                                                <option value="1">Active</option>
                                                <option value="0" selected="">Deactive</option>
                                            </select>
                                            @error('status')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Select priority *</label>
                                            <select name="priority" class="form-control">
                                                <option value="1" class="bg-danger text-white">HIGH</option>
                                                <option value="2" class="bg-warning text-black" selected="">MEDIUM</option>
                                                <option value="3" class="bg-info text-white">LOW</option>
                                            </select>
                                            @error('priority')
                                            <label class="text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-auto">
                                    <div class="brief-bttn">
                                        <button class="btn brief-btn" type="submit">Submit Form</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <!-- External Client Modal -->
    <div class="modal fade" id="externalClientModal" tabindex="-1" role="dialog" aria-labelledby="externalClientModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Warning</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Client already exists.
                </div>
                <div class="modal-footer">
                    {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    <a href="#" type="button" class="btn btn-primary" id="btn_edit_external_client">Edit Client</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function check_if_external_client () {
            if ($('#email').val() == '' || $('#brand_id').val() == '') {
                return false;
            }

            $.ajax({
                url: '{{route("v2.clients.check.external")}}',
                method: 'GET',
                data: {
                    email: $('#email').val(),
                    brand_id: $('#brand_id').val(),
                    v2: true,
                },
                success: (data) => {
                    if (data == '') {
                        return false;
                    } else {
                        let url = '{{route("v2.clients.edit", "temp")}}'
                        url = url.replace('temp', data);
                        $('#btn_edit_external_client').prop('href', url);
                        $('#externalClientModal').modal('show');
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

            $('#brand_id').on('change', () => {
                check_if_external_client();
            });
        });
    </script>
@endsection
