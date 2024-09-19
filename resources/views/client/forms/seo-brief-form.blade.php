<form class="col-md-12 brief-form p-0" method="post" action="{{ route('client.seo-brief.form.update', $forms->id) }}" enctype="multipart/form-data">
    @csrf
    <div class="card mb-4">
        <div class="card-body">
            <div class="card-title mb-3">SEO Brief Form</div>
            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label for="company_name">Company Name</label>
                    <input class="form-control" name="company_name" id="company_name" type="text" value="{{ old('company_name', $forms->company_name) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="company_name">Website URL</label>
                    <input class="form-control" name="website_url" id="website_url" type="text" value="{{ old('website_url', $forms->website_url) }}" required/>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">Business Address</label>
                    <textarea class="form-control" name="business_address" id="business_address" rows="5" >{{ old('business_address', $forms->business_address) }}</textarea>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="company_name">Industry/Niche</label>
                    <input class="form-control" name="industry_or_niche" id="industry_or_niche" type="text" value="{{ old('industry_or_niche', $forms->industry_or_niche) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="company_name">Contact Person Name</label>
                    <input class="form-control" name="contact_person_name" id="contact_person_name" type="text" value="{{ old('contact_person_name', $forms->contact_person_name) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="company_name">Email</label>
                    <input class="form-control" name="email" id="email" type="text" value="{{ old('email', $forms->email) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="company_name">Contact Number</label>
                    <input class="form-control" name="contact_number" id="contact_number" type="text" value="{{ old('contact_number', $forms->contact_number) }}" required/>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">What are your primary business goals for the next 6-12 months?</label>
                    <textarea class="form-control" name="primary_business_goals" id="primary_business_goals" rows="5" >{{ old('primary_business_goals', $forms->primary_business_goals) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer"><b>What are your main SEO objectives?</b> (e.g., increase organic traffic, improve keyword rankings, boost sales or leads, etc.)</label>
                    <textarea class="form-control" name="main_seo_objectives" id="main_seo_objectives" rows="5" >{{ old('main_seo_objectives', $forms->main_seo_objectives) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">Any specific KPIs you'd like to track?</label>
                    <textarea class="form-control" name="kpis" id="kpis" rows="5" >{{ old('kpis', $forms->kpis) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer"><b>Who is your primary target audience?</b> (Demographics, interests, pain points, etc.)</label>
                    <textarea class="form-control" name="primary_target_audience" id="primary_target_audience" rows="5" >{{ old('primary_target_audience', $forms->primary_target_audience) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">Are there any secondary or niche audiences you want to reach?</label>
                    <textarea class="form-control" name="secondary_niche" id="secondary_niche" rows="5" >{{ old('secondary_niche', $forms->secondary_niche) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer"><b>Which geographical areas are you targeting?</b> (Local, national, or global)</label>
                    <textarea class="form-control" name="geographical_areas" id="geographical_areas" rows="5" >{{ old('geographical_areas', $forms->geographical_areas) }}</textarea>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="core_offer">Have you previously done SEO on your website?</label>
                    <select name="previously_done_seo" id="" class="form-control">
                        <option value="Yes" {!! $forms->previously_done_seo == 'Yes' ? 'selected' : '' !!}>Yes</option>
                        <option value="No" {!! $forms->previously_done_seo == 'No' ? 'selected' : '' !!}>No</option>
                    </select>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer"><b>Who are your top 3 competitors?</b> (Provide URLs)</label>
                    <textarea class="form-control" name="top_three_competitors" id="top_three_competitors" rows="5" >{{ old('top_three_competitors', $forms->top_three_competitors) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer"><b>Do you have a list of target keywords?</b> (Please provide a list if available)</label>
                    <textarea class="form-control" name="target_keywords" id="target_keywords" rows="5" >{{ old('target_keywords', $forms->target_keywords) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">Any keywords you believe are critical for your business that you are not currently ranking for?</label>
                    <textarea class="form-control" name="target_keywords_2" id="target_keywords_2" rows="5" >{{ old('target_keywords_2', $forms->target_keywords_2) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">Are you targeting local-specific keywords or geographical areas?</label>
                    <textarea class="form-control" name="local_specific_or_geographical" id="local_specific_or_geographical" rows="5" >{{ old('local_specific_or_geographical', $forms->local_specific_or_geographical) }}</textarea>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="core_offer">Do you have an online/physical store?</label>
                    <select name="have_store" id="" class="form-control">
                        <option value="Yes" {!! $forms->have_store == 'Yes' ? 'selected' : '' !!}>Yes</option>
                        <option value="No" {!! $forms->have_store == 'No' ? 'selected' : '' !!}>No</option>
                    </select>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer"><b>Please Grant the admin access of Google Analytics & Google Search Console tool.</b> (if available)</label>
                    <textarea class="form-control" name="ga_gsc_admin_access" id="ga_gsc_admin_access" rows="5" >{{ old('ga_gsc_admin_access', $forms->ga_gsc_admin_access) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="core_offer">Any additional comments we should know about this project?</label>
                    <textarea class="form-control" name="additional_comments" id="additional_comments" rows="5" >{{ old('additional_comments', $forms->additional_comments) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
{{--                @if($forms->business_established == null)--}}
                <div class="col-md-12 mt-1">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
{{--                @endif--}}
            </div>
        </div>
    </div>
</form>