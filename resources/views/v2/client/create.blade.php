@extends('v2.layouts.app')

@section('title', 'Create lead')

@section('css')

@endsection

@section('content')
    <div class="for-slider-main-banner">
        @switch($user_role_id)
            @case(2)
                <section class="brief-pg">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="brief-info">
                                    <h2 class="mt-4">Lead Form</h2>
                                    <form action="{{route('v2.leads.store')}}" method="POST">
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
                                                    <select class="form-control select2" name="brand" id="brand">
                                                        <option value="">Select brand</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{$brand->id}}" {{ request()->get('brand') ==  $brand->id ? 'selected' : ' '}}>{{$brand->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Service *</label>
                                                    <select class="form-control select2" name="service[]" id="service" required multiple>
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}" {!! (in_array($service->id, (old('service') ?? []))) ? 'selected' : '' !!}>{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('service')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Select status *</label>
                                                    <select name="status" class="form-control">
                                                        <option value="Closed">Closed</option>
                                                        <option value="On Discussion">On Discussion</option>
                                                        {{--                                    <option value="Onboarded">Onboarded</option>--}}
                                                    </select>
                                                    @error('status')
                                                    <label class="text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Assign to *</label>
                                                    <select name="user_id" class="form-control">
                                                        @foreach($front_agents as $front_agent)
                                                            <option value="{{ $front_agent->id }}">{{ $front_agent->name . ' ' . $front_agent->last_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('user_id')
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

                @break

            @default
                <section class="brief-pg">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="brief-info">
                                    <h4>Brief Pending Info</h4>
                                    <h2>Book Formatting & Publishing Brief Form</h2>
                                    <form action="">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>What is the title of the book? *</label>
                                                    <input type="text" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>What is the subtitle of the book?</label>
                                                    <input type="text" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Any additional contributors you would like to acknowledge? (e.g. Book
                                                        Illustrator, Editor, etc.) *</label>
                                                    <textarea id="textarea" rows="7" class="form-control"
                                                              required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="fromatting-">
                                                    <h2>Formatting Requirements</h2>
                                                    <div class="formate-select">
                                                        <label>Where do you want to? *</label>
                                                        <ul>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Amazon KDP</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/barnes.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Barnes & Noble</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/google-book.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Google Books</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/google-book2.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Google Books</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/spark.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>Ingram Spark</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="formate-select">
                                                        <label>Which formats do you want your book to be formatted on? *</label>
                                                        <ul>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>e Book</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>e Book</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="selection-formate">
                                                                    <img src="images/kdp.png" class="img-fluid" alt="">
                                                                    <div class="kdp-formate">
                                                                        <input type="radio" id="">
                                                                        <label>e Book</label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
        @endswitch
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
                    <a href="#" type="button" class="btn btn-primary" id="btn_edit_external_lead">Edit Lead</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(() => {

        });
    </script>
@endsection
