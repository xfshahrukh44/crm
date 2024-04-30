<?php

namespace App\Http\Controllers;

use App\Models\Bookprinting;
use Illuminate\Http\Request;
use App\Models\FormFiles;
use Auth;

class BookprintingController extends Controller
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
     * @param  \App\Models\Bookprinting  $bookPrinting
     * @return \Illuminate\Http\Response
     */
    public function show(Bookprinting $bookPrinting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bookprinting  $bookPrinting
     * @return \Illuminate\Http\Response
     */
    public function edit(Bookprinting $bookPrinting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bookprinting  $bookPrinting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        // dd("this");
        
        $bookPrinting_form = Bookprinting::find($id);
        
        if($bookPrinting_form->user_id == Auth::user()->id){
            
            $bookPrinting_form->title = $request->title;
            $bookPrinting_form->need_the_printed = $request->need_the_printed;
            $bookPrinting_form->printed_copies = $request->printed_copies;
            $bookPrinting_form->trim_size_of_the_pages = $request->trim_size_of_the_pages;
            $bookPrinting_form->trim_size_you_want = $request->trim_size_you_want;
            $bookPrinting_form->full_name = $request->full_name;
            $bookPrinting_form->phone_number = $request->phone_number;
            $bookPrinting_form->address = $request->address;
            
            $bookPrinting_form->save();
            
            
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
                    $form_files->logo_form_id = $bookPrinting_form->id;
                    $form_files->form_code = 6;
                    $form_files->save();
                }
            }
            
            return redirect()->back()->with('success', 'Book Printing Form Updated');
            
        }else{
            return redirect()->back();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookCover  $bookPrinting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bookprinting $bookPrinting)
    {
        //
    }
}
