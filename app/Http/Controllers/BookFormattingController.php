<?php

namespace App\Http\Controllers;

use App\Models\BookFormatting;
use Illuminate\Http\Request;
use Auth;
use App\Models\FormFiles;

class BookFormattingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookFormatting  $bookFormatting
     * @return \Illuminate\Http\Response
     */
    public function show(BookFormatting $bookFormatting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BookFormatting  $bookFormatting
     * @return \Illuminate\Http\Response
     */
    public function edit(BookFormatting $bookFormatting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookFormatting  $bookFormatting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all()); 
        
        $book_formatting_form = BookFormatting::find($id);
        
        if($book_formatting_form->user_id == Auth::user()->id){
            
            $book_formatting_form->book_title = $request->book_title;
            $book_formatting_form->book_subtitle = $request->book_subtitle;
            $book_formatting_form->author_name = $request->author_name;
            $book_formatting_form->contributors = $request->contributors;
            $book_formatting_form->publish_your_book = json_encode($request->publish_your_book);
            $book_formatting_form->book_formatted = json_encode($request->book_formatted);
            $book_formatting_form->trim_size = $request->trim_size;
            $book_formatting_form->other_trim_size = $request->other_trim_size;
            $book_formatting_form->additional_instructions = $request->additional_instructions; 
            
            $book_formatting_form->auth_pub_email = $request->auth_pub_email;
            $book_formatting_form->auth_pub_password = $request->auth_pub_password;
            $book_formatting_form->auth_pub_full_name = $request->auth_pub_full_name;
            $book_formatting_form->auth_pub_dob = $request->auth_pub_dob;
            $book_formatting_form->auth_pub_country = $request->auth_pub_country;
            $book_formatting_form->auth_pub_address_1 = $request->auth_pub_address_1;
            $book_formatting_form->auth_pub_address_2 = $request->auth_pub_address_2;
            $book_formatting_form->auth_pub_city = $request->auth_pub_city;
            $book_formatting_form->auth_pub_state = $request->auth_pub_state;
            $book_formatting_form->auth_pub_postalcode = $request->auth_pub_postalcode;
            $book_formatting_form->auth_pub_phone = $request->auth_pub_phone;
            $book_formatting_form->auth_pub_have_header_footer = $request->auth_pub_have_header_footer;
            $book_formatting_form->gp_bank_name = $request->gp_bank_name;
            $book_formatting_form->gp_bank_location = $request->gp_bank_location;
            $book_formatting_form->gp_routing_no = $request->gp_routing_no;
            $book_formatting_form->gp_account_no = $request->gp_account_no;
            $book_formatting_form->gp_ac_holder_name = $request->gp_ac_holder_name;
            $book_formatting_form->gp_type_of_acc = $request->gp_type_of_acc;
            $book_formatting_form->gp_iban_no = $request->gp_iban_no;
            $book_formatting_form->gp_swift_code = $request->gp_swift_code;
            $book_formatting_form->gp_bank_code = $request->gp_bank_code;
            $book_formatting_form->gp_branch_code = $request->gp_branch_code;
            $book_formatting_form->taxclassification = $request->taxclassification;
            $book_formatting_form->tax_iden_full_name = $request->tax_iden_full_name;
            $book_formatting_form->tax_iden_citizenship = $request->tax_iden_citizenship;
            $book_formatting_form->tax_iden_permanent_address = $request->tax_iden_permanent_address;
            $book_formatting_form->tax_iden_mailing_address = $request->tax_iden_mailing_address;
            
            $book_formatting_form->tax_iden_ssn_no = $request->tax_iden_ssn_no;
            $book_formatting_form->tax_iden_ein_no = $request->tax_iden_ein_no;
            $book_formatting_form->tax_iden_tin_no = $request->tax_iden_tin_no;
            $book_formatting_form->tax_iden_other = $request->tax_iden_other;
            $book_formatting_form->bd_book_description = $request->bd_book_description;
            $book_formatting_form->bd_book_as_your_own = $request->bd_book_as_your_own;
            $book_formatting_form->bd_genre_of_your_book = $request->bd_genre_of_your_book;
            $book_formatting_form->bd_keywords = $request->bd_keywords;
            $book_formatting_form->bd_isbn_book = $request->bd_isbn_book;
            $book_formatting_form->bd_isbn_paperback = $request->bd_isbn_paperback;
            $book_formatting_form->bd_isbn_hardcover = $request->bd_isbn_hardcover;
            $book_formatting_form->bd_imprint_name = $request->bd_imprint_name;
            $book_formatting_form->bd_sell_your_ebook = $request->bd_sell_your_ebook;
            $book_formatting_form->bd_print_book = $request->bd_print_book;
            $book_formatting_form->best_suits_your_book = json_encode($request->best_suits_your_book);
            $book_formatting_form->bd_category1 = $request->bd_category1;
            $book_formatting_form->bd_category2 = $request->bd_category2;
            $book_formatting_form->bd_category3 = $request->bd_category3;
            $book_formatting_form->bd_special_elements = $request->bd_special_elements;
            $book_formatting_form->bd_any_specific_preferences = $request->bd_any_specific_preferences;
            $book_formatting_form->bd_any_special_formatting = $request->bd_any_special_formatting;
            $book_formatting_form->bd_any_specific_design = $request->bd_any_specific_design;
            $book_formatting_form->bd_any_specific_instructions = $request->bd_any_specific_instructions;
            $book_formatting_form->bd_any_endnotes_or_glossaries = $request->bd_any_endnotes_or_glossaries;
            $book_formatting_form->bd_any_style_and_font = $request->bd_any_style_and_font;
            $book_formatting_form->bd_color_black_and_white = $request->bd_color_black_and_white;
            $book_formatting_form->bd_formatting_and_pub_phase = $request->bd_formatting_and_pub_phase;


            
            $book_formatting_form->save();
            if($request->hasfile('attachment'))
            {
                $i = 0;
                foreach($request->file('attachment') as $file)
                {
                    $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $name = strtolower(str_replace(' ', '-', $file_name)) . '_' . $i . '_' .time().'.'.$file->extension();
                    $file->move(public_path().'/files/form', $name);
                    $i++;
                    $form_files = new FormFiles();
                    $form_files->name = $file_name;
                    $form_files->path = $name;
                    $form_files->logo_form_id = $book_formatting_form->id;
                    $form_files->form_code = 6;
                    $form_files->save();
                }
            }
            return redirect()->back()->with('success', 'Book Formatting & Publishing Form Created');
        }else{
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookFormatting  $bookFormatting
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookFormatting $bookFormatting)
    {
        //
    }
}
