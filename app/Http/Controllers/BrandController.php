<?php

namespace App\Http\Controllers;

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\BookMarketing;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Brand;
use App\Models\Category;
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
use App\Models\Task;
use App\Models\User;
use App\Models\WebForm;
use App\Notifications\AssignProjectNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    protected $layout;

    public function construct()
    {
        if (!Auth::check() || !in_array(Auth::user()->is_employee, [2, 6, 4, 0])) {
            return false;
        }

        $layout_map = [
            2 => 'layouts.app-admin',
            6 => 'layouts.app-manager',
            0 => 'layouts.app-sale',
            4 => 'layouts.app-support',
        ];

        $this->layout = $layout_map[Auth::user()->is_employee];

        return true;
    }

    public function brands_dashboard (Request $request)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $brands = Brand::
//        whereHas('projects', function ($q) {
//            return $q->whereHas('tasks', function ($q) {
//                return $q->orderBy('created_at', 'DESC');
//            })->orderBy('created_at', 'DESC');
//        })
        when(in_array(Auth::user()->is_employee, [6, 4, 0]), function ($q) {
            return $q->whereIn('id', Auth::user()->brand_list());
        })
        ->when($request->has('brand_name'), function ($q) use ($request) {
            return $q->where(function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('url', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('phone', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('phone_tel', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('email', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('address', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('address_link', 'LIKE', '%'.$request->get('brand_name').'%');
            });
        })
        ->orderBy('created_at', 'DESC')
        ->paginate(30);

        return view('brand-dashboard', compact('brands'))->with(['layout' => $this->layout]);
    }

    public function brands_detail (Request $request, $id)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $projects = Project::where('brand_id', $id);
        $total_projects_count = $projects->count();
        $completed_projects_count = 0;
        foreach ($projects->get() as $project) {
            $completed_projects_count += no_pending_tasks_left($project->id) ? 1 : 0;
        }

//        $brand= Brand::with('clients')->find($id);
        $brand= Brand::find($id);
        $clients = Client::where('brand_id', $id)
            ->withCount('projects')->withCount('invoices')
            ->orderBy('created_at', 'DESC')
//            ->orderBy('projects_count', 'desc')->orderBy('invoices_count', 'desc')
            ->when($request->has('client_name'), function ($q) use ($request) {
                return $q->whereHas('user', function ($q) use ($request) {
                    return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$request->get('client_name').'%')
                        ->orWhere('name', 'LIKE', '%'.$request->get('client_name').'%')
                        ->orWhere('last_name', 'LIKE', '%'.$request->get('client_name').'%')
                        ->orWhere('email', 'LIKE', '%'.$request->get('client_name').'%')
                        ->orWhere('contact', 'LIKE', '%'.$request->get('client_name').'%');
                });
            })
            ->paginate(10);

        $brand_user_ids = DB::table('brand_users')->where('brand_id', $id)->pluck('user_id')->toArray();

        $buhs = User::whereIn('id', $brand_user_ids)->where('is_employee', 6)->get();
        $support_heads = User::whereIn('id', $brand_user_ids)->where('is_employee', 4)->where('is_support_head', 1)->get();
        $customer_supports = User::whereIn('id', $brand_user_ids)->where('is_employee', 4)->where('is_support_head', 0)->get();
        $agents = User::whereIn('id', $brand_user_ids)->where('is_employee', 0)->get();

        return view('brand-detail', compact('brand', 'clients', 'buhs', 'support_heads', 'customer_supports', 'agents', 'total_projects_count', 'completed_projects_count'))->with(['layout' => $this->layout]);
    }

    public function clients_detail (Request $request, $id)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $client= Client::find($id);
        $client_user = \App\Models\User::where('client_id', $client->id)->first();
        $projects = $client_user ? $client_user->recent_projects : [];

        return view('client-detail', compact('client', 'projects'))->with(['layout' => $this->layout]);
    }

    public function projects_detail (Request $request, $id)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $project= Project::find($id);

//        $category_ids_from_tasks = array_unique(Task::where('project_id', $id)->where('status', '!=', 3)->pluck('category_id')->toArray());
        $category_ids_from_tasks = array_unique(Task::where('project_id', $id)->pluck('category_id')->toArray());

        $categories_with_active_tasks = [];
        foreach ($category_ids_from_tasks as $category_id_from_tasks) {
            if (Auth::user()->is_employee == 2) {
//                $tasks = Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])->where('status', '!=', 3)->get();
                $tasks = Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])
                    ->orderBy('created_at', 'DESC')->get();
            } else {
                $tasks = Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])
//                    ->where('status', '!=', 3)
                    ->whereIn('brand_id', Auth::user()->brand_list())
                    ->orderBy('created_at', 'DESC')->get();
            }
            $categories_with_active_tasks []= [
                'category' => Category::find($category_id_from_tasks),
                'tasks' => $tasks,
            ];
        }

        return view('project-detail', compact('project', 'categories_with_active_tasks'))->with(['layout' => $this->layout]);
    }

    public function get_invoices (Request $request)
    {
        $invoices = Invoice::with('currency_show', 'sale', 'brands')->where('payment_status', 2)
            ->when($request->has('brand'), function ($q) use ($request) {
                return $q->where('brand', $request->get('brand'));
            })->when($request->has('start_date'), function ($q) use ($request) {
                return $q->where('updated_at', '>=', Carbon::parse($request->get('start_date')));
            })->when($request->has('end_date'), function ($q) use ($request) {
                return $q->where('updated_at', '<=', Carbon::parse($request->get('end_date')));
            })->get();

        return $invoices;
    }

    public function get_support_agents (Request $request)
    {
        $user = User::select('id', 'name', 'last_name')
            ->where('id', '!=', auth()->id())
            ->where('is_employee', 4)
            ->whereHas('brands', function ($query) use ($request) {
                return $query->where('brand_id', $request->brand_id);
            })->get()->toArray();

        if (auth()->user()->is_employee != 2) {
            $user []= auth()->user();
        }

        return response()->json(['success' => true , 'data' => $user]);
    }

    public function assign_pending_project_to_agent (Request $request)
    {
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
        $html .= '<strong>Assigned by:</strong> <span>'.Auth::user()->name.'</span><br />';
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

        return redirect()->back()->with('success', $user->name . ' ' . $user->last_name . ' Assigned Successfully');
    }

    public function fetch_search_bar_content (Request $request)
    {
        return fetch_search_bar_content($request->get('query'));
    }

    public function check_if_external_client (Request $request)
    {
        return check_if_external_client($request, (request()->has('v2') && request()->get('v2') == 'true'));
    }

    public function clear_notification (Request $request)
    {
        $res = clear_notification($request->notification_id);

        return response()->json([
            'success' => $res,
            'data' => [],
            'message' => $res ? 'Notification cleared!' : 'Failed to clear notification.',
            'errors' => [],
        ]);
    }

}
