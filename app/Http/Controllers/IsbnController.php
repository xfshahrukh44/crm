<?php

namespace App\Http\Controllers;

use App\Models\Isbnform;
use Illuminate\Http\Request;
use App\Models\FormFiles;
use Auth;

class IsbnController extends Controller
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
     * @param  \App\Models\Isbnform  $isbn
     * @return \Illuminate\Http\Response
     */
     
    public function show(Isbnform $isbn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Isbnform  $isbn
     * @return \Illuminate\Http\Response
     */
     
    public function edit(Isbnform $isbn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Isbnform  $isbn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        
        $isbn_form = Isbnform::find($id);
        
        // dd($request->all());
        
        if($isbn_form->user_id == Auth::user()->id){
            
            $isbn_form->pi_fullname = $request->pi_fullname;
            $isbn_form->pi_email = $request->pi_email;
            $isbn_form->pi_phone = $request->pi_phone;
            $isbn_form->pi_mailing_address = $request->pi_mailing_address;
            $isbn_form->bi_titlebook = $request->bi_titlebook;
            $isbn_form->bi_subtitle = $request->bi_subtitle;
            $isbn_form->bi_authorname = $request->bi_authorname;
            $isbn_form->bi_editorname = $request->bi_editorname;
            $isbn_form->bi_publishername = $request->bi_publishername;
            $isbn_form->bi_projectpublication = $request->bi_projectpublication;
            $isbn_form->part_of_series = $request->part_of_series;
            $isbn_form->bookformat = $request->bookformat;
            $isbn_form->bi_est_no_page = $request->bi_est_no_page;
            $isbn_form->isbn_assign = $request->isbn_assign;
            $isbn_form->bi_bookcategory = $request->bi_bookcategory;
            $isbn_form->bi_bri_summaryofbook = $request->bi_bri_summaryofbook;
            $isbn_form->includeillustrations = $request->includeillustrations;
            $isbn_form->prefaceorintroduction = $request->prefaceorintroduction;
            $isbn_form->bookhave = $request->bookhave;
            $isbn_form->booktolibraries = $request->booktolibraries;
            $isbn_form->special_instruction = $request->special_instruction;
            $isbn_form->irf_booktitle = $request->irf_booktitle;
            $isbn_form->irf_booksubtitle = $request->irf_booksubtitle;
            $isbn_form->irf_describebook = $request->irf_describebook;
            $isbn_form->gen_firstgenre = $request->gen_firstgenre;
            $isbn_form->gen_secondgenre = $request->gen_secondgenre;
            $isbn_form->ac_firstname = $request->ac_firstname;
            $isbn_form->ac_lastname = $request->ac_lastname;
            $isbn_form->ac_suffix = $request->ac_suffix;
            $isbn_form->ac_biography = $request->ac_biography;
            $isbn_form->ac_function = $request->ac_function;
            $isbn_form->sp_publisher = $request->sp_publisher;
            $isbn_form->sp_publicationdate = $request->sp_publicationdate;
            $isbn_form->targetaudience = $request->targetaudience;
            $isbn_form->dollar_amount = $request->dollar_amount;

            
            $isbn_form->save();
            
            if($request->hasfile('attachment'))
            {
                $i = 0;
                foreach($request->file('attachment') as $file)
                {
                    $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $name = strtolower(str_replace(' ', '-', $file_name)) . '_' . $i . '_' .time().'.'.$file->getClientOriginalExtension();
                    
                    $file->move(public_path().'/files/form', $name);
                    $i++;
                    $form_files = new FormFiles();
                    $form_files->name = $file_name;
                    $form_files->path = $name;
                    $form_files->logo_form_id = $isbn_form->id;
                    $form_files->form_code = 11;
                    $form_files->save();
                }
            }
            return redirect()->back()->with('success', 'ISBN Form Updated');
        }else{
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Isbnform  $isbn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Isbnform $isbn)
    {
        //
    }
}
