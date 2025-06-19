@extends('v2.layouts.app')
@section('title', 'SEO Form')

@section('content')
    <div class="for-slider-main-banner">
        <section class="list-0f">
            <div class="container-fluid">
                <div class="breadcrumb">
                    <h1 class="mr-2">SEO Brief INV#{{$seo_form->invoice->invoice_number}}</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <form class="col-md-12 brief-form" method="post" route="{{ route('client.logo.form.update', $seo_form->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title mb-3">SEO Brief Form</div>
                                <div class="row">
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="company_name">Company Name</label>
                                        <input class="form-control" name="company_name" id="company_name" type="text" value="{{ old('company_name', $seo_form->company_name) }}" required/>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="company_name">Website URL</label>
                                        <input class="form-control" name="website_url" id="website_url" type="text" value="{{ old('website_url', $seo_form->website_url) }}" required/>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">Business Address</label>
                                        <textarea class="form-control" name="business_address" id="business_address" rows="5" >{{ old('business_address', $seo_form->business_address) }}</textarea>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="company_name">Industry/Niche</label>
                                        <input class="form-control" name="industry_or_niche" id="industry_or_niche" type="text" value="{{ old('industry_or_niche', $seo_form->industry_or_niche) }}" required/>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="company_name">Contact Person Name</label>
                                        <input class="form-control" name="contact_person_name" id="contact_person_name" type="text" value="{{ old('contact_person_name', $seo_form->contact_person_name) }}" required/>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="company_name">Email</label>
                                        <input class="form-control" name="email" id="email" type="text" value="{{ old('email', $seo_form->email) }}" required/>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="company_name">Contact Number</label>
                                        <input class="form-control" name="contact_number" id="contact_number" type="text" value="{{ old('contact_number', $seo_form->contact_number) }}" required/>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">What are your primary business goals for the next 6-12 months?</label>
                                        <textarea class="form-control" name="primary_business_goals" id="primary_business_goals" rows="5" >{{ old('primary_business_goals', $seo_form->primary_business_goals) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer"><b>What are your main SEO objectives?</b> (e.g., increase organic traffic, improve keyword rankings, boost sales or leads, etc.)</label>
                                        <textarea class="form-control" name="main_seo_objectives" id="main_seo_objectives" rows="5" >{{ old('main_seo_objectives', $seo_form->main_seo_objectives) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">Any specific KPIs you'd like to track?</label>
                                        <textarea class="form-control" name="kpis" id="kpis" rows="5" >{{ old('kpis', $seo_form->kpis) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer"><b>Who is your primary target audience?</b> (Demographics, interests, pain points, etc.)</label>
                                        <textarea class="form-control" name="primary_target_audience" id="primary_target_audience" rows="5" >{{ old('primary_target_audience', $seo_form->primary_target_audience) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">Are there any secondary or niche audiences you want to reach?</label>
                                        <textarea class="form-control" name="secondary_niche" id="secondary_niche" rows="5" >{{ old('secondary_niche', $seo_form->secondary_niche) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer"><b>Which geographical areas are you targeting?</b> (Local, national, or global)</label>
                                        <textarea class="form-control" name="geographical_areas" id="geographical_areas" rows="5" >{{ old('geographical_areas', $seo_form->geographical_areas) }}</textarea>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="core_offer">Have you previously done SEO on your website?</label>
                                        <select name="previously_done_seo" id="" class="form-control">
                                            <option value="Yes" {!! $seo_form->previously_done_seo == 'Yes' ? 'selected' : '' !!}>Yes</option>
                                            <option value="No" {!! $seo_form->previously_done_seo == 'No' ? 'selected' : '' !!}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer"><b>Who are your top 3 competitors?</b> (Provide URLs)</label>
                                        <textarea class="form-control" name="top_three_competitors" id="top_three_competitors" rows="5" >{{ old('top_three_competitors', $seo_form->top_three_competitors) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer"><b>Do you have a list of target keywords?</b> (Please provide a list if available)</label>
                                        <textarea class="form-control" name="target_keywords" id="target_keywords" rows="5" >{{ old('target_keywords', $seo_form->target_keywords) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">Any keywords you believe are critical for your business that you are not currently ranking for?</label>
                                        <textarea class="form-control" name="target_keywords_2" id="target_keywords_2" rows="5" >{{ old('target_keywords_2', $seo_form->target_keywords_2) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">Are you targeting local-specific keywords or geographical areas?</label>
                                        <textarea class="form-control" name="local_specific_or_geographical" id="local_specific_or_geographical" rows="5" >{{ old('local_specific_or_geographical', $seo_form->local_specific_or_geographical) }}</textarea>
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label for="core_offer">Do you have an online/physical store?</label>
                                        <select name="have_store" id="" class="form-control">
                                            <option value="Yes" {!! $seo_form->have_store == 'Yes' ? 'selected' : '' !!}>Yes</option>
                                            <option value="No" {!! $seo_form->have_store == 'No' ? 'selected' : '' !!}>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer"><b>Please Grant the admin access of Google Analytics & Google Search Console tool.</b> (if available)</label>
                                        <textarea class="form-control" name="ga_gsc_admin_access" id="ga_gsc_admin_access" rows="5" >{{ old('ga_gsc_admin_access', $seo_form->ga_gsc_admin_access) }}</textarea>
                                    </div>
                                    <div class="col-md-12 form-group mb-3">
                                        <label for="core_offer">Any additional comments we should know about this project?</label>
                                        <textarea class="form-control" name="additional_comments" id="additional_comments" rows="5" >{{ old('additional_comments', $seo_form->additional_comments) }}</textarea>
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
                        {{--                    @foreach($seo_form->formfiles as $formfiles)--}}
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
            </div>
        </section>
    </div>
@endsection

@push('scripts')
@endpush
