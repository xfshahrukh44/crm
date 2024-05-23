<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LogoForm;
use App\Models\User;
use App\Models\WebForm;
use App\Models\SmmForm;
use App\Models\ContentWritingForm;
use App\Models\SeoForm;
use App\Models\BookFormatting;
use App\Models\NoForm;
use App\Models\BookWriting;
use App\Models\AuthorWebsite;
use App\Models\Proofreading;
use App\Models\BookCover;
use Illuminate\Http\Request;
use App\Models\FormFiles;
use App\Models\Isbnform;
use App\Models\Bookprinting;


use Auth;
use File;

class LogoFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $logo_form = LogoForm::find($id);
        if($logo_form->user_id == Auth::user()->id){
            return view('client.logo', compact('logo_form'));
        }else{
            return redirect()->back();
        }
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
     * @param  \App\Models\LogoForm  $logoForm
     * @return \Illuminate\Http\Response
     */
    public function show(LogoForm $logoForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LogoForm  $logoForm
     * @return \Illuminate\Http\Response
     */
    public function edit(LogoForm $logoForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LogoForm  $logoForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $logo_form = LogoForm::find($id);
        if($logo_form->user_id == Auth::user()->id){
            $logo_form->logo_name = $request->logo_name;
            $logo_form->slogan = $request->slogan;
            $logo_form->business = $request->business;
            $logo_form->logo_categories = json_encode($request->logo_categories);
            $logo_form->icon_based_logo = json_encode($request->icon_based_logo);
            $logo_form->font_style = $request->font_style;
            $logo_form->additional_information = $request->additional_information;
            $logo_form->save();
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
                    $form_files->logo_form_id = $logo_form->id;
                    $form_files->form_code = 1;
                    $form_files->save();
                }
            }
            return redirect()->back()->with('success', 'Logo Form Created');
        }else{
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LogoForm  $logoForm
     * @return \Illuminate\Http\Response
     */
     
     
    public function destroy(Request $request)
    {
        $logo_form = FormFiles::where('form_code', 1)->first();
        if(Auth::user()->id == $logo_form->logo_form->user_id){
            if(File::exists(public_path('files//form/'.$logo_form->path))){
                File::delete(public_path('files//form/'.$logo_form->path));
            }
            $logo_form->delete();
            return response()->json(['success' => true, 'message' => 'File Deleted Successfully']);
        }else{
            return response()->json(['success' => false, 'message' => 'You Dont have Access']);
        }
    }
    

    public function getBriefPending(Request $request){
//        $logo_form = LogoForm::where('logo_name', '')->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $web_form = WebForm::where('business_name', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $smm_form = SmmForm::where('business_name', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $content_writing_form = ContentWritingForm::where('company_name', null)
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $seo_form = SeoForm::where('company_name', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $book_formatting_form = BookFormatting::where('book_title', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $author_website_form = AuthorWebsite::where('author_name', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $book_writing_form = BookWriting::where('book_title', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $no_form = NoForm::where('name', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $proofreading_form = Proofreading::where('description', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $bookcover_form = BookCover::where('title', null)->groupBy('user_id')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();

        //change
        $client_users_with_brief_pendings = User::whereIn('id', get_brief_client_user_ids())->get();

//        return view('admin.brief.pending', compact('client_users_with_brief_pendings', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form', 'book_formatting_form', 'book_writing_form', 'author_website_form', 'proofreading_form', 'bookcover_form', 'no_form'));
        return view('admin.brief.pending', compact('client_users_with_brief_pendings'));
    }

    public function getBriefPendingById(Request $request){
//        $logo_form = LogoForm::where('logo_name', '')->where('agent_id', Auth()->user()->id)
        $logo_form = LogoForm::where('logo_name', '')
            ->when($request->has('user_id'), function ($q) use ($request) {
                return $q->where('user_id', $request->get('user_id'));
            })->get();
//        $web_form = WebForm::where('business_name', null)->where('agent_id', Auth()->user()->id)
        $web_form = WebForm::where('business_name', null)
            ->when($request->has('user_id'), function ($q) use ($request) {
                return $q->where('user_id', $request->get('user_id'));
            })->get();
//        $smm_form = SmmForm::where('business_name', null)->where('agent_id', Auth()->user()->id)
        $smm_form = SmmForm::where('business_name', null)
            ->when($request->has('user_id'), function ($q) use ($request) {
                return $q->where('user_id', $request->get('user_id'));
            })->get();
//        $content_writing_form = ContentWritingForm::where('company_name', null)->where('agent_id', Auth()->user()->id)
        $content_writing_form = ContentWritingForm::where('company_name', null)
            ->when($request->has('user_id'), function ($q) use ($request) {
                return $q->where('user_id', $request->get('user_id'));
            })->get();
//        $seo_form = SeoForm::where('company_name', null)->where('agent_id', Auth()->user()->id)
        $seo_form = SeoForm::where('company_name', null)
            ->when($request->has('user_id'), function ($q) use ($request) {
                return $q->where('user_id', $request->get('user_id'));
            })->get();

        //change
        $client_users_with_brief_pendings = User::whereIn('id', get_brief_client_user_ids())->get();

//        return view('sale.brief.pending', compact('client_users_with_brief_pendings', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form'));
        return view('sale.brief.pending', compact('client_users_with_brief_pendings'));
    }
    
    public function getBriefPendingByIdManager(Request $request){
//        $logo_form = LogoForm::where('logo_name', '')->whereHas('invoice', function ($query) {
//                        return $query->where('brand', Auth::user()->brand_list());
//                    })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $web_form = WebForm::where('business_name', null)->whereHas('invoice', function ($query) {
//                        return $query->where('brand', Auth::user()->brand_list());
//                    })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $smm_form = SmmForm::where('business_name', null)->whereHas('invoice', function ($query) {
//                        return $query->where('brand', Auth::user()->brand_list());
//                    })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $content_writing_form = ContentWritingForm::where('company_name', null)->whereHas('invoice', function ($query) {
//                                    return $query->where('brand', Auth::user()->brand_list());
//                                })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $seo_form = SeoForm::where('company_name', null)->whereHas('invoice', function ($query) {
//                        return $query->where('brand', Auth::user()->brand_list());
//                    })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $book_formatting_form = BookFormatting::where('book_title', null)->whereHas('invoice', function ($query) {
//                        return $query->where('brand', Auth::user()->brand_list());
//                    })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $author_website_form = AuthorWebsite::where('author_name', null)->whereHas('invoice', function ($query) {
//                                    return $query->where('brand', Auth::user()->brand_list());
//                                })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $book_writing_form = BookWriting::where('book_title', null)->whereHas('invoice', function ($query) {
//            return $query->where('brand', Auth::user()->brand_list());
//        })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $no_form = NoForm::whereHas('invoice', function ($query) {
//                    return $query->where('brand', Auth::user()->brand_list());
//                })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $proofreading_form = Proofreading::where('description', null)->whereHas('invoice', function ($query) {
//            return $query->where('brand', Auth::user()->brand_list());
//        })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $bookcover_form = BookCover::where('title', null)->whereHas('invoice', function ($query) {
//            return $query->where('brand', Auth::user()->brand_list());
//        })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//
//        $isbn_form = Isbnform::where('pi_fullname', null)->whereHas('invoice', function ($query) {
//            return $query->where('brand', Auth::user()->brand_list());
//        })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//
//
//        $bookprinting_form = Bookprinting::where('title', null)->whereHas('invoice', function ($query) {
//            return $query->where('brand', Auth::user()->brand_list());
//        })->groupBy('user_id')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();

        //change
        $client_users_with_brief_pendings = User::whereIn('id', get_brief_client_user_ids())->get();
        
//        return view('manager.brief.pending', compact('client_users_with_brief_pendings', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form', 'book_formatting_form', 'book_writing_form', 'author_website_form', 'no_form', 'proofreading_form', 'bookcover_form', 'isbn_form', 'bookprinting_form'));
        return view('manager.brief.pending', compact('client_users_with_brief_pendings'));
    }


    public function getPendingProject(Request $request){
//        $logo_form = LogoForm::where('logo_name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $web_form = WebForm::where('business_name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $smm_form = SmmForm::where('business_name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $content_writing_form = ContentWritingForm::where('company_name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $seo_form = SeoForm::where('company_name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $book_formatting_form = BookFormatting::where('book_title', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $book_writing_form = BookWriting::where('book_title', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $author_website_form = AuthorWebsite::where('author_name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $proofreading_form = Proofreading::where('description', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $bookcover_form = BookCover::where('title', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $no_form = NoForm::where('name', '!=' , '')->with('project')->doesntHave('project')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();

        //change
        $client_users_with_pending_projects = User::whereIn('id', get_project_client_user_ids())->when($request->has('user_id'), function ($q) use ($request) {
            return $q->where('id', $request->get('user_id'));
        })->get();

//        return view('admin.brief.fill', compact('client_users_with_pending_projects', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form', 'book_formatting_form', 'book_writing_form', 'author_website_form', 'proofreading_form', 'bookcover_form', 'no_form'));
        return view('admin.brief.fill', compact('client_users_with_pending_projects'));
    }

    public function getPendingProjectManager(Request $request){
//        $logo_form = LogoForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                        return $query->whereIn('brand', Auth::user()->brand_list());
//                    })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $web_form = WebForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                        return $query->whereIn('brand', Auth::user()->brand_list());
//                    })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $smm_form = SmmForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                        return $query->whereIn('brand', Auth::user()->brand_list());
//                    })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $content_writing_form = ContentWritingForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                                    return $query->whereIn('brand', Auth::user()->brand_list());
//                                })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $seo_form = SeoForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                        return $query->whereIn('brand', Auth::user()->brand_list());
//                    })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $book_formatting_form = BookFormatting::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                                    return $query->whereIn('brand', Auth::user()->brand_list());
//                                })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//        $book_writing_form = BookWriting::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $author_website_form = AuthorWebsite::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $proofreading_form = Proofreading::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $bookcover_form = BookCover::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//
//        $isbn_form = Isbnform::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $bookprinting_form = Bookprinting::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();
//
//        $no_form = NoForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//                        return $query->whereIn('brand', Auth::user()->brand_list());
//                    })->orderBy('id', 'desc')
//        ->when($request->has('user_id'), function ($q) use ($request) {
//            return $q->where('user_id', $request->get('user_id'));
//        })->get();

        //change
        $client_users_with_pending_projects = User::whereIn('id', get_project_client_user_ids())->when($request->has('user_id'), function ($q) use ($request) {
            return $q->where('id', $request->get('user_id'));
        })->get();
        
//        return view('manager.brief.fill', compact('client_users_with_pending_projects', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form', 'book_formatting_form', 'book_writing_form', 'author_website_form', 'no_form', 'proofreading_form', 'bookcover_form', 'isbn_form', 'bookprinting_form'));
        return view('manager.brief.fill', compact('client_users_with_pending_projects'));
    }

    public function  getPendingProjectbyIdManager($id, $form){
        if($form == 1){
            $logo_form = LogoForm::find($id);
            return view('manager.brief.logoform', compact('logo_form'));
        }elseif($form == 2){
            $web_form = WebForm::find($id);
            return view('manager.brief.webform', compact('web_form'));
        }elseif($form == 3){
            $smm_form = SmmForm::find($id);
            return view('manager.brief.smmform', compact('smm_form'));
        }elseif($form == 4){
            $content_form = ContentWritingForm::find($id);
            return view('manager.brief.contentform', compact('content_form'));
        }elseif($form == 5){
            $seo_form = SeoForm::find($id);
            return view('manager.brief.seoform', compact('seo_form'));
        }elseif($form == 6){
            $book_formatting_form = BookFormatting::find($id);
            return view('manager.brief.bookformattingform', compact('book_formatting_form'));
        }elseif($form == 7){
            $data = BookWriting::find($id);
            return view('manager.brief.bookwritingform', compact('data'));
        }elseif($form == 8){
            $data = AuthorWebsite::find($id);
            return view('manager.brief.authorwebsiteform', compact('data'));
        }elseif($form == 9){
            $data = Proofreading::find($id);
            return view('manager.brief.proofreadingform', compact('data'));
        }elseif($form == 10){
            $data = BookCover::find($id);
            return view('manager.brief.bookcoverform', compact('data'));
        }elseif($form == 11){
            $data = Isbnform::find($id);
            return view('manager.brief.isbnform', compact('data'));
        }elseif($form == 12){
            $data = Bookprinting::find($id);
            return view('manager.brief.bookprintingform', compact('data'));
        }
        
        
    }

    public function getPendingProjectbyId($id, $form){
        
        if($form == 1){
            $logo_form = LogoForm::find($id);
            return view('admin.brief.logoform', compact('logo_form'));
        }elseif($form == 2){
            $web_form = WebForm::find($id);
            return view('admin.brief.webform', compact('web_form'));
        }elseif($form == 3){
            $smm_form = SmmForm::find($id);
            return view('admin.brief.smmform', compact('smm_form'));
        }elseif($form == 4){
            $content_form = ContentWritingForm::find($id);
            return view('admin.brief.contentform', compact('content_form'));
        }elseif($form == 5){
            $seo_form = SeoForm::find($id);
            return view('admin.brief.seoform', compact('seo_form'));
        }elseif($form == 6){
            $data = BookFormatting::find($id);
            return view('admin.brief.bookformattingform', compact('data'));
        }elseif($form == 7){
            $data = BookWriting::find($id);
            return view('admin.brief.bookwritingform', compact('data'));
        }elseif($form == 8){
            $data = AuthorWebsite::find($id);
            return view('admin.brief.authorwebsiteform', compact('data'));
        }elseif($form == 9){
            $data = Proofreading::find($id);
            return view('admin.brief.proofreadingform', compact('data'));
        }elseif($form == 10){
            $data = BookCover::find($id);
            return view('admin.brief.bookcoverform', compact('data'));
        }elseif($form == 11){
            $data = Isbnform::find($id);  
            return view('manager.brief.isbnform', compact('data'));
        }elseif($form == 12){
            $data = Bookprinting::find($id);
            return view('manager.brief.bookprintingform', compact('data'));
        }
        
    }
    
}
