<form class="col-md-12 brief-form p-0" method="post" action="{{ route('client.press-release.form.update', $forms->id) }}" enctype="multipart/form-data">
    @csrf
    <div class="card mb-4">
        <div class="card-body">
            <div class="card-title mb-3">Press Release Form</div>
            <div class="row">
                <div class="col-md-12">
                    <h3>
                        Basic Book Details
                    </h3>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="book_title">Title</label>
                    <input class="form-control" name="book_title" id="book_title" type="text" value="{{ old('book_title', $forms->book_title) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="author_name">Author Name</label>
                    <input class="form-control" name="author_name" id="author_name" type="text" value="{{ old('author_name', $forms->author_name) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="genre">Genre</label>
                    <input class="form-control" name="genre" id="genre" type="text" value="{{ old('genre', $forms->genre) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="publisher">Publisher</label>
                    <input class="form-control" name="publisher" id="publisher" type="text" value="{{ old('publisher', $forms->publisher) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="publication_date">Publication Date</label>
                    <input class="form-control" name="publication_date" id="publication_date" type="text" value="{{ old('publication_date', $forms->publication_date) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="isbn">ISBN</label>
                    <input class="form-control" name="isbn" id="isbn" type="text" value="{{ old('isbn', $forms->isbn) }}" required/>
                </div>


                <div class="col-md-12">
                    <h3>
                        Book Description
                    </h3>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="synopsis">Blurb/Synopsis</label>
                    <textarea class="form-control" name="synopsis" id="synopsis" rows="5" >{{ old('synopsis', $forms->synopsis) }}</textarea>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="unique_selling_points">Unique Selling Points: What makes this book stand out?</label>
                    <input class="form-control" name="unique_selling_points" id="unique_selling_points" type="text" value="{{ old('unique_selling_points', $forms->unique_selling_points) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="target_audience">Target Audience: Who is the book intended for?</label>
                    <input class="form-control" name="target_audience" id="target_audience" type="text" value="{{ old('target_audience', $forms->target_audience) }}" required/>
                </div>


                <div class="col-md-12">
                    <h3>
                        Author Information
                    </h3>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="short_biography">Short Biography</label>
                    <textarea class="form-control" name="short_biography" id="short_biography" rows="5" >{{ old('short_biography', $forms->short_biography) }}</textarea>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="previous_works">Previous Works: Mention of other published works</label>
                    <input class="form-control" name="previous_works" id="previous_works" type="text" value="{{ old('previous_works', $forms->previous_works) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="award_recognition">Awards/Recognitions</label>
                    <input class="form-control" name="award_recognition" id="award_recognition" type="text" value="{{ old('award_recognition', $forms->award_recognition) }}" required/>
                </div>


                <div class="col-md-12">
                    <h3>
                        Key Features for the Media
                    </h3>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="quote_excerpts">Quotes/Excerpts</label>
                    <textarea class="form-control" name="quote_excerpts" id="quote_excerpts" rows="5" >{{ old('quote_excerpts', $forms->quote_excerpts) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="reviews">Advance Praise/Reviews</label>
                    <textarea class="form-control" name="reviews" id="reviews" rows="5" >{{ old('reviews', $forms->reviews) }}</textarea>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="tie_ins">Tie-ins to Current Events</label>
                    <textarea class="form-control" name="tie_ins" id="tie_ins" rows="5" >{{ old('tie_ins', $forms->tie_ins) }}</textarea>
                </div>


                <div class="col-md-12">
                    <h3>
                        Marketing Information
                    </h3>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="formats_and_availability">Formats and Availability</label>
                    <input class="form-control" name="formats_and_availability" id="formats_and_availability" type="text" value="{{ old('formats_and_availability', $forms->formats_and_availability) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="price">Price</label>
                    <input class="form-control" name="price" id="price" type="text" value="{{ old('price', $forms->price) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="events">Events</label>
                    <input class="form-control" name="events" id="events" type="text" value="{{ old('events', $forms->events) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="media_kit">Media Kit</label>
                    <input class="form-control" name="media_kit" id="media_kit" type="text" value="{{ old('media_kit', $forms->media_kit) }}" required/>
                </div>


                <div class="col-md-12">
                    <h3>
                        Contact Information
                    </h3>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="press_contact">Press Contact</label>
                    <input class="form-control" name="press_contact" id="press_contact" type="text" value="{{ old('press_contact', $forms->press_contact) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="twitter">Twitter</label>
                    <input class="form-control" name="twitter" id="twitter" type="text" value="{{ old('twitter', $forms->twitter) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="tiktok">Tiktok</label>
                    <input class="form-control" name="tiktok" id="tiktok" type="text" value="{{ old('tiktok', $forms->tiktok) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="facebook">Facebook</label>
                    <input class="form-control" name="facebook" id="facebook" type="text" value="{{ old('facebook', $forms->facebook) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="instagram">Instagram</label>
                    <input class="form-control" name="instagram" id="instagram" type="text" value="{{ old('instagram', $forms->instagram) }}" required/>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="publishers_contact">Publisher’s Contact (if applicable)</label>
                    <input class="form-control" name="publishers_contact" id="publishers_contact" type="text" value="{{ old('publishers_contact', $forms->publishers_contact) }}" required/>
                </div>


                <div class="col-md-12">
                    <h3>
                        Call-to-Action (CTA)
                    </h3>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="cta">Call-to-Action</label>
                    <textarea class="form-control" name="cta" id="cta" rows="5" >{{ old('cta', $forms->cta) }}</textarea>
                </div>


                <div class="col-md-12">
                    <h3>
                        Visual Elements
                    </h3>
                    <ul>
                        <li>
                            <b>Book Cover Image</b>: High-resolution image of the book’s front cover
                        </li>
                        <li>
                            <b>Author Photo:</b> Professional headshot of the author.
                        </li>
                        <li>
                            <b>Promotional Graphics (Optional):</b> Social media banners, illustrations, or other media assets.
                        </li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title mb-3">Add media</div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="file" name="attachment[]" multiple />
                                </div>
                                @foreach ($forms->formfiles as $formfiles)
                                    <div class="col-md-3">
                                        <div class="file-box">
                                            <h3>{{ $formfiles->name }}</h3>
                                            <a href="{{ asset('files/form') }}/{{ $formfiles->path }}" target="_blank"
                                               class="btn btn-primary">Download</a>
                                            <a href="javascript:;" data-id="{{ $formfiles->id }}"
                                               class="btn btn-danger delete-file">Delete</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <h3>
                        Key Highlights Section
                    </h3>
                    <p>
                        Summarize the most attention-grabbing facts about the book, such as:
                    </p>
                    <ul>
                        <li>A bestseller claims.</li>
                        <li>Collaboration with a famous personality.</li>
                        <li>Inspiration for a film/TV adaptation.</li>
                    </ul>
                </div>
                <div class="col-md-12 form-group mb-3">
                    <label for="key_highlights">Key Highlights</label>
                    <textarea class="form-control" name="key_highlights" id="key_highlights" rows="5" >{{ old('key_highlights', $forms->key_highlights) }}</textarea>
                </div>

                <div class="col-md-12 text-center">
                    <p>
                        <b>
                            Thank You for Filling Out This Project Brief
                        </b>
                    </p>
                    <p>
                        Please make sure you’ve filled out all information accurately as
                        it will be used to create/manage/verify your social media campaigns.
                    </p>
                    <p>
                        <b>
                            Contact your dedicated project manager if you have any queries
                        </b>
                    </p>
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
