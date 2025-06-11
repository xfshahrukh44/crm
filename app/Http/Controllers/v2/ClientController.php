<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\BookMarketing;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Brand;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\Invoice;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\NewSMM;
use App\Models\PressReleaseForm;
use App\Models\Proofreading;
use App\Models\SeoBrief;
use App\Models\SeoForm;
use App\Models\Service;
use App\Models\SmmForm;
use App\Models\User;
use App\Models\WebForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();

        $clients = \App\Models\Client::orderBy('priority', 'ASC')
            ->orderBy('id', 'desc')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand_id', auth()->user()->brand_list());
            })
            ->when(!is_null(request()->get('start_date')), function ($q) {
                return $q->whereDate('created_at', '>=', request()->get('start_date'));
            })
            ->when(!is_null(request()->get('end_date')), function ($q) {
                return $q->whereDate('created_at', '<=', request()->get('end_date'));
            })
            ->when(request()->get('task_id') != '', function ($q) {
                return $q->where(function ($q) {
                    return $q->where(function ($q) {
                        return $q->whereHas('user', function ($q) {
                            return $q->whereHas('projects', function ($q) {
                                return $q->whereHas('tasks', function ($q) {
                                    return $q->where('id', request()->get('task_id'));
                                });
                            });
                        });
                    })->orWhere(function ($q) {
                        return $q->whereHas('projects', function ($q) {
                            return $q->whereHas('tasks', function ($q) {
                                return $q->where('id', request()->get('task_id'));
                            });
                        });
                    });
                });
            })
            ->when(request()->get('name') != '', function ($q) {
                return $q->where(\Illuminate\Support\Facades\DB::raw('concat(name," ",last_name)'), 'like', '%'.request()->get('name').'%')
                    ->orWhere('name', 'like', '%'.request()->get('name').'%')
                    ->orWhere('last_name', 'like', '%'.request()->get('name').'%');
            })
            ->when(request()->get('email') != '', function ($q) {
                return $q->where('email', 'LIKE', "%".request()->get('email')."%");
            })
            ->when(request()->get('brand') != '', function ($q) {
                return $q->where('brand_id', request()->get('brand'));
            })
            ->when(request()->get('status') != '', function ($q) {
                return $q->where('status', request()->get('status'));
            })->paginate(10);

        return view('v2.client.index', compact('brands', 'clients'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();

        return view('v2.client.create', compact('brands'));
    }

    public function store (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);

        //user check
        if (
            DB::table('users')->where('email', $request->email)->first() ||
            DB::table('clients')->where('email', $request->email)->first()
        ) { return redirect()->back()->with('error', 'Email already taken'); }

        //non admin checks
        if (!v2_acl([2])) {
            if (!in_array($request->get('brand_id'), auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $client = Client::create($request->except('_token'));

        //create user record
        DB::table('users')->insert([
            'name' => $client->name,
            'last_name' => $client->last_name,
            'email' => $client->contact,
            'contact' => $client->contact,
            'status' => 1,
            'password' => Hash::make('qwerty'),
            'is_employee' => 3,
            'client_id' => $client->id,
        ]);

        //create stripe customer
        create_clients_merchant_accounts($client->id);

        return redirect()->route('v2.invoices.create', $client->id)->with('success','Client created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$client = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        $brands = \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();

        return view('v2.client.edit', compact('client', 'brands'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$client = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        $request->validate([
            'name' => 'required',
            'brand_id' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:clients,email,'.$client->id,
//            'email' => 'required' . !is_null($client->user) ? ('|unique:users,email,'.$client->user->id) : '',
            'status' => 'required',
        ]);

        //non admin checks
        if (!v2_acl([2])) {
            if (!in_array($request->get('brand_id'), auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $client->update($request->all());

        return redirect()->route('v2.clients')->with('success','Client updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$client = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non admin checks
        if (!v2_acl([2])) {
            if (!in_array($client->brand_id, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $invoices = \App\Models\Invoice::where('client_id', $client->id)->orderBy('created_at', 'DESC')->get();
        $client_user = \App\Models\User::where('client_id', $client->id)->first();
        $projects = $client_user ? $client_user->recent_projects : [];
        $pending_projects = $client->user ? get_pending_projects($client->user->id) : [];
        $briefs_pendings = $client->user ? get_briefs_pending($client->user->id) : [];

        return view('v2.client.show', compact('client', 'invoices', 'projects', 'pending_projects', 'briefs_pendings'));
    }

    public function createAuth(Request $request){
        if (!v2_acl([2, 6, 0, 4])) {
            return response()->json(['success' => false , 'message' => 'Access denied.']);
        }

        try {
            $invoices = Invoice::where('client_id', $request->id)->get();
            $pass = $request->pass;
            $id = $request->id;
            $client = Client::find($id);
            $user = new User();
            $user->name = $client->name;
            $user->last_name = $client->last_name;
            $user->email = $client->email;
            $user->contact = $client->contact;
            $user->status = 1;
            $user->password = Hash::make($pass);
            $user->is_employee = 3;
            $user->client_id = $id;
            $user->save();
            $client->user_id = $user->id;
            $client->save();

            foreach($invoices as $invoice){
                $service_array = explode(',', $invoice->service);
                for($i = 0; $i < count($service_array); $i++){
                    $service = Service::find($service_array[$i]);
                    if($service->form == 1){
                        // Logo Form
                        $logo_form = new LogoForm();
                        $logo_form->invoice_id = $invoice->id;
                        $logo_form->user_id = $user->id;
                        $logo_form->agent_id = $invoice->sales_agent_id;
                        $logo_form->save();
                    }elseif($service->form == 2){
                        // Website Form
                        $web_form = new WebForm();
                        $web_form->invoice_id = $invoice->id;
                        $web_form->user_id = $user->id;
                        $web_form->agent_id = $invoice->sales_agent_id;
                        $web_form->save();
                    }elseif($service->form == 3){
                        // Smm Form
                        $smm_form = new SmmForm();
                        $smm_form->invoice_id = $invoice->id;
                        $smm_form->user_id = $user->id;
                        $smm_form->agent_id = $invoice->sales_agent_id;
                        $smm_form->save();
                    }elseif($service->form == 4){
                        // Content Writing Form
                        $content_writing_form = new ContentWritingForm();
                        $content_writing_form->invoice_id = $invoice->id;
                        $content_writing_form->user_id = $user->id;
                        $content_writing_form->agent_id = $invoice->sales_agent_id;
                        $content_writing_form->save();
                    }elseif($service->form == 5){
                        // Search Engine Optimization Form
                        $seo_form = new SeoForm();
                        $seo_form->invoice_id = $invoice->id;
                        $seo_form->user_id = $user->id;
                        $seo_form->agent_id = $invoice->sales_agent_id;
                        $seo_form->save();
                    } elseif($service->form == 6){
                        if($invoice->createform == 1){
                            // Book Formatting & Publishing Form
                            $book_formatting_form = new BookFormatting();
                            $book_formatting_form->invoice_id = $invoice->id;
                            $book_formatting_form->user_id = $user->id;
                            $book_formatting_form->agent_id = $invoice->sales_agent_id;
                            $book_formatting_form->save();
                        }
                    }elseif($service->form == 7){
                        if($invoice->createform == 1){
                            // Book Writing Form
                            $book_writing_form = new BookWriting();
                            $book_writing_form->invoice_id = $invoice->id;
                            $book_writing_form->user_id = $user->id;
                            $book_writing_form->agent_id = $invoice->sales_agent_id;
                            $book_writing_form->save();
                        }
                    }elseif($service->form == 8){
                        if($invoice->createform == 1){
                            // AuthorWebsite Form
                            $author_website_form = new AuthorWebsite();
                            $author_website_form->invoice_id = $invoice->id;
                            $author_website_form->user_id = $user->id;
                            $author_website_form->agent_id = $invoice->sales_agent_id;
                            $author_website_form->save();
                        }
                    }elseif($service->form == 9){
                        if($invoice->createform == 1){
                            // Proofreading Form
                            $proofreading_form = new Proofreading();
                            $proofreading_form->invoice_id = $invoice->id;
                            $proofreading_form->user_id = $user->id;
                            $proofreading_form->agent_id = $invoice->sales_agent_id;
                            $proofreading_form->save();
                        }
                    }elseif($service->form == 10){
                        if($invoice->createform == 1){
                            // BookCover Form
                            $bookcover_form = new BookCover();
                            $bookcover_form->invoice_id = $invoice->id;
                            $bookcover_form->user_id = $user->id;
                            $bookcover_form->agent_id = $invoice->sales_agent_id;
                            $bookcover_form->save();
                        }
                    }elseif($service->form == 11){
                        if($invoice->createform == 1){
                            // BookCover Form
                            $isbn_form = new Isbnform();
                            $isbn_form->invoice_id = $invoice->id;
                            $isbn_form->user_id = $user->id;
                            $isbn_form->agent_id = $invoice->sales_agent_id;
                            $isbn_form->save();
                        }
                    }elseif($service->form == 12){
                        if($invoice->createform == 1){
                            // BookCover Form
                            $bookprinting_form = new Bookprinting();
                            $bookprinting_form->invoice_id = $invoice->id;
                            $bookprinting_form->user_id = $user->id;
                            $bookprinting_form->agent_id = $invoice->sales_agent_id;
                            $bookprinting_form->save();
                        }
                    }elseif($service->form == 13){
                        if($invoice->createform == 1){
                            $seo_form = new SeoBrief();
                            $seo_form->invoice_id = $invoice->id;
                            $seo_form->user_id = $user->id;
                            $seo_form->agent_id = $invoice->sales_agent_id;
                            $seo_form->save();
                        }
                    }elseif($service->form == 14){
                        if($invoice->createform == 1){
                            $book_marketing_form = new BookMarketing();
                            $book_marketing_form->invoice_id = $invoice->id;
                            $book_marketing_form->user_id = $user->id;
                            $book_marketing_form->agent_id = $invoice->sales_agent_id;
                            $book_marketing_form->save();
                        }
                    }elseif($service->form == 15){
                        if($invoice->createform == 1){
                            $new_smm_form = new NewSMM();
                            $new_smm_form->invoice_id = $invoice->id;
                            $new_smm_form->user_id = $user->id;
                            $new_smm_form->agent_id = $invoice->sales_agent_id;
                            $new_smm_form->save();
                        }
                    }elseif($service->form == 16){
                        if($invoice->createform == 1){
                            $new_smm_form = new PressReleaseForm();
                            $new_smm_form->invoice_id = $invoice->id;
                            $new_smm_form->user_id = $user->id;
                            $new_smm_form->agent_id = $invoice->sales_agent_id;
                            $new_smm_form->save();
                        }
                    }


                }
            }

            $client = Client::find($request->id);

            //mail_notification
            $brand = Brand::find($client->brand_id);

            $html = '<p>'. 'Dear ' . $client->name . ',' .'</p>';
            $html .= '<p>'. 'Welcome to '.$brand->name.'! We are excited to have you on board.' .'</p>';
            $html .= '<p>'. 'Your account has been successfully created. Below are your login credentials and some basic instructions to help you get started:' .'</p>';
            $html .= '<p><ul>'. '<li><strong>*Username: '.$client->email.'</strong></li><li><strong>*Password: '.$pass.'</strong></li>' .'</ul></p>';
            $html .= '<p>'. 'For your security, please change your password upon your first login. You can access your account here: <a href="'.route('login').'">'.route('login').'</a>' .'</p>';
            $html .= '<p>'. 'If you have any questions or need further assistance, please do not hesitate to contact our support team.' .'</p>';
            $html .= '<p>'. 'Welcome aboard!' .'</p>';
            $html .= '<p>'. 'Best Regards,' .'</p>';
            $html .= '<p>'. $brand->name .'.</p>';

            mail_notification(
                '',
                [$client->email],
                'Welcome to '.$brand->name.' – Your Account is Ready!',
                view('mail.crm-mail-template')->with([
                    'subject' => 'Welcome to '.$brand->name.' – Your Account is Ready!',
                    'brand_name' => $brand->name,
                    'brand_logo' => asset($brand->logo),
                    'additional_html' => $html
                ]),
//            true
            );

            return response()->json(['success' => true , 'message' => 'Login Created']);
        } catch (\Exception $e) {
            return response()->json(['success' => false , 'message' => $e->getMessage()]);
        }
    }

    public function updateAuth(Request $request){
        if (!v2_acl([2, 6, 0, 4])) {
            return response()->json(['success' => false , 'message' => 'Access denied.']);
        }

        try {
            $id = $request->id;
            $pass = $request->pass;
            $user = User::where('client_id', $id)->first();
            $user->password = Hash::make($pass);
            $user->save();

            $client = Client::find($id);

            //mail_notification
            $brand = Brand::find($client->brand_id);

            $html = '<p>'. 'Dear ' . $client->name . ',' .'</p>';
            $html .= '<p>'. 'Your account credentials have been reset. Below are your login credentials:' .'</p>';
            $html .= '<p><ul>'. '<li><strong>*Username: '.$client->email.'</strong></li><li><strong>*Password: '.$pass.'</strong></li>' .'</ul></p>';
            $html .= '<p>'. 'You can access your account here: <a href="'.route('login').'">'.route('login').'</a>' .'</p>';
            $html .= '<p>'. 'If you have any questions or need further assistance, please do not hesitate to contact our support team.' .'</p>';
            $html .= '<p>'. 'Best Regards,' .'</p>';
            $html .= '<p>'. $brand->name .'.</p>';

            mail_notification(
                '',
                [$client->email],
                'CRM | Password reset',
                view('mail.crm-mail-template')->with([
                    'subject' => 'CRM | Password reset',
                    'brand_name' => $brand->name,
                    'brand_logo' => asset($brand->logo),
                    'additional_html' => $html
                ]),
//            true
            );
            return response()->json(['success' => true , 'message' => 'Password Reset']);
        } catch (\Exception $e) {
            return response()->json(['success' => false , 'message' => $e->getMessage()]);
        }
    }
}
