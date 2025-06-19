<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\BookMarketing;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\Invoice;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\NewSMM;
use App\Models\NoForm;
use App\Models\PressReleaseForm;
use App\Models\Project;
use App\Models\Proofreading;
use App\Models\SeoBrief;
use App\Models\SeoForm;
use App\Models\Service;
use App\Models\SmmForm;
use App\Models\User;
use App\Models\WebForm;
use App\Notifications\AssignProjectNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 6, 4])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = $this->getBrands();
        $categories = \App\Models\Category::all();
        //restricted brand access
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true); // Ensure it's an array

        $projects = \App\Models\Project::whereHas('client')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand_id', auth()->user()->brand_list());
            })
            ->when(user_is_cs(), function ($q) {
                return $q->where('user_id', auth()->id());
            })
            ->when(!is_null(request()->get('start_date')) && request()->get('start_date') != '', function ($q) {
                return $q->whereDate('created_at', '>=', request()->get('start_date'));
            })
            ->when(!is_null(request()->get('end_date')) && request()->get('end_date') != '', function ($q) {
                return $q->whereDate('created_at', '<=', request()->get('end_date'));
            })
            ->when(request()->get('brand') != null && request()->get('brand') != '', function ($q) {
                return $q->where('brand_id', request()->get('brand'));
            })
            ->when(request()->get('client') != null && request()->get('client') != '', function ($q) {
                $name = request()->get('client');
                return $q->whereHas('client', function ($query) use ($name){
                    return $query->where('name', 'LIKE', "%{$name}%")
                        ->orWhere('last_name', 'LIKE', "%{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%");
                });
            })
            ->when(request()->get('user') != null && request()->get('user') != '', function ($q) {
                $name = request()->get('user');
                return $q->whereHas('added_by', function ($query) use ($name){
                    return $query->where('name', 'LIKE', "%{$name}%")
                        ->orWhere('last_name', 'LIKE', "%{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%");
                });
            })
            ->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand_id', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand_id', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            })->orderBy('id', 'desc')->paginate(20);

        return view('v2.project.index', compact('projects', 'brands', 'categories'));
    }

    public function create (Request $request)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        return view('v2.project.create');
    }

    public function store (Request $request)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        $request->validate([
//            'name' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|email|unique:projects,email',
//            'status' => 'required',
//            'brand_id' => 'required',
//        ]);
//
//        //user check
//        if (
//            DB::table('users')->where('email', $request->email)->first() ||
//            DB::table('projects')->where('email', $request->email)->first()
//        ) { return redirect()->back()->with('error', 'Email already taken'); }
//
//        $project = Project::create($request->except('_token'));
//
//        //create user record
//        DB::table('users')->insert([
//            'name' => $project->name,
//            'last_name' => $project->last_name,
//            'email' => $project->contact,
//            'contact' => $project->contact,
//            'status' => 1,
//            'password' => Hash::make('qwerty'),
//            'is_employee' => 3,
//            'project_id' => $project->id,
//        ]);
//
//        //create stripe customer
//        create_clients_merchant_accounts($project->id);
//
//        return redirect()->route('v2.invoices.create', $project->id)->with('success','Project created Successfully.');
    }

    public function edit (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$project = Project::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.project.edit', compact('project'));
    }

    public function update (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$project = Project::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        $request->validate([
//            'name' => 'required',
//            'brand_id' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|unique:projects,email,'.$project->id,
////            'email' => 'required' . !is_null($project->user) ? ('|unique:users,email,'.$project->user->id) : '',
//            'status' => 'required',
//        ]);
//
//        $project->update($request->all());
//
//        return redirect()->route('v2.projects')->with('success','Project updated Successfully.');
    }

    public function assign (Request $request)
    {
        if (user_is_cs() || !v2_acl([2, 6, 4])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $form_id  = $request->id;
        $agent_id  = $request->agent_id;
        $form_checker  = $request->form;
        $name = '';
        $client_id = 0;
        $brand_id = 0;
        $description = '';
        if($form_checker == 0){
            $no_form = NoForm::find($form_id);
            $no_form_invoice = Invoice::find($no_form->invoice_id);
            $client = Client::find($no_form_invoice->client_id);
            $service = Service::find(explode(',', $no_form_invoice->service)[0]);
            $name = $client->name . ' ' . $client->last_name . ' - ' . $service->name;
//            if($no_form->name != null){
//                $name = $no_form->name . ' - OTHER';
//            }else{
//                $name = $no_form->name . ' - OTHER';
//            }
            $client_id = $no_form->user->id;
            $brand_id = $no_form->invoice->brand;
            $description = $no_form->business;

        }elseif($form_checker == 1){
            // Logo form
            $logo_form = LogoForm::find($form_id);
            if($logo_form->logo_name != null){
                $name = $logo_form->logo_name . ' - LOGO';
            }else{
                $name = $logo_form->user->name . ' - LOGO';
            }
            $client_id = $logo_form->user->id;
            $brand_id = $logo_form->invoice->brand;
            $description = $logo_form->business;
        }elseif($form_checker == 2){
            // Web form
            $web_form = WebForm::find($form_id);
            if($web_form->business_name != null){
                $name = $web_form->business_name . ' - WEBSITE';
            }else{
                $name = $web_form->user->name . ' - WEBSITE';
            }
            $client_id = $web_form->user->id;
            $brand_id = $web_form->invoice->brand;
            $description = $web_form->about_companys;
        }elseif($form_checker == 3){
            // Social Media Marketing Form
            $smm_form = SmmForm::find($form_id);
            if($smm_form->business_name != null){
                $name = $smm_form->business_name . ' - SMM';
            }else{
                $name = $smm_form->user->name . ' - SMM';
            }
            $client_id = $smm_form->user->id;
            $brand_id = $smm_form->invoice->brand;
            $description = $smm_form->business_category;
        }elseif($form_checker == 4){
            // Content Writing Form
            $content_form = ContentWritingForm::find($form_id);
            if($content_form->company_name != null){
                $name = $content_form->company_name . ' - CONTENT WRITING';
            }else{
                $name = $content_form->user->name . ' - CONTENT WRITING';
            }
            $client_id = $content_form->user->id;
            $brand_id = $content_form->invoice->brand;
            $description = $content_form->company_details;
        }elseif($form_checker == 5){
            // Search Engine Optimization Form
            $seo_form = SeoForm::find($form_id);
            if($seo_form->company_name != null){
                $name = $seo_form->company_name . ' - SEO';
            }else{
                $name = $seo_form->user->name . ' - SEO';
            }
            $client_id = $seo_form->user->id;
            $brand_id = $seo_form->invoice->brand;
            $description = $seo_form->top_goals;
        }elseif($form_checker == 6){
            // Book Formatting & Publishing Form
            $book_formatting_form = BookFormatting::find($form_id);
            if($book_formatting_form->book_title != null){
                $name = $book_formatting_form->book_title . ' - Book Formatting & Publishing';
            }else{
                $name = $book_formatting_form->user->name . ' - Book Formatting & Publishing';
            }
            $client_id = $book_formatting_form->user->id;
            $brand_id = $book_formatting_form->invoice->brand;
            $description = $book_formatting_form->additional_instructions;
        }elseif($form_checker == 7){
            // Book Writing Form
            $book_writing_form = BookWriting::find($form_id);
            if($book_writing_form->book_title != null){
                $name = $book_writing_form->book_title . ' - Book Writing';
            }else{
                $name = $book_writing_form->user->name . ' - Book Writing';
            }
            $client_id = $book_writing_form->user->id;
            $brand_id = $book_writing_form->invoice->brand;
            $description = $book_writing_form->brief_summary;
        }elseif($form_checker == 8){
            // Author Website Form
            $author_website_form = AuthorWebsite::find($form_id);
            if($author_website_form->author_name != null){
                $name = $author_website_form->author_name . ' - Author Website';
            }else{
                $name = $author_website_form->user->name . ' - Author Website';
            }
            $client_id = $author_website_form->user->id;
            $brand_id = $author_website_form->invoice->brand;
            $description = $author_website_form->brief_overview;
        }elseif($form_checker == 9){
            // Editing & Proofreading Form
            $proofreading_form = Proofreading::find($form_id);
            if($proofreading_form->author_name != null){
                $name = $proofreading_form->description . ' - Editing & Proofreading';
            }else{
                $name = $proofreading_form->user->name . ' - Editing & Proofreading';
            }
            $client_id = $proofreading_form->user->id;
            $brand_id = $proofreading_form->invoice->brand;
            $description = $proofreading_form->guide;
        }elseif($form_checker == 10){
            // Cover Design Form
            $bookcover_form = BookCover::find($form_id);
            if($bookcover_form->author_name != null){
                $name = $bookcover_form->title . ' - Cover Design';
            }else{
                $name = $bookcover_form->user->name . ' - Cover Design';
            }
            $client_id = $bookcover_form->user->id;
            $brand_id = $bookcover_form->invoice->brand;
            $description = $bookcover_form->information;
        }
        elseif($form_checker == 11){
            // Cover Design Form
            $isbn_form = Isbnform::find($form_id);
            if($isbn_form->author_name != null){
                $name = $isbn_form->title . ' - ISBN Form';
            }else{
                $name = $isbn_form->user->name . ' - ISBN Form';
            }
            $client_id = $isbn_form->user->id;
            $brand_id = $isbn_form->invoice->brand;
            $description = $isbn_form->information;
        }
        elseif($form_checker == 12){
            // Cover Design Form
            $bookprinting_form = Bookprinting::find($form_id);
            if($bookprinting_form->author_name != null){
                $name = $bookprinting_form->title . ' - Book Printing Form';
            }else{
                $name = $bookprinting_form->user->name . ' - Book Printing Form';
            }
            $client_id = $bookprinting_form->user->id;
            $brand_id = $bookprinting_form->invoice->brand;
            $description = $bookprinting_form->information;
        }elseif($form_checker == 13){
            // Search Engine Optimization Form
            $seo_form = SeoBrief::find($form_id);
            if($seo_form->company_name != null){
                $name = $seo_form->company_name . ' - SEO';
            }else{
                $name = $seo_form->user->name . ' - SEO';
            }
            $client_id = $seo_form->user->id;
            $brand_id = $seo_form->invoice->brand;
            $description = $seo_form->company_name;
        }elseif($form_checker == 14){
            $book_marketing_form = BookMarketing::find($form_id);
            if($book_marketing_form->company_name != null){
                $name = $book_marketing_form->company_name . ' - Book Marketing';
            }else{
                $name = $book_marketing_form->user->name . ' - Book Marketing';
            }
            $client_id = $book_marketing_form->user->id;
            $brand_id = $book_marketing_form->invoice->brand;
            $description = $book_marketing_form->company_name;
        }elseif($form_checker == 15){
            $new_smm_form = NewSMM::find($form_id);
            if($new_smm_form->client_name != null){
                $name = $new_smm_form->client_name . ' - SMM(new)';
            }else{
                $name = $new_smm_form->user->name . ' - SMM(new)';
            }
            $client_id = $new_smm_form->user->id;
            $brand_id = $new_smm_form->invoice->brand;
            $description = $new_smm_form->client_name;

        }elseif($form_checker == 16){
            $press_release_form = PressReleaseForm::find($form_id);
            if($press_release_form->book_title != null){
                $name = $press_release_form->book_title . ' - Press Release';
            }else{
                $name = $press_release_form->user->name . ' - Press Release';
            }
            $client_id = $press_release_form->user->id;
            $brand_id = $press_release_form->invoice->brand;
            $description = $press_release_form->book_title;

        }

        $project = new Project();
        $project->name = $name;
        $project->description = $description;
        $project->status = 1;
        $project->user_id = $agent_id;
        $project->client_id = $client_id;
        $project->brand_id = $brand_id;
        $project->form_id = $form_id;
        $project->form_checker = $form_checker;
        $project->save();
        $assignData = [
            'id' => Auth()->user()->id,
            'project_id' => $project->id,
            'name' => Auth()->user()->name . ' ' . Auth()->user()->last_name,
            'text' => $project->name . ' has assign. ('.Auth()->user()->name.')',
            'url' => '',
        ];
        $user = User::find($agent_id);
        $user->notify(new AssignProjectNotification($assignData));

        //mail_notification
        $html = '<p>'.'New project `'.$project->name.'`'.'</p><br />';
        $html .= '<strong>Assigned by:</strong> <span>'.auth()->user()->name.'</span><br />';
        $html .= '<strong>Assigned to:</strong> <span>'.$user->name.' ('.$user->email.')'.'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';

        mail_notification(
            '',
            [$user->email],
            'New project',
            view('mail.crm-mail-template')->with([
                'subject' => 'New project',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return redirect()->back()->with('success', $project->name . ' Assigned Successfully');
    }

    public function reassign (Request $request)
    {
        if (user_is_cs() || !v2_acl([2, 6, 4])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $project = Project::find($request->id);
        $project->user_id = $request->agent_id;
        $project->save();

        //mail_notification
        $user = User::find($request->agent_id);
        $html = '<p>'.'Project `'.$project->name.'` has been reassigned.'.'</p><br />';
        $html .= '<strong>Reassigned by:</strong> <span>'.auth()->user()->name.'</span><br />';
        $html .= '<strong>Reassigned to:</strong> <span>'.$user->name.' ('.$user->email.') '.'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';

//        mail_notification('', [$user->email], 'CRM | Project assignment', $html, true);
        mail_notification(
            '',
            [$user->email],
            'Project assignment',
            view('mail.crm-mail-template')->with([
                'subject' => 'Project assignment',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return redirect()->back()->with('success', $project->name . ' Reassigned Successfully');
    }

    public function show (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$project = Project::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.project.show', compact('project'));
    }

//    public function viewForm($form_id, $check, $id){
    public function viewForm($form_id, $check){
        if (!v2_acl([2, 6, 4, 1, 5])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

//        if(!$project = Project::find($id)) {
//            return redirect()->back();
//        }

        // if($project->user_id == Auth()->user()->id){
        if($check == 1){
            $logo_form = LogoForm::find($form_id);
            return view('v2.brief.logoform', compact('logo_form'));
        }else if($check == 2){
            $web_form = WebForm::find($form_id);
            return view('v2.brief.webform', compact('web_form'));
        }elseif($check == 3){
            $smm_form = SmmForm::find($form_id);
            return view('v2.brief.smmform', compact('smm_form'));
        }elseif($check == 4){
            $content_form = ContentWritingForm::find($form_id);
            return view('v2.brief.contentform', compact('content_form'));
        }elseif($check == 5){
            $seo_form = SeoForm::find($form_id);
            return view('v2.brief.seoform', compact('seo_form'));
        }elseif($check == 6){
            $data = BookFormatting::find($form_id);
            return view('v2.brief.bookprintingform', compact('data'));
        }elseif($check == 7){
            $data = BookWriting::find($form_id);
            return view('v2.brief.bookwritingform', compact('data'));
        }elseif($check == 8){
            $data = AuthorWebsite::find($form_id);
            return view('v2.brief.authorwesbiteform', compact('data'));
        }elseif($check == 9){
            $data = Proofreading::find($form_id);
            return view('v2.brief.proofreadingform', compact('data'));
        }elseif($check == 10){
            $data = BookCover::find($form_id);
            return view('v2.brief.bookcoverform', compact('data'));
        }
        elseif($check == 11){
            $data = Isbnform::find($form_id);
            return view('v2.brief.isbnform', compact('data'));
        }
        elseif($check == 12){
            $data = Bookprinting::find($form_id);
            return view('v2.brief.bookprinting', compact('data'));
        }
        elseif($check == 13){
            $data = SeoBrief::find($form_id);
            return view('v2.brief.seoform', compact('data'));
        }
        elseif($check == 14){
            $book_marketing_form = BookMarketing::find($form_id);
            return view('v2.brief.book-marketing', compact('book_marketing_form'));
        }
        elseif($check == 15){
            $data = Bookprinting::find($form_id);
            return view('v2.brief.bookprinting', compact('data'));
        }
        elseif($check == 16){
            $data = PressReleaseForm::find($form_id);
            return view('v2.brief.press-release-form', compact('data'));
        }


        // }else{
        //     return redirect()->back();
        // }
    }

    public function updateComments (Request $request)
    {
        if (user_is_cs() || !v2_acl([2, 6, 4])) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Access denied.',
                'errors' => [],
            ]);
        }

        DB::table('projects')->where('id', $request->rec_id)->update([
            'comments' => $request->comments ?? '',
            'comments_id' => auth()->id(),
            'comments_timestamp' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Comments added!',
            'errors' => [],
        ]);
    }

    public function getBrands ()
    {
        return \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();
    }
}
