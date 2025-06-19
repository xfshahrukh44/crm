@extends('v2.layouts.app')
@section('title', 'Editing & Proofreading Form')

@section('content')
    <div class="for-slider-main-banner">
        <section class="list-0f">
            <div class="container-fluid">
                <div class="breadcrumb">
                    <h1 class="mr-2">BOOK PRINTING INV#{{$data->invoice->invoice_number}}</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">

                    <form class="col-md-12 brief-form p-0" method="post" action="{{ route('client.bookprinting.form.update', $data->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card mb-4">
                            <div class="card-body mb-4">
                                <div class="card-title mb-3">{{ $data->form_name }}</div>
                                <div class="row">

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> What is the title of the book </label>
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $data->title) }}" required>
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> Do you need the printed copies to be in paperback and hardcover format ? </label>
                                        <input type="text" name="need_the_printed" class="form-control" value="{{ old('need_the_printed', $data->need_the_printed) }}" >
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> How many printed copies do you require? </label>
                                        <input type="text" name="printed_copies" class="form-control" value="{{ old('printed_copies', $data->printed_copies) }}" >
                                    </div>


                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> Which trim size do you want your book to be formatted on? (e.g 5x8 , 5.25x8 5.5x8.5 , 6x9 , 8x10 , 8.5x11 , 8.5x8.5 etc) </label>
                                        <label for="title"> Note: Please Fill The box below for the required trim size of the pages in the book. If you required trim size is not specified please mention the trim size you want in the box below. </label>
                                        <input type="text" name="trim_size_of_the_pages" class="form-control" value="{{ old('trim_size_of_the_pages', $data->trim_size_of_the_pages) }}" >
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> If you have selected other please specify the trim size you want for the printed copies (Optional) </label>
                                        <input type="text" name="trim_size_you_want" class="form-control" value="{{ old('trim_size_you_want', $data->trim_size_you_want) }}" >
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> What is your full name? </label>
                                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $data->full_name) }}" >
                                    </div>

                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> What is your phone number ? </label>
                                        <label for="title"> (We will share this information with the shipping company for contacting your if you needed) </label>
                                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $data->phone_number) }}" >
                                    </div>


                                    <div class="col-md-12 form-group mb-3">
                                        <label for="title"> What is your your complete address? </label>
                                        <label for="title"> (Your printed copies will be delivered on this address so please make sure that the information is accurate) </label>
                                        <input type="text" name="address" class="form-control" value="{{ old('address', $data->address) }}" >
                                    </div>

                                </div>
                            </div>
                        </div>



                    </form>

                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
@endpush
