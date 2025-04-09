@extends('layouts.app-manager')
@section('title', 'Book Marketing Form')

@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Book Marketing Brief INV#{{$book_marketing_form->invoice->invoice_number}}</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <form class="col-md-12 brief-form" method="post" action="#" enctype="multipart/form-data">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Book Marketing Brief Form</div>
                <div class="row">
                    <div class="col-md-3 form-group mb-3">
                        <label for="client_name">Client name</label>
                        <input class="form-control" name="client_name" id="client_name" type="text" value="{{ old('client_name', $book_marketing_form->client_name) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="project_name">Project name</label>
                        <input class="form-control" name="project_name" id="project_name" type="text" value="{{ old('project_name', $book_marketing_form->project_name) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="package_chosen">Package Chosen/Amount Paid</label>
                        <input class="form-control" name="package_chosen" id="package_chosen" type="text" value="{{ old('package_chosen', $book_marketing_form->package_chosen) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="project_duration">Project Duration</label>
                        <input class="form-control" name="project_duration" id="project_duration" type="text" value="{{ old('project_duration', $book_marketing_form->project_duration) }}" required/>
                    </div>
                    <div class="col-md-12">
                        <p>
                            <b>
                                The Following information is required for documentation & verification purposes only.
                                Please make sure you share information about your business in form of a brochure, website
                                or any other document along with the source files of your branding as it would make it easier
                                for us to source and create content for your social media platforms.
                            </b>
                        </p>
                    </div>
                    <div class="col-md-12">
                        <h3>Project Objective</h3>
                    </div>
                    <div class="col-md-12">
                        <p>
                            <b>
                                What are the desired results that you want to get generated from this Social Media project?
                            </b>
                        </p>
                        <p>
                            <b>
                                Check one or more of the following:
                            </b>
                        </p>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="desired_results" class="mr-2">Increase in Page Likes/Followers</label>
                        <input class="" name="desired_results[]" value="Increase in Page Likes/Followers" type="checkbox" {!! in_array('Increase in Page Likes/Followers', (json_decode($book_marketing_form->desired_results) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="desired_results" class="mr-2">Targeted Advertisement</label>
                        <input class="" name="desired_results[]" value="Targeted Advertisement" type="checkbox" {!! in_array('Targeted Advertisement', (json_decode($book_marketing_form->desired_results) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="desired_results" class="mr-2">Social Media Management</label>
                        <input class="" name="desired_results[]" value="Social Media Management" type="checkbox" {!! in_array('Social Media Management', (json_decode($book_marketing_form->desired_results) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="desired_results" class="mr-2">Brand Awareness</label>
                        <input class="" name="desired_results[]" value="Brand Awareness" type="checkbox" {!! in_array('Brand Awareness', (json_decode($book_marketing_form->desired_results) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                    </div>


                    <div class="col-md-12">
                        <h3>
                            Please provide the following information about your book
                        </h3>
                    </div>
                    <div class="col-md-12">
                        <p>
                            This information will be used to create your social profiles and establish the country of origin
                            of your brand - In case the business in purely online, please provide P.O. Box address (however,
                            mailing address remains mandatory)
                        </p>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="business_name">Company/Business Name</label>
                        <input class="form-control" name="business_name" id="business_name" type="text" value="{{ old('business_name', $book_marketing_form->business_name) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="business_email">Business Email Address</label>
                        <input class="form-control" name="business_email" id="business_email" type="text" value="{{ old('business_email', $book_marketing_form->business_email) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="business_contact">Business Phone Number</label>
                        <input class="form-control" name="business_contact" id="business_contact" type="text" value="{{ old('business_contact', $book_marketing_form->business_contact) }}" required/>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="business_address">Business Mailing Address (for verification purposes)</label>
                        <textarea class="form-control" name="business_address" id="business_address" rows="5" >{{ old('business_address', $book_marketing_form->business_address) }}</textarea>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="business_location">Business Location</label>
                        <input class="form-control" name="business_location" id="business_location" type="text" value="{{ old('business_location', $book_marketing_form->business_location) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="business_website_url">Business Website Address (URL)</label>
                        <input class="form-control" name="business_website_url" id="business_website_url" type="text" value="{{ old('business_website_url', $book_marketing_form->business_website_url) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="business_working_hours">Business Working Hours</label>
                        <input class="form-control" name="business_working_hours" id="business_working_hours" type="text" value="{{ old('business_working_hours', $book_marketing_form->business_working_hours) }}" required/>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="where_is_your_book_published">Where is your book published (Please provide the links)</label>
                        <textarea class="form-control" name="where_is_your_book_published" id="where_is_your_book_published" rows="5" >{{ old('where_is_your_book_published', $book_marketing_form->where_is_your_book_published) }}</textarea>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="business_category">Business Category/Industry (Real Estate, Education, IT, Retail etc)</label>
                        <textarea class="form-control" name="business_category" id="business_category" rows="5" >{{ old('business_category', $book_marketing_form->business_category) }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <h3>
                            Existing Social Media Platforms
                        </h3>
                    </div>
                    <div class="col-md-12">
                        <p>
                            <b>
                                Please share links and credentials to your existing social media business pages (if any)
                            </b>
                        </p>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="facebook_link">Link to Facebook Page</label>
                        <input class="form-control" name="facebook_link" id="facebook_link" type="text" value="{{ old('facebook_link', $book_marketing_form->facebook_link) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="instagram_link">Link to Instagram Page</label>
                        <input class="form-control" name="instagram_link" id="instagram_link" type="text" value="{{ old('instagram_link', $book_marketing_form->instagram_link) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="instagram_password">Instagram Password</label>
                        <input class="form-control" name="instagram_password" id="instagram_password" type="text" value="{{ old('instagram_password', $book_marketing_form->instagram_password) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="twitter_link">Link to Twitter Page</label>
                        <input class="form-control" name="twitter_link" id="twitter_link" type="text" value="{{ old('twitter_link', $book_marketing_form->twitter_link) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="twitter_password">Twitter Password</label>
                        <input class="form-control" name="twitter_password" id="twitter_password" type="text" value="{{ old('twitter_password', $book_marketing_form->twitter_password) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="linkedin_link">Link to Linkedin Page</label>
                        <input class="form-control" name="linkedin_link" id="linkedin_link" type="text" value="{{ old('linkedin_link', $book_marketing_form->linkedin_link) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="pinterest_link">Link to Pinterest Page</label>
                        <input class="form-control" name="pinterest_link" id="pinterest_link" type="text" value="{{ old('pinterest_link', $book_marketing_form->pinterest_link) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="pinterest_password">Pinterest Password</label>
                        <input class="form-control" name="pinterest_password" id="pinterest_password" type="text" value="{{ old('pinterest_password', $book_marketing_form->pinterest_password) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="youtube_link">Link to Youtube Page</label>
                        <input class="form-control" name="youtube_link" id="youtube_link" type="text" value="{{ old('youtube_link', $book_marketing_form->youtube_link) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="youtube_gmail">Gmail Address for YouTube</label>
                        <input class="form-control" name="youtube_gmail" id="youtube_gmail" type="text" value="{{ old('youtube_gmail', $book_marketing_form->youtube_gmail) }}" required/>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="youtube_gmail_password">Gmail Password for YouTube</label>
                        <input class="form-control" name="youtube_gmail_password" id="youtube_gmail_password" type="text" value="{{ old('youtube_gmail_password', $book_marketing_form->youtube_gmail_password) }}" required/>
                    </div>


                    <div class="col-md-12">
                        <p>
                            <b>
                                Please mention Social Media platforms that you want to opt (consult your Account Manager):
                            </b>
                        </p>
                        <p>
                            <b>
                                Check one or more of the following:
                            </b>
                        </p>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="social_media_platforms" class="mr-2">Facebook</label>
                        <input class="" name="social_media_platforms[]" value="Facebook" type="checkbox" {!! in_array('Facebook', (json_decode($book_marketing_form->social_media_platforms) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="social_media_platforms" class="mr-2">Twitter</label>
                        <input class="" name="social_media_platforms[]" value="Twitter" type="checkbox" {!! in_array('Twitter', (json_decode($book_marketing_form->social_media_platforms) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="social_media_platforms" class="mr-2">Instagram</label>
                        <input class="" name="social_media_platforms[]" value="Instagram" type="checkbox" {!! in_array('Instagram', (json_decode($book_marketing_form->social_media_platforms) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="social_media_platforms" class="mr-2">Pinterest</label>
                        <input class="" name="social_media_platforms[]" value="Pinterest" type="checkbox" {!! in_array('Pinterest', (json_decode($book_marketing_form->social_media_platforms) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="social_media_platforms" class="mr-2">Youtube</label>
                        <input class="" name="social_media_platforms[]" value="Youtube" type="checkbox" {!! in_array('Youtube', (json_decode($book_marketing_form->social_media_platforms) ?? [])) ? 'checked' : '' !!}/>
                        <br>

                        <label for="social_media_platforms" class="mr-2">Linkedin</label>
                        <input class="" name="social_media_platforms[]" value="Linkedin" type="checkbox" {!! in_array('Linkedin', (json_decode($book_marketing_form->social_media_platforms) ?? [])) ? 'checked' : '' !!}/>
                        <br>
                    </div>


                    <div class="col-md-12">
                        <h3>
                            Demographics
                        </h3>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="target_locations">Target Locations (States, Cities)</label>
                        <input class="form-control" name="target_locations" id="target_locations" type="text" value="{{ old('target_locations', $book_marketing_form->target_locations) }}" required/>
                    </div>
                    <div class="col-md-12">
                        <p>
                            <b>
                                Target Audience
                            </b>
                        </p>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="target_audiences" class="mr-2">Male</label>
                        <input class="" name="target_audiences[]" value="Male" type="checkbox" {!! in_array('Male', (json_decode($book_marketing_form->target_audiences) ?? [])) ? 'checked' : '' !!} />
                        <br>

                        <label for="target_audiences" class="mr-2">Female</label>
                        <input class="" name="target_audiences[]" value="Female" type="checkbox" {!! in_array('Female', (json_decode($book_marketing_form->target_audiences) ?? [])) ? 'checked' : '' !!} />
                        <br>

                        <label for="target_audiences" class="mr-2">Both</label>
                        <input class="" name="target_audiences[]" value="Both" type="checkbox" {!! in_array('Both', (json_decode($book_marketing_form->target_audiences) ?? [])) ? 'checked' : '' !!} />
                        <br>
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label for="age_bracket">Age Bracket</label>
                        <input class="form-control" name="age_bracket" id="age_bracket" type="text" value="{{ old('age_bracket', $book_marketing_form->age_bracket) }}" required/>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="keywords">Please mention keywords or online interests that best represent your audience</label>
                        <textarea class="form-control" name="keywords" id="keywords" rows="5" >{{ old('keywords', $book_marketing_form->keywords) }}</textarea>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="unique_selling_points">What is your business USP (Unique Selling Points)?</label>
                        <textarea class="form-control" name="unique_selling_points" id="unique_selling_points" rows="5" >{{ old('unique_selling_points', $book_marketing_form->unique_selling_points) }}</textarea>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="exclude_information">Are there any topics, websites, information or keywords that you DO NOT want us to use?</label>
                        <textarea class="form-control" name="exclude_information" id="exclude_information" rows="5" >{{ old('exclude_information', $book_marketing_form->exclude_information) }}</textarea>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="additional_comments">Additional Comments</label>
                        <textarea class="form-control" name="additional_comments" id="additional_comments" rows="5" >{{ old('additional_comments', $book_marketing_form->additional_comments) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

{{--        <div class="card mb-4">--}}
{{--            <div class="card-body">--}}
{{--                <div class="card-title mb-3">Attachment</div>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-12">--}}
{{--                        <input type="file" name="attachment[]" multiple/>--}}
{{--                    </div>--}}
{{--                    @foreach($book_marketing_form->formfiles as $formfiles)--}}
{{--                    <div class="col-md-3">--}}
{{--                        <div class="file-box">--}}
{{--                            <h3>{{ $formfiles->name }}</h3>--}}
{{--                            <a href="{{ asset('files/form') }}/{{$formfiles->path}}" target="_blank" class="btn btn-primary">Download</a>--}}
{{--                            <a href="javascript:;" data-id="{{ $formfiles->id }}" class="btn btn-danger delete-file">Delete</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </form>
</div>
@endsection

@push('scripts')
    <script>
        $('input, textarea').each((i, item) => {
            $(item).prop('readonly', true);
        });
    </script>
@endpush
