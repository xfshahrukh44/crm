@extends('layouts.app-support')
@section('title', 'Book Formatting & Publishing Form')

@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">Book Formatting & Publishing Brief INV#{{$data->invoice->invoice_number}}</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">

    <form class="col-md-12 brief-form p-0" method="post" action="{{ route('client.bookformatting.form.update', $data->id) }}" enctype="multipart/form-data">

            @csrf
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title mb-3">Book Formatting & Publishing Brief Form</div>
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="book_title">What is the title of the book? <span>*</span></label>
                            <input class="form-control" name="book_title" id="book_title" type="text" value="{{ old('book_title', $data->book_title) }}" required/>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle">What is the subtitle of the book?</label>
                            <input class="form-control" name="book_subtitle" id="book_subtitle" type="text" value="{{ old('book_subtitle', $data->book_subtitle) }}"/>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="author_name">What is the name of the author? <span>*</span></label>
                            <input class="form-control" name="author_name" id="author_name" type="text"  value="{{ old('author_name', $data->author_name) }}" required/>
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="contributors">Any additional contributors you would like to acknowledge? (e.g. Book Illustrator, Editor, etc.) <span>*</span></label>
                            <textarea class="form-control" name="contributors" id="contributors" rows="5" required>{{ old('contributors', $data->contributors) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title mb-3">Formatting Requirements</div>
                    <p>Where do you want to? <span>*</span></p>
                    @php
                    $publish_your_book = json_decode($data->publish_your_book);
                    @endphp
                    <div class="row">
                        <div class="col-lg-2">
                            <label for="amazon_kdp" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="amazon_kdp" name="publish_your_book[]" value="Amazon KDP" @if($publish_your_book != null) {{ in_array('Amazon KDP', $publish_your_book) ? ' checked' : '' }} @endif data-value="Where do you want to?" data-name="required">Amazon KDP
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label for="barnes_noble" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="barnes_noble" name="publish_your_book[]" value="Barnes & Noble" @if($publish_your_book != null) {{ in_array('Barnes & Noble', $publish_your_book) ? ' checked' : '' }} @endif>Barnes & Noble
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label for="google_books" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="google_books" name="publish_your_book[]" value="Google Books" @if($publish_your_book != null) {{ in_array('Google Books', $publish_your_book) ? ' checked' : '' }} @endif>Google Books
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label for="kobo" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="kobo" name="publish_your_book[]" value="Kobo" @if($publish_your_book != null) {{ in_array('Kobo', $publish_your_book) ? ' checked' : '' }} @endif>Kobo
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label for="ingram_spark" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="ingram_spark" name="publish_your_book[]" value="Ingram Spark" @if($publish_your_book != null) {{ in_array('Ingram Spark', $publish_your_book) ? ' checked' : '' }} @endif>Ingram Spark
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <p>Which formats do you want your book to be formatted on? <span>*</span></p>
                    @php
                    $book_formatted = json_decode($data->book_formatted);
                    @endphp
                    <div class="row">
                        <div class="col-lg-2">
                            <label for="ebook" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="ebook" name="book_formatted[]" value="eBook" @if($book_formatted != null) {{ in_array('eBook', $book_formatted) ? ' checked' : '' }} @endif data-value="Which formats do you want your book to be formatted on?" data-name="required">eBook
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label for="paperback" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="paperback" name="book_formatted[]" value="Paperback" @if($book_formatted != null) {{ in_array('Paperback', $book_formatted) ? ' checked' : '' }} @endif>Paperback
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label for="hardcover" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="hardcover" name="book_formatted[]" value="Hardcover" @if($book_formatted != null) {{ in_array('Hardcover', $book_formatted) ? ' checked' : '' }} @endif>Hardcover
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <p>Which trim size do you want your book to be formatted on? <span>*</span></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="trim_size_1" name="trim_size" value="5_8" {{ $data->trim_size == '5_8' ? 'checked' : '' }} data-value="Which trim size do you want your book to be formatted on?" data-name="required">
                                    <label for="trim_size_1" class="comic">5″ X 8″</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="trim_size_2" name="trim_size" value="5.25_8" {{ $data->trim_size == '5.25_8' ? 'checked' : '' }}>
                                    <label for="trim_size_2" class="comic">5.25″ X 8″</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="trim_size_3" name="trim_size" value="5.5_8.5" {{ $data->trim_size == '5.5_8.5' ? 'checked' : '' }}>
                                    <label for="trim_size_3" class="comic">5.5″ X 8.5″</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="trim_size_4" name="trim_size" value="6_9" {{ $data->trim_size == '6_9' ? 'checked' : '' }}>
                                    <label for="trim_size_4" class="comic">6″ X 9″</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="trim_size_5" name="trim_size" value="8.5_11" {{ $data->trim_size == '8.5_11' ? 'checked' : '' }}>
                                    <label for="trim_size_5" class="comic">8.5″ X 11″</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="trim_size_6" name="trim_size" value="Other" {{ $data->trim_size == 'Other' ? 'checked' : '' }}>
                                    <label for="trim_size_6" class="comic">Other (Please specify)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 form-group mb-3">
                            <label for="trim_size_7">If you have selected Other please specify the trim size you want your book to be formatted on.</label>
                            <input class="form-control" name="other_trim_size" id="trim_size_7" type="text"  value="{{ old('other_trim_size', $data->other_trim_size) }}"/>
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="additional_instructions">Any Additional Instructions that you would like us to follow?</label>
                            <textarea class="form-control" name="additional_instructions" id="additional_instructions" rows="5">{{ old('additional_instructions', $data->additional_instructions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>



            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title mb-3">Author/Publisher Information</div>


                    <p> Do you already have a publishing account setup on any of the platforms? (Yes/No) </p>

                    <p> Note: If Yes mention the name of the platforms and provide its credentials as in email address and password. If you do not have an account just provide an email address that we can use to sign up your account on Amazon KDP or other platforms. </p>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="book_title"> Email Address: </label>
                            <input class="form-control" name="auth_pub_email" id="book_title" type="text" value="{{ old('auth_pub_email', $data->auth_pub_email) }}" required/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Password (Leave blank if you do not already have an account): </label>
                            <input class="form-control" name="auth_pub_password" id="book_subtitle" type="text" value="{{ old('auth_pub_password', $data->auth_pub_password) }}"/>
                        </div>



                    </div>

                    <h4 class="mb-3 mt-3"> Fill in the following information </h4>

                    <div class="row">

                        <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle"> Full Name </label>
                            <input class="form-control" name="auth_pub_full_name" id="book_subtitle" type="text" value="{{ old('auth_pub_full_name', $data->auth_pub_full_name) }}"/>
                        </div>

                         <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle"> Date of birth (YYYY-MM-DD) </label>
                            <input class="form-control" name="auth_pub_dob" id="book_subtitle" type="date" value="{{ old('auth_pub_dob', $data->auth_pub_dob) }}"/>
                        </div>

                         <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle"> Country or Region </label>
                            <input class="form-control" name="auth_pub_country" id="book_subtitle" type="text" value="{{ old('auth_pub_country', $data->auth_pub_country) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Address Line 1 </label>
                            <textarea class="form-control" name="auth_pub_address_1" id="genre_book" rows="5" >{{ old('auth_pub_address_1', $data->auth_pub_address_1) }}</textarea>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Address Line 2 (Optional) </label>
                            <textarea class="form-control" name="auth_pub_address_2" id="genre_book" rows="5" >{{ old('auth_pub_address_2', $data->auth_pub_address_2) }}</textarea>
                        </div>


                         <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> City </label>
                            <input class="form-control" name="auth_pub_city" id="book_subtitle" type="text" value="{{ old('auth_pub_city', $data->auth_pub_city) }}"/>
                        </div>

                         <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> State/Province/Region </label>
                            <input class="form-control" name="auth_pub_state" id="book_subtitle" type="text" value="{{ old('auth_pub_state', $data->auth_pub_state) }}"/>
                        </div>

                         <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Postal Code </label>
                            <input class="form-control" name="auth_pub_postalcode" id="book_subtitle" type="text" value="{{ old('auth_pub_postalcode', $data->auth_pub_postalcode) }}"/>
                        </div>

                         <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Cell Phone Number </label>
                            <input class="form-control" name="auth_pub_phone" id="book_subtitle" type="text" value="{{ old('auth_pub_phone', $data->auth_pub_phone) }}"/>
                        </div>

                         <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Would you like the book to have headers, footers, or page numbers? If yes, please provide any specific instructions. </label>
                            <textarea class="form-control" name="auth_pub_have_header_footer" id="genre_book" rows="5" >{{ old('auth_pub_have_header_footer', $data->auth_pub_have_header_footer) }}</textarea>
                        </div>

                    </div>

                    <hr>

                    <h4 class="mb-3 mt-3"> Getting Paid </h4>

                    <p> (Provide your bank information to receive electronic royalty payments. Tell us about your bank) </p>

                    <div class="row">

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Your Bank Name </label>
                            <input class="form-control" name="gp_bank_name" id="gp_bank_name" type="text" value="{{ old('gp_bank_name', $data->gp_bank_name) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Bank Location/Address </label>
                            <input class="form-control" name="gp_bank_location" id="gp_bank_location" type="text" value="{{ old('gp_bank_location', $data->gp_bank_location) }}"/>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Routing number </label>
                            <input class="form-control" name="gp_routing_no" id="gp_routing_no" type="text" value="{{ old('gp_routing_no', $data->gp_routing_no) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Account Number </label>
                            <input class="form-control" name="gp_account_no" id="gp_account_no" type="text" value="{{ old('gp_account_no', $data->gp_account_no) }}"/>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Account holder name </label>
                            <input class="form-control" name="gp_ac_holder_name" id="gp_ac_holder_name" type="text" value="{{ old('gp_ac_holder_name', $data->gp_ac_holder_name) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Type of account (Checking/Savings) </label>
                            <input class="form-control" name="gp_type_of_acc" id="gp_type_of_acc" type="text" value="{{ old('gp_type_of_acc', $data->gp_type_of_acc) }}"/>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> IBAN number (Leave blank if this does apply to your country standards) </label>
                            <input class="form-control" name="gp_iban_no" id="gp_iban_no" type="text" value="{{ old('gp_iban_no', $data->gp_iban_no) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> BIC/SWIFT code (Leave blank if this does apply to your country standards) </label>
                            <input class="form-control" name="gp_swift_code" id="gp_swift_code" type="text" value="{{ old('gp_swift_code', $data->gp_swift_code) }}"/>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Bank Code (Leave blank if this does apply to your country standards) </label>
                            <input class="form-control" name="gp_bank_code" id="gp_bank_code" type="text" value="{{ old('gp_bank_code', $data->gp_bank_code) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Branch Code (Leave blank if this does apply to your country standards) </label>
                            <input class="form-control" name="gp_branch_code" id="gp_branch_code" type="text" value="{{ old('gp_branch_code', $data->gp_branch_code) }}"/>
                        </div>


                    </div>

                    <hr>

                    <h4 class="mb-3 mt-3"> Tax Identity Information </h4>

                    <p> <b> Please provide the following information accurately as this information is verified through different processes. Submission of incorrect information may result in account suspension. </b> </p>

                    <p for="genre"> What is your tax classification (Individual/Business)? </p>
                    <p> "Individual" includes Sole Proprietors or Single-Member LLCs where the owner is an individual </p>

                    <div class="row">

                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="taxclassification1" name="taxclassification" value="Individual" {{ $data->taxclassification == 'Individual' ? 'checked' : '' }} >
                                    <label for="taxclassification1" class="comic"> Individual </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="taxclassification2" name="taxclassification" value="Business" {{ $data->taxclassification == 'Business' ? 'checked' : '' }} >
                                    <label for="taxclassification2" class="comic"> Business </label>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="row">


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Full name (As per your registered tax details) </label>
                            <input class="form-control" name="tax_iden_full_name" id="tax_iden_full_name" type="text" value="{{ old('tax_iden_full_name', $data->tax_iden_full_name) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Country of citizenship </label>
                            <input class="form-control" name="tax_iden_citizenship" id="tax_iden_citizenship" type="text" value="{{ old('tax_iden_citizenship', $data->tax_iden_citizenship) }}"/>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Permanent address (As registered in your tax details) </label>
                            <input class="form-control" name="tax_iden_permanent_address" id="tax_iden_permanent_address" type="text" value="{{ old('tax_iden_permanent_address', $data->tax_iden_permanent_address) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Mailing address </label>
                            <input class="form-control" name="tax_iden_mailing_address" id="tax_iden_mailing_address" type="text" value="{{ old('tax_iden_mailing_address', $data->tax_iden_mailing_address) }}"/>
                        </div>

                    </div>

                     <p> <b> Provide one of the following as per your tax information filled in the form above </b> </p>

                     <div class="row">

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> SSN Number </label>
                            <input class="form-control" name="tax_iden_ssn_no" id="tax_iden_ssn_no" type="text" value="{{ old('tax_iden_ssn_no', $data->tax_iden_ssn_no) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> EIN Number </label>
                            <input class="form-control" name="tax_iden_ein_no" id="tax_iden_ein_no" type="text" value="{{ old('tax_iden_ein_no', $data->tax_iden_ein_no) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> TIN Number </label>
                            <input class="form-control" name="tax_iden_tin_no" id="tax_iden_tin_no" type="text" value="{{ old('tax_iden_tin_no', $data->tax_iden_tin_no) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Other </label>
                            <input class="form-control" name="tax_iden_other" id="tax_iden_other" type="text" value="{{ old('tax_iden_other', $data->tax_iden_other) }}"/>
                        </div>

                    </div>

                    <hr>

                    <h4 class="mb-3 mt-3"> Book Details </h4>

                    <div class="row">

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Please provide the Book Description that is to be published with your book </label>
                            <textarea class="form-control" name="bd_book_description" id="bd_book_description" rows="5" >{{ old('bd_book_description', $data->bd_book_description) }}</textarea>
                        </div>

                    </div>

                    <p> Do you own the copyright and hold necessary publishing rights to publish this book as your own? (Yes/No) </p>

                    <div class="row">

                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="bd_book_as_your_own1" name="bd_book_as_your_own" value="Yes" {{ $data->bd_book_as_your_own == 'Yes' ? 'checked' : '' }} >
                                    <label for="bd_book_as_your_own1" class="comic"> Yes </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="bd_book_as_your_own2" name="bd_book_as_your_own" value="No" {{ $data->bd_book_as_your_own == 'No' ? 'checked' : '' }} >
                                    <label for="bd_book_as_your_own2" class="comic"> No </label>
                                </div>
                            </div>
                        </div>

                    </div>


                    <p> What is the Genre of your Book? (Fiction/Non Fiction) </p>

                    <div class="row">

                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="bd_genre_of_your_book1" name="bd_genre_of_your_book" value="Fiction" {{ $data->bd_genre_of_your_book == 'Fiction' ? 'checked' : '' }} >
                                    <label for="bd_genre_of_your_book1" class="comic"> Fiction </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="formCheck font-box">
                                <div class="form-check pl-0">
                                    <input type="radio" class="form-check-input" id="bd_genre_of_your_book2" name="bd_genre_of_your_book" value="Non Fiction" {{ $data->bd_genre_of_your_book == 'Non Fiction' ? 'checked' : '' }} >
                                    <label for="bd_genre_of_your_book2" class="comic"> Non Fiction </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Please mention 7 or more keywords that are relevant your book. </label>
                            <textarea class="form-control" name="bd_keywords" id="bd_keywords" rows="5" >{{ old('bd_keywords', $data->bd_keywords) }}</textarea>
                        </div>

                    </div>

                    <hr>

                    <p> Would you like to use Free Platform assigned ISBN number or Paid ISBN Numbers to be used at the time of publishing your book. </p>

                    <p><b> Note: If you already have purchased ISBN Numbers for your book please share the ISBN numbers with their imprint name </b></p>

                    <div class="row">

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> ISBN eBook </label>
                            <input class="form-control" name="bd_isbn_book" id="bd_isbn_book" type="text" value="{{ old('bd_isbn_book', $data->bd_isbn_book) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> ISBN Paperback </label>
                            <input class="form-control" name="bd_isbn_paperback" id="bd_isbn_paperback" type="text" value="{{ old('bd_isbn_paperback', $data->bd_isbn_paperback) }}"/>
                        </div>

                         <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> ISBN Hardcover </label>
                            <input class="form-control" name="bd_isbn_hardcover" id="bd_isbn_hardcover" type="text" value="{{ old('bd_isbn_hardcover', $data->bd_isbn_hardcover) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> Imprint Name </label>
                            <input class="form-control" name="bd_imprint_name" id="bd_imprint_name" type="text" value="{{ old('bd_imprint_name', $data->bd_imprint_name) }}"/>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> At what price do you want to sell your eBook? </label>
                            <input class="form-control" name="bd_sell_your_ebook" id="bd_sell_your_ebook" type="text" value="{{ old('bd_sell_your_ebook', $data->bd_sell_your_ebook) }}"/>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="book_subtitle"> At what price do you want to sell your print book? (Mention price for paperback and hardcover) </label>
                            <input class="form-control" name="bd_print_book" id="bd_print_book" type="text" value="{{ old('bd_print_book', $data->bd_print_book) }}"/>
                        </div>

                    </div>


                    <p> Mention at least 3 categories that best suits your book in the box below </p>
                    @php
                    $best_suits_your_book = json_decode($data->best_suits_your_book);
                    @endphp
                    <div class="row">

                        <div class="col-lg-2">
                            <label for="best_suits_your_book1" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book1" name="best_suits_your_book[]" value="Arts & Photography" @if($best_suits_your_book != null) {{ in_array('Arts & Photography', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Arts & Photography
                                    </div>
                                </div>
                            </label>
                        </div>

                         <div class="col-lg-3">
                            <label for="best_suits_your_book2" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book2" name="best_suits_your_book[]" value="Engineering & Transportation" @if($best_suits_your_book != null) {{ in_array('Engineering & Transportation', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Engineering & Transportation
                                    </div>
                                </div>
                            </label>
                        </div>


                       <div class="col-lg-3">
                            <label for="best_suits_your_book3" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book3" name="best_suits_your_book[]" value="Politics & Social Sciences" @if($best_suits_your_book != null) {{ in_array('Politics & Social Sciences', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Politics & Social Sciences
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book4" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book4" name="best_suits_your_book[]" value="Biographies & Memoirs" @if($best_suits_your_book != null) {{ in_array('Biographies & Memoirs', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Biographies & Memoirs
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book5" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book5" name="best_suits_your_book[]" value="Reference" @if($best_suits_your_book != null) {{ in_array('Reference', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Reference
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book6" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book6" name="best_suits_your_book[]" value="Health, Fitness & Dieting" @if($best_suits_your_book != null) {{ in_array('Health, Fitness & Dieting', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Health, Fitness & Dieting
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book7" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book7" name="best_suits_your_book[]" value="Business & Money" @if($best_suits_your_book != null) {{ in_array('Business & Money', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Business & Money
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book8" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book8" name="best_suits_your_book[]" value="History" @if($best_suits_your_book != null) {{ in_array('History', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        History
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book9" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book9" name="best_suits_your_book[]" value="Religion & Spirituality" @if($best_suits_your_book != null) {{ in_array('Religion & Spirituality', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Religion & Spirituality
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book10" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book10" name="best_suits_your_book[]" value="Calendars" @if($best_suits_your_book != null) {{ in_array('Calendars', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Calendars
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book11" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book11" name="best_suits_your_book[]" value="Romance" @if($best_suits_your_book != null) {{ in_array('Romance', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Romance
                                    </div>
                                </div>
                            </label>
                        </div>


                        <div class="col-lg-2">
                            <label for="best_suits_your_book12" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book12" name="best_suits_your_book[]" value="Humor & Entertainment" @if($best_suits_your_book != null) {{ in_array('Humor & Entertainment', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Humor & Entertainment
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book13" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book13" name="best_suits_your_book[]" value="Christian Books & Bibles" @if($best_suits_your_book != null) {{ in_array('Christian Books & Bibles', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Christian Books & Bibles
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book14" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book14" name="best_suits_your_book[]" value="Law" @if($best_suits_your_book != null) {{ in_array('Law', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Law
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book15" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book15" name="best_suits_your_book[]" value="Science & Math" @if($best_suits_your_book != null) {{ in_array('Science & Math', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Science & Math
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book16" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book16" name="best_suits_your_book[]" value="Comics & Graphic Novels" @if($best_suits_your_book != null) {{ in_array('Comics & Graphic Novels', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Comics & Graphic Novels
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book17" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book17" name="best_suits_your_book[]" value="LGBTQ+ Books" @if($best_suits_your_book != null) {{ in_array('LGBTQ+ Books', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        LGBTQ+ Books
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book18" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book18" name="best_suits_your_book[]" value="Science Fiction & Fantasy" @if($best_suits_your_book != null) {{ in_array('Science Fiction & Fantasy', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Science Fiction & Fantasy
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book19" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book19" name="best_suits_your_book[]" value="Computers & Technology" @if($best_suits_your_book != null) {{ in_array('Computers & Technology', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Computers & Technology
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book20" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book20" name="best_suits_your_book[]" value="Literature & Fiction" @if($best_suits_your_book != null) {{ in_array('Literature & Fiction', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Literature & Fiction
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book21" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book21" name="best_suits_your_book[]" value="Self-Help" @if($best_suits_your_book != null) {{ in_array('Self-Help', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Self-Help
                                    </div>
                                </div>
                            </label>
                        </div>


                        <div class="col-lg-2">
                            <label for="best_suits_your_book22" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book22" name="best_suits_your_book[]" value="Cookbooks, Food & Wine" @if($best_suits_your_book != null) {{ in_array('Cookbooks, Food & Wine', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Cookbooks, Food & Wine
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book23" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book23" name="best_suits_your_book[]" value="Medical Books" @if($best_suits_your_book != null) {{ in_array('Medical Books', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Medical Books
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book24" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book24" name="best_suits_your_book[]" value="Sports & Outdoors" @if($best_suits_your_book != null) {{ in_array('Sports & Outdoors', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Sports & Outdoors
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book25" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book25" name="best_suits_your_book[]" value="Crafts, Hobbies & Home" @if($best_suits_your_book != null) {{ in_array('Crafts, Hobbies & Home', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Crafts, Hobbies & Home
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-3">
                            <label for="best_suits_your_book26" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book26" name="best_suits_your_book[]" value="Mystery, Thriller & Suspense" @if($best_suits_your_book != null) {{ in_array('Mystery, Thriller & Suspense', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Mystery, Thriller & Suspense
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-3">
                            <label for="best_suits_your_book27" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book27" name="best_suits_your_book[]" value="Teen & Young Adult" @if($best_suits_your_book != null) {{ in_array('Teen & Young Adult', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Teen & Young Adult
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book28" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book28" name="best_suits_your_book[]" value="Education & Teaching" @if($best_suits_your_book != null) {{ in_array('Education & Teaching', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Education & Teaching
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book29" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book29" name="best_suits_your_book[]" value="Parenting & Relationships" @if($best_suits_your_book != null) {{ in_array('Parenting & Relationships', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Parenting & Relationships
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book30" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book30" name="best_suits_your_book[]" value="Science & Math" @if($best_suits_your_book != null) {{ in_array('Science & Math', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Science & Math
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book31" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book31" name="best_suits_your_book[]" value="Science Fiction & Fantasy" @if($best_suits_your_book != null) {{ in_array('Science Fiction & Fantasy', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Science Fiction & Fantasy
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book32" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book32" name="best_suits_your_book[]" value="Test Preparation" @if($best_suits_your_book != null) {{ in_array('Test Preparation', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Test Preparation
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-lg-2">
                            <label for="best_suits_your_book33" class="w-100">
                                <div class="formCheck purpose-box font-box">
                                    <div class="form-check ml-0 pl-0">
                                        <input type="checkbox" class="form-check-input" id="best_suits_your_book33" name="best_suits_your_book[]" value="Travel" @if($best_suits_your_book != null) {{ in_array('Travel', $best_suits_your_book) ? ' checked' : '' }} @endif >
                                        Travel
                                    </div>
                                </div>
                            </label>
                        </div>



                    </div>


                    <div class="row">

                        <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle"> Category 1 </label>
                            <input class="form-control" name="bd_category1" id="bd_category1" type="text" value="{{ old('bd_category1', $data->bd_category1) }}"/>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle"> Category 2 </label>
                            <input class="form-control" name="bd_category2" id="bd_category2" type="text" value="{{ old('bd_category2', $data->bd_category2) }}"/>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="book_subtitle"> Category 3 </label>
                            <input class="form-control" name="bd_category3" id="bd_category3" type="text" value="{{ old('bd_category3', $data->bd_category3) }}"/>
                        </div>


                        <!--Formating-->

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Are there any special elements in your book that require formatting (e.g., illustrations, tables, graphs, etc.)? </label>
                            <input class="form-control" name="bd_special_elements" id="bd_special_elements" type="text" value="{{ old('bd_special_elements', $data->bd_special_elements) }}"/>
                        </div>

                         <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Do you have any specific preferences or requirements for the book's margins, line spacing, paragraph spacing or spacing in general? </label>
                            <input class="form-control" name="bd_any_specific_preferences" id="bd_any_specific_preferences" type="text" value="{{ old('bd_any_specific_preferences', $data->bd_any_specific_preferences) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Will your book have any special formatting needs for quotations, citations, or references? </label>
                            <input class="form-control" name="bd_any_special_formatting" id="bd_any_special_formatting" type="text" value="{{ old('bd_any_special_formatting', $data->bd_any_special_formatting) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Are there any specific design elements or styles you would like to incorporate into the book (e.g., drop caps, special fonts, etc.)? </label>
                            <input class="form-control" name="bd_any_specific_design" id="bd_any_specific_design" type="text" value="{{ old('bd_any_specific_design', $data->bd_any_specific_design) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Do you have any specific instructions regarding the placement and formatting of images or illustrations? </label>
                            <input class="form-control" name="bd_any_specific_instructions" id="bd_any_specific_instructions" type="text" value="{{ old('bd_any_specific_instructions', $data->bd_any_specific_instructions) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Will your book include any special formatting for footnotes, endnotes, or glossaries? </label>
                            <input class="form-control" name="bd_any_endnotes_or_glossaries" id="bd_any_endnotes_or_glossaries" type="text" value="{{ old('bd_any_endnotes_or_glossaries', $data->bd_any_endnotes_or_glossaries) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Do you have any specific requirements for the font style and font size of headings, paragraphs, etc? </label>
                            <input class="form-control" name="bd_any_style_and_font" id="bd_any_style_and_font" type="text" value="{{ old('bd_any_style_and_font', $data->bd_any_style_and_font) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Does your book have images or fonts that are to be printed in color or black and white? (Please specify) </label>
                            <input class="form-control" name="bd_color_black_and_white" id="bd_color_black_and_white" type="text" value="{{ old('bd_color_black_and_white', $data->bd_color_black_and_white) }}"/>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="book_subtitle"> Is your manuscript completely edited in terms of grammar, spellings, punctuations, sentence structure, etc. and the content is finalized and approved for it to be moved to the formatting and publishing phase? </label>
                            <input class="form-control" name="bd_formatting_and_pub_phase" id="bd_formatting_and_pub_phase" type="text" value="{{ old('bd_formatting_and_pub_phase', $data->bd_formatting_and_pub_phase) }}"/>
                        </div>


                    </div>



                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title mb-3">Attachment</div>
                    <div class="row">
                        <div class="col-12">
                            <input type="file" name="attachment[]" multiple/>
                        </div>
                        @foreach($data->formfiles as $formfiles)
                            <div class="col-md-3">
                                <div class="file-box">
                                    <h3>{{ $formfiles->name }}</h3>
                                    <a href="{{ asset('files/form') }}/{{$formfiles->path}}" target="_blank" class="btn btn-primary">Download</a>
                                    <a href="javascript:;" data-id="{{ $formfiles->id }}" class="btn btn-danger delete-file">Delete</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>



        </form>

</div>
@endsection

@push('scripts')
@endpush
