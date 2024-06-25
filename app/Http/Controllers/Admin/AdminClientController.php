<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Brand;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\LogoForm;
use App\Models\NoForm;
use App\Models\WebForm;
use App\Models\SmmForm;
use App\Models\BookFormatting;
use App\Models\BookWriting;
use App\Models\ContentWritingForm;
use App\Models\SeoForm;
use App\Models\AuthorWebsite;
use App\Models\Proofreading;
use App\Models\BookCover;
use App\Models\Project;
use App\Models\Task;
use App\Models\Isbnform;
use App\Models\Bookprinting;
use Hash;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Notifications\AssignProjectNotification;

class AdminClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = new Client;
        $data = $data->orderBy('id', 'desc');
        if($request->name != ''){
            $data = $data->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$request->name.'%');
        }
        if($request->email != ''){
            $data = $data->where('email', 'LIKE', "%$request->email%");
        }
        if($request->brand != ''){
            $data = $data->where('brand_id', $request->brand);
        }
        if($request->status != ''){
            $data = $data->where('status', $request->status);
        }
        $data = $data->paginate(20);
        $brands = DB::table('brands')->get();
        return view('admin.client.index', compact('data', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::all();
        return view('admin.client.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);
        $request->request->add(['user_id' => auth()->user()->id]);
        Client::create($request->all());
        return redirect()->back()->with('success', 'Client created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */

    public function showNotification(Client $client, $id){
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        $notification->markAsRead();
        return view('admin.client.show', compact('client'));
    }

    public function show(Client $client)
    {
        return view('admin.client.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Client::find($id);
        $brands = Brand::all();
        return view('admin.client.edit', compact('data', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'brand_id' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:clients,email,'.$client->id,
//            'email' => 'required' . !is_null($client->user) ? ('|unique:users,email,'.$client->user->id) : '',
            'status' => 'required',
        ]);
        $client->update($request->all());
        return redirect()->back()->with('success', 'Client Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->back()->with('success', 'Client Deleted Successfully.');
    }

    public function paymentLink($id){
        $user = Client::find($id);
        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
        return view('admin.payment.create', compact('user', 'brand'));
    }

    public function createAuthManager(Request $request){
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
        foreach($invoices as $invoice){
            $service_array = explode(',', $invoice->service);
            for($i = 0; $i < count($service_array); $i++){
                $service = Service::find($service_array[$i]);
                if($service->form == 0){
                    if($invoice->createform == 1){
                        $no_form = new NoForm();
                        $no_form->invoice_id = $invoice->id;
                        $no_form->user_id = $user->id;
                        $no_form->agent_id = $invoice->sales_agent_id;
                        $no_form->save();
                    }
                }elseif($service->form == 1){
                    if($invoice->createform == 1){
                        // Logo Form
                        $logo_form = new LogoForm();
                        $logo_form->invoice_id = $invoice->id;
                        $logo_form->user_id = $user->id;
                        $logo_form->agent_id = $invoice->sales_agent_id;
                        $logo_form->save();
                    }
                }elseif($service->form == 2){
                    if($invoice->createform == 1){
                        // Website Form
                        $web_form = new WebForm();
                        $web_form->invoice_id = $invoice->id;
                        $web_form->user_id = $user->id;
                        $web_form->agent_id = $invoice->sales_agent_id;
                        $web_form->save();
                    }
                }elseif($service->form == 3){
                    if($invoice->createform == 1){
                        // Smm Form
                        $smm_form = new SmmForm();
                        $smm_form->invoice_id = $invoice->id;
                        $smm_form->user_id = $user->id;
                        $smm_form->agent_id = $invoice->sales_agent_id;
                        $smm_form->save();
                    }
                }elseif($service->form == 4){
                    if($invoice->createform == 1){
                        // Content Writing Form
                        $content_writing_form = new ContentWritingForm();
                        $content_writing_form->invoice_id = $invoice->id;
                        $content_writing_form->user_id = $user->id;
                        $content_writing_form->agent_id = $invoice->sales_agent_id;
                        $content_writing_form->save();
                    }
                }elseif($service->form == 5){
                    if($invoice->createform == 1){
                        // Search Engine Optimization Form
                        $seo_form = new SeoForm();
                        $seo_form->invoice_id = $invoice->id;
                        $seo_form->user_id = $user->id;
                        $seo_form->agent_id = $invoice->sales_agent_id;
                        $seo_form->save();
                    }
                }elseif($service->form == 6){
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
                }
                 
                
            }
        }

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
    }


    public function createAuth(Request $request){
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
    }

    public function createAuthSupport(Request $request){
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
    }

    public function updateAuthManager(Request $request){
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
    }

    public function updateAuth(Request $request){
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
    }

    public function updateAuthSupport(Request $request){
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
    }

    public function getAgent($brand_id = null){
        $user = User::select('id', 'name', 'last_name')->where('is_employee', 4)->get();
        return response()->json(['success' => true , 'data' => $user]);
    }

    public function getAgentManager($brand_id = null){
        $user = User::select('id', 'name', 'last_name')->where('is_employee', 4)->whereHas('brands', function ($query) use ($brand_id) {
                    return $query->where('brand_id', $brand_id);
                })->get();
        return response()->json(['success' => true , 'data' => $user]);
    }

    public function updateAgent(Request $request){
        $client = Client::find($request->id);
        $client->assign_id = $request->agent_id;
        $client->save();
        return response()->json(['success' => true , 'message' => 'Agent Added Successfully']);
    }


    public function reassignSupportManager(Request $request){
        $project = Project::find($request->id);
        $project->user_id = $request->agent_id;
        $project->save();

        //mail_notification
        $user = User::find($request->agent_id);
        $html = '<p>'.'Project `'.$project->name.'` has been reassigned.'.'</p><br />';
        $html .= '<strong>Reassigned by:</strong> <span>'.Auth::user()->name.'</span><br />';
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
    
    
    
    
    public function reassignSupportManagerTaskID(Request $request){
        
        // dd($request->all()); 
        
        $task = Task::find($request->id);
        $task->user_id = $request->agent_id;
        $task->save();
        
        
        $project = Project::find($task->project_id);
        $project->user_id = $request->agent_id;
        $project->save(); 
        
        
        return redirect()->back()->with('success', $task->name . ' Reassigned Successfully');
        
    }
    
    
    
    
    

    public function assignSupportManager(Request $request){
        
        // dd($request->all());
        
        $form_id  = $request->id;
        $agent_id  = $request->agent_id;
        $form_checker  = $request->form;
        $name = '';
        $client_id = 0;
        $brand_id = 0;
        $description = '';
        if($form_checker == 0){
            $no_form = NoForm::find($form_id);
            if($no_form->name != null){
                $name = $no_form->name . ' - OTHER';
            }else{
                $name = $no_form->name . ' - OTHER';
            }
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

//        dd('here');
        //mail_notification
        $html = '<p>'.'New project `'.$project->name.'`'.'</p><br />';
        $html .= '<strong>Assigned by:</strong> <span>'.Auth::user()->name.'</span><br />';
        $html .= '<strong>Assigned to:</strong> <span>'.$user->name.'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
//        mail_notification('', [$user->email], 'CRM | New project', $html, true);
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
    

    public function assignSupport(Request $request){
        $form_id  = $request->id;
        $agent_id  = $request->agent_id;
        $form_checker  = $request->form;
        $name = '';
        $client_id = 0;
        $brand_id = 0;
        $description = '';
        if($form_checker == 1){
            // Logo form
            $logo_form = LogoForm::find($form_id);
            $name = $logo_form->logo_name . ' - LOGO';
            $client_id = $logo_form->user->id;
            $brand_id = $logo_form->invoice->brand;
            $description = $logo_form->business;
        }elseif($form_checker == 2){
            // Web form
            $web_form = WebForm::find($form_id);
            $name = $web_form->business_name . ' - WEBSITE';
            $client_id = $web_form->user->id;
            $brand_id = $web_form->invoice->brand;
            $description = $web_form->about_companys;
        }elseif($form_checker == 3){
            // Social Media Marketing Form
            $smm_form = SmmForm::find($form_id);
            $name = $smm_form->business_name . ' - SMM';
            $client_id = $smm_form->user->id;
            $brand_id = $smm_form->invoice->brand;
            $description = $smm_form->business_category;
        }elseif($form_checker == 4){
            // Content Writing Form
            $content_form = ContentWritingForm::find($form_id);
            $name = $content_form->company_name . ' - CONTENT WRITING';
            $client_id = $content_form->user->id;
            $brand_id = $content_form->invoice->brand;
            $description = $content_form->company_details;
        }elseif($form_checker == 5){
            // Search Engine Optimization Form
            $seo_form = SeoForm::find($form_id);
            $name = $seo_form->company_name . ' - SEO';
            $client_id = $seo_form->user->id;
            $brand_id = $seo_form->invoice->brand;
            $description = $seo_form->top_goals;
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
        return response()->json(['success' => true , 'message' => 'Support Assigned Successfully']);
    }
}
