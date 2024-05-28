@extends('layouts.app-qa')
@section('title', 'Editing & Proofreading Form')

@section('content')
<div class="breadcrumb">
    <h1 class="mr-2">ISBN FORM INV#{{$data->invoice->invoice_number}}</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    
    <form class="col-md-12 brief-form p-0" method="post" action="{{ route('client.isbn.form.update', $data->id) }}" enctype="multipart/form-data">
        
        @csrf
        
        <div class="card mb-4">
            <div class="card-body mb-4">
                <div class="card-title mb-3">{{ $data->form_name }}</div>
                
                <h3>Preassigned Control Number (PCN) Enrollment Questionnaire for Authors and Self-Publishers </h3>
                
                <br>
                
                <h4>Personal Information</h4>
                
                <div class="row">
                    
                    <div class="col-md-12 form-group mb-3">
                         <label>Full Name</label>
                         <input type="text" class="form-control"  name="pi_fullname" value="{{ old('pi_fullname', $data->pi_fullname) }}" required>
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="pi_email" value="{{ old('pi_email', $data->pi_email) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Phone Number</label>
                        <input type="tel" class="form-control" name="pi_phone" value="{{ old('pi_phone', $data->pi_phone) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Mailing Address</label>
                        <input type="text" class="form-control" name="pi_mailing_address" value="{{ old('pi_mailing_address', $data->pi_mailing_address) }}" >
                    </div>
                    
                   
                    
                    <div class="col-md-12 form-group mb-3">
                        
                         <h4 class="mt-3 mb-3">Book Information </h4>
                         
                        <label>Title of the Book</label>
                        <input type="text" class="form-control" name="bi_titlebook" value="{{ old('bi_titlebook', $data->bi_titlebook) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Subtitle (if any)</label>
                        <input type="text" class="form-control" name="bi_subtitle" value="{{ old('bi_subtitle', $data->bi_subtitle) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="exampleFormControlSelect1">Author(s) Name(s)</label>
                        <textarea class="form-control" name="bi_authorname" value="{{ old('bi_authorname', $data->bi_authorname) }}" >  </textarea>
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="exampleFormControlSelect1">Editor(s) Name(s)</label>
                        <textarea class="form-control" name="bi_editorname"  id="about" rows="5" > {{ old('bi_editorname', $data->bi_editorname) }} </textarea>
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                         <label>Publisher's Name</label>
                        <input type="text" class="form-control" name="bi_publishername" value="{{ old('bi_publishername', $data->bi_publishername) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Projected Publication Date</label>
                        <input type="date" class="form-control" name="bi_projectpublication" value="{{ old('bi_projectpublication', $data->bi_projectpublication) }}" >
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="genre">Is this book part of a series?</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="part_of_series1" name="part_of_series" value="Yes" {{ $data->part_of_series == 'Yes' ? 'checked' : '' }}>
                                        <label for="part_of_series1" class="genre">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="part_of_series2" name="part_of_series" value="No" {{ $data->part_of_series == 'No' ? 'checked' : '' }}>
                                        <label for="part_of_series2" class="genre">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="part_of_series3" name="part_of_series" value="if" {{ $data->part_of_series == 'if' ? 'checked' : '' }}>
                                        <label for="part_of_series3" class="genre">If yes, please specify the name of the series.</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="genre">Book Format</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookformat1" name="bookformat" value="Hardcover" {{ $data->bookformat == 'Hardcover' ? 'checked' : '' }}>
                                        <label for="bookformat1" class="genre">Hardcover</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookformat2" name="bookformat" value="Paperback" {{ $data->bookformat == 'Paperback' ? 'checked' : '' }}>
                                        <label for="bookformat2" class="genre">Paperback</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookformat3" name="bookformat" value="Audiobook" {{ $data->bookformat == 'Audiobook' ? 'checked' : '' }}>
                                        <label for="bookformat3" class="genre">Audiobook</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookformat4" name="bookformat" value="eBook" {{ $data->bookformat == 'eBook' ? 'checked' : '' }}>
                                        <label for="bookformat4" class="genre">eBook</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookformat5" name="bookformat" value="other" {{ $data->bookformat == 'other' ? 'checked' : '' }}>
                                        <label for="bookformat5" class="genre">Other (please specify)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                       
                    <div class="col-md-12 form-group mb-3">
                        <label>Estimated Number of Pages</label>
                        <input type="number" class="form-control" name="bi_est_no_page" value="{{ old('bi_est_no_page', $data->bi_est_no_page) }}" >
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>ISBN (if already assigned)</label>
                        <input type="text" class="form-control" name="isbn_assign" value="{{ old('isbn_assign', $data->isbn_assign) }}" >
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Book Categories/Genres</label>
                        <input type="text" class="form-control" name="bi_bookcategory" value="{{ old('bi_bookcategory', $data->bi_bookcategory) }}" >
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Brief Summary of the Book</label>
                        <textarea class="form-control" name="bi_bri_summaryofbook"  id="about" rows="5" > {{ old('bi_bri_summaryofbook', $data->bi_bri_summaryofbook) }} </textarea>
                    </div>
                    
                   
                    
                    
                    <div class="col-md-12 form-group mb-3">
                         <h4 class="mt-3 mb-3">Additional Information</h4>
                         
                        <label for="genre">Will the book include illustrations?</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="includeillustrations1" name="includeillustrations" value="Yes" {{ $data->includeillustrations == 'Yes' ? 'checked' : '' }}>
                                        <label for="includeillustrations1" class="genre">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="includeillustrations2" name="includeillustrations" value="No" {{ $data->includeillustrations == 'No' ? 'checked' : '' }}>
                                        <label for="includeillustrations2" class="genre">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="includeillustrations3" name="includeillustrations" value="if" {{ $data->includeillustrations == 'if' ? 'checked' : '' }}>
                                        <label for="includeillustrations3" class="genre">If yes, please specify the number and type (black and white, color, etc.)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="genre">Will the book include a preface or introduction?</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="prefaceorintroduction1" name="prefaceorintroduction" value="Yes" {{ $data->prefaceorintroduction == 'Yes' ? 'checked' : '' }}>
                                        <label for="prefaceorintroduction1" class="genre">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="prefaceorintroduction2" name="prefaceorintroduction" value="No" {{ $data->prefaceorintroduction == 'No' ? 'checked' : '' }}>
                                        <label for="prefaceorintroduction2" class="genre">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="genre">Does the book have a table of contents?</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookhave1" name="bookhave" value="Yes" {{ $data->bookhave == 'Yes' ? 'checked' : '' }}>
                                        <label for="bookhave1" class="genre">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="bookhave2" name="bookhave" value="No" {{ $data->bookhave == 'No' ? 'checked' : '' }}>
                                        <label for="bookhave2" class="genre">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label for="genre">Do you intend to distribute this book to libraries?</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="booktolibraries1" name="booktolibraries" value="Yes" {{ $data->booktolibraries == 'Yes' ? 'checked' : '' }}>
                                        <label for="booktolibraries1" class="genre">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="booktolibraries2" name="booktolibraries" value="No" {{ $data->booktolibraries == 'No' ? 'checked' : '' }}>
                                        <label for="booktolibraries2" class="genre">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                   
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Any other information or special instructions?</label>
                        <textarea class="form-control" id="about" rows="5" name="special_instruction"  > {{ old('special_instruction', $data->special_instruction) }} </textarea>
                    </div>
                    
                    
                     
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        
                        <p><i>Please fill out this questionnaire and return it to us to proceed with the PCN enrollment process for your book. Thank you!</i></p>
                        
                        <h3 class="mt-3 mb-3">Bowker ISBN Registration Form</h3>
                        
                        <h4 class="mt-3 mb-3">Title Information</h4>
                        
                        <label>Book Title</label>
                        <input type="text" class="form-control" name="irf_booktitle" value="{{ old('irf_booktitle', $data->irf_booktitle) }}" >
                        
                    </div>
                    
                    
                     <div class="col-md-12 form-group mb-3">
                         <label>Subtitle</label>
                        <input type="text" class="form-control" name="irf_booksubtitle" value="{{ old('irf_booksubtitle', $data->irf_booksubtitle) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                         <label>Describe your book (0 of 350 words)</label>
                         <textarea class="form-control" name="irf_describebook" id="about" rows="5" > {{ old('irf_describebook', $data->irf_describebook) }} </textarea>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        
                         <h4 class="mt-3 mb-3">Subjects & Genres</h4>
                        
                         <label>First Genre:</label>
                         <input type="text" class="form-control" name="gen_firstgenre" value="{{ old('gen_firstgenre', $data->gen_firstgenre) }}" >
                         
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Second Genre</label>
                        <input type="text" class="form-control" name="gen_secondgenre" value="{{ old('gen_secondgenre', $data->gen_secondgenre) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        
                         <h4 class="mt-3 mb-3">Authors & Contributors</h4>
    
                        <label>First Name</label>
                        <input type="text" class="form-control" name="ac_firstname" value="{{ old('ac_firstname', $data->ac_firstname) }}" >
                      
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                      <label>Last Name</label>
                      <input type="text" class="form-control" name="ac_lastname" value="{{ old('ac_lastname', $data->ac_lastname) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Suffix</label>
                        <input type="text" class="form-control" name="ac_suffix" value="{{ old('ac_suffix', $data->ac_suffix) }}" >
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Biography (0 of 350 words)</label>
                        <textarea class="form-control" name="ac_biography"  id="about" rows="5" > {{ old('ac_biography', $data->ac_biography) }} </textarea>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                        <label>Function (Author, Writer, Illustrator, etc)</label>
                        <textarea class="form-control" name="ac_function" id="about" rows="5" > {{ old('ac_function', $data->ac_function) }} </textarea>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                         
                        <h4 class="mt-3 mb-3">Sales & Pricing</h4>
                         
                        <label>Publisher</label>
                        <input type="text" class="form-control" name="sp_publisher" value="{{ old('sp_publisher', $data->sp_publisher) }}" >
                    </div>
                    
                    
                     <div class="col-md-12 form-group mb-3">
                        <label>Publication Date</label>
                        <input type="date" class="form-control" name="sp_publicationdate" value="{{ old('sp_publicationdate', $data->sp_publicationdate) }}" >
                    </div>
                        
                        
                    <div class="col-md-12 form-group mb-3">
                        <label for="genre">Target Audience</label>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience1" name="targetaudience" value="Adult Education" {{ $data->targetaudience == 'Adult Education' ? 'checked' : '' }}>
                                        <label for="targetaudience1" class="genre">Adult Education</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience2" name="targetaudience" value="College Audience" {{ $data->targetaudience == 'College Audience' ? 'checked' : '' }}>
                                        <label for="targetaudience2" class="genre">College Audience</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience3" name="targetaudience" value="Elemantry/High School" {{ $data->targetaudience == 'Elemantry/High School' ? 'checked' : '' }}>
                                        <label for="targetaudience3" class="genre">Elemantry/High School</label>
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-3">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience4" name="targetaudience" value="English as a Second Language" {{ $data->targetaudience == 'English as a Second Language' ? 'checked' : '' }}>
                                        <label for="targetaudience4" class="genre">English as a Second Language</label>
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-2">
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience5" name="targetaudience" value="Family" {{ $data->targetaudience == 'Family' ? 'checked' : '' }}>
                                        <label for="targetaudience5" class="genre">Family</label>
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-lg-2 mt-3" >
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience6" name="targetaudience" value="Junenile Audience" {{ $data->targetaudience == 'Junenile Audience' ? 'checked' : '' }}>
                                        <label for="targetaudience6" class="genre">Junenile Audience</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 mt-3" >
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience7" name="targetaudience" value="Scholarly & Professional" {{ $data->targetaudience == 'Scholarly & Professional' ? 'checked' : '' }}>
                                        <label for="targetaudience7" class="genre">Scholarly & Professional</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 mt-3" >
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience8" name="targetaudience" value="Second Language Teaching" {{ $data->targetaudience == 'Second Language Teaching' ? 'checked' : '' }}>
                                        <label for="targetaudience8" class="genre">Second Language Teaching</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 mt-3" >
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience9" name="targetaudience" value="Trade" {{ $data->targetaudience == 'Trade' ? 'checked' : '' }}>
                                        <label for="targetaudience9" class="genre">Trade</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 mt-3" >
                                <div class="formCheck font-box mb-0">
                                    <div class="form-check pl-0">
                                        <input type="radio" class="form-check-input" id="targetaudience10" name="targetaudience" value="Young Adult Audience" {{ $data->targetaudience == 'Young Adult Audience' ? 'checked' : '' }}>
                                        <label for="targetaudience10" class="genre">Young Adult Audience</label>
                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12 form-group mb-3">
                         
                        <h4 class="mt-3 mb-3">Book Price</h4>
                         
                        <label>Set Dollar Amount</label>
                        <input type="text" class="form-control" name="dollar_amount" value="{{ old('dollar_amount', $data->dollar_amount) }}" >
                    
                    </div>
                    
                    
                        
                </div>
            </div>
        </div>
    
        
    </form>
                        
    
</div>
@endsection

@push('scripts')
@endpush