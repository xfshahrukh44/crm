<?php

namespace App\Http\Controllers;

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\BookMarketing;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Brand;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\Merchant;
use App\Models\NewSMM;
use App\Models\NoForm;
use App\Models\Proofreading;
use App\Models\SeoBrief;
use App\Models\SeoForm;
use App\Models\Service;
use App\Models\SmmForm;
use App\Models\User;
use App\Models\WebForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BillingController extends Controller
{
    public function billingDashboard()
    {
        return view('billing.home');
    }

    public function billingClient(Request $request){
        $data = new Client;
//        $data = $data->whereIn('brand_id', Auth()->user()->brand_list());
        $data = $data->orderBy('priority', 'ASC');
        $data = $data->orderBy('id', 'desc');
        if($request->name != ''){
            $data = $data->where('name', 'LIKE', "%$request->name%");
            $data = $data->where('name', 'LIKE', "%$request->last_name%");
        }
        if($request->email != ''){
            $data = $data->where('email', 'LIKE', "%$request->email%");
        }
        if($request->contact != ''){
            $data = $data->where('contact', 'LIKE', "%$request->contact%");
        }
        if($request->status != ''){
            $data = $data->where('status', $request->status);
        }
        if($request->task_id != ''){
            $data = $data
                ->where(function ($q) {
                    return $q->whereHas('user', function ($q) {
                        return $q->whereHas('projects', function ($q) {
                            return $q->whereHas('tasks', function ($q) {
                                return $q->where('id', request()->get('task_id'));
                            });
                        });
                    });
                })
                ->orWhere(function ($q) {
                    return $q->whereHas('projects', function ($q) {
                        return $q->whereHas('tasks', function ($q) {
                            return $q->where('id', request()->get('task_id'));
                        });
                    });
//                })->whereIn('brand_id', auth()->user()->brand_list());
                });
        }

        $data = $data->paginate(20);
        return view('billing.client.index', compact('data'));
    }

    public function billingClientCreate(){
        return view('billing.client.create');
    }

    public function billingPaymentLink($id){
        $user = Client::find($id);
//        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();
        $brand = Brand::all();
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant = Merchant::where('status', 1)->orderBy('id', 'desc')->get();

        $sale_agents = User::whereIn('is_employee', [0, 4, 6])
            ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
            ->get();

        if (request()->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return view('billing.payment.create', compact('user', 'brand', 'currencies', 'services', 'merchant', 'sale_agents'));
    }

    public function billingClientEdit($id){
//        $data = Client::where('id', $id)->whereIn('brand_id', Auth::user()->brand_list())->first();
        $data = Client::where('id', $id)->first();
        if($data == null){
            return redirect()->back();
        }else{
            return view('billing.client.edit', compact('data'));
        }
    }

    public function billingClientStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);
        $request->request->add(['user_id' => auth()->user()->id]);
        $client = Client::create($request->all());

        if ($request->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return redirect()->route('billing.generate.payment', $client->id);
    }

    public function billingStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'brand' => 'required',
            'service' => 'required',
            'package' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'payment_type' => 'required',
            'merchant' => 'required'
        ]);

        if ($request->has('show_service_forms')) {
            $client = Client::find($request->client_id);
            $client_show_service_forms = explode(',', $client->show_service_forms) ?? [];

            foreach (($request->get('show_service_forms')['on'] ?? []) as $item) {
                $client_show_service_forms []= $item;
            }

            foreach (($request->get('show_service_forms')['off'] ?? []) as $item) {
                $search_key = array_search($item, $client_show_service_forms);
                if (array_key_exists($search_key, $client_show_service_forms)) {
                    unset($client_show_service_forms[$search_key]);
                }
            }
            unset($client_show_service_forms[""]);

            $client->show_service_forms = implode(',', array_unique($client_show_service_forms));
            $client->save();
        }

        $latest = Invoice::latest()->first();
        if (! $latest) {
            $nextInvoiceNumber = date('Y').'-1';
        }else{
            $expNum = explode('-', $latest->invoice_number);
            $expIncrement = (int)$expNum[1] + 1;
            $nextInvoiceNumber = $expNum[0].'-'.$expIncrement;
        }
        $contact = $request->contact;
        if($contact == null){
            $contact = '#';
        }
        $invoice = new Invoice;
        if($request->createform != null){
            $invoice->createform = $request->createform;
        }else{
            $invoice->createform = 1;
        }
        $invoice->name = $request->name;
        $invoice->email = $request->email;
        $invoice->contact = $contact;
        $invoice->brand = $request->brand;
        $invoice->package = $request->package;
        $invoice->currency = $request->currency;
        $invoice->client_id = $request->client_id;
        $invoice->invoice_number = $nextInvoiceNumber;
        $invoice->sales_agent_id = $request->has('sales_agent_id') && $request->get('sales_agent_id') != '' ? $request->get('sales_agent_id') : Auth()->user()->id;
        $invoice->recurring = $request->get('recurring');
        $invoice->sale_or_upsell = $request->get('sale_or_upsell');
        $invoice->discription = $request->discription;
        $invoice->amount = $request->amount;
        $invoice->payment_status = '1';
        $invoice->custom_package = $request->custom_package;
        $invoice->payment_type = $request->payment_type;
        $service = implode(",",$request->service);
        $invoice->service = $service;
        $invoice->merchant_id = $request->merchant;
        $invoice->save();

        //create stripe invoice
        if ($request->get('merchant') == 4) {
            $currency_map = [
                1 => 'usd',
                2 => 'cad',
                3 => 'gbp',
            ];

            $stripe_invoice_res = create_stripe_invoice($invoice->id, $currency_map[$request->get('currency') ?? 1]);
        }

        if (in_array($request->get('merchant'), [3, 5, 7, 8, 9, 10])) {
            $invoice->is_authorize = true;
            $invoice->save();
        }

        $id = $invoice->id;

        $id = Crypt::encrypt($id);
        $invoiceId = Crypt::decrypt($id);
        $_getInvoiceData = Invoice::findOrFail($invoiceId);
        $_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        $package_name = '';
        if($_getInvoiceData->package == 0){
            $package_name = strip_tags($_getInvoiceData->custom_package);
        }
        $sendemail = $request->sendemail;
        if($sendemail == 1){
            // Send Invoice Link To Email
            $details = [
                'brand_name' => $_getBrand->name,
                'brand_logo' => $_getBrand->logo,
                'brand_phone' => $_getBrand->phone,
                'brand_email' => $_getBrand->email,
                'brand_address' => $_getBrand->address,
                'invoice_number' => $_getInvoiceData->invoice_number,
                'currency_sign' => $_getInvoiceData->currency_show->sign,
                'amount' => $_getInvoiceData->amount,
                'name' => $_getInvoiceData->name,
                'email' => $_getInvoiceData->email,
                'contact' => $_getInvoiceData->contact,
                'date' => $_getInvoiceData->created_at->format('jS M, Y'),
                'link' => route('client.paynow', $id),
                'package_name' => $package_name,
                'discription' => $_getInvoiceData->discription
            ];
            try {
                \Mail::to($_getInvoiceData->email)->send(new \App\Mail\InoviceMail($details));
            } catch (\Exception $e) {

                $mail_error_data = json_encode([
                    'emails' => [$_getInvoiceData->email],
                    'body' => [
                        'brand_name' => $_getBrand->name,
                        'brand_logo' => $_getBrand->logo,
                        'brand_phone' => $_getBrand->phone,
                        'brand_email' => $_getBrand->email,
                        'brand_address' => $_getBrand->address,
                        'invoice_number' => $_getInvoiceData->invoice_number,
                        'currency_sign' => $_getInvoiceData->currency_show->sign,
                        'amount' => $_getInvoiceData->amount,
                        'name' => $_getInvoiceData->name,
                        'email' => $_getInvoiceData->email,
                        'contact' => $_getInvoiceData->contact,
                        'date' => $_getInvoiceData->created_at->format('jS M, Y'),
                        'link' => route('client.paynow', $id),
                        'package_name' => $package_name,
                        'discription' => $_getInvoiceData->discription
                    ],
                    'error' => $e->getMessage(),
                ]);

                \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
            }
        }

        //mail_notification
        $services = implode(", ", Service::whereIn('id', $request->service)->pluck('name')->toArray());
        $html = '<p>'.'Sales agent '. Auth::user()->name .' has created a new invoice'.'</p><br />';
        $html .= '<strong>Client:</strong> <span>'.$request->name.'</span><br />';
        $html .= '<strong>Service(s):</strong> <span>'.strval($services).'</span><br />';
        mail_notification(
            '',
            ['info@designcrm.net'],
            'New invoice created',
            view('mail.crm-mail-template')->with([
                'subject' => 'New invoice created',
                'brand_name' => $_getBrand->name,
                'brand_logo' => asset($_getBrand->logo),
                'additional_html' => $html
            ])
        );

        if (session()->has('redirect_to_client_detail')) {
            session()->remove('redirect_to_client_detail');

            return redirect()->route('clients.detail', $request->client_id)->with('success', 'Client created Successfully.');
        }

        return redirect()->route('billing.link',($invoice->id))->with('success', 'Invoice created successfully!');
    }

    public function billingClientUpdate(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);
        $client->update($request->all());
        return redirect()->back()->with('success', 'Client Updated Successfully.');
    }

    public function linkPageBilling($id){
        $id = Crypt::encrypt($id);
        $invoiceId = Crypt::decrypt($id);
        $_getInvoiceData = Invoice::findOrFail($invoiceId);
        $_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        return view('billing.invoice.link-page', compact('_getInvoiceData', 'id', '_getInvoiceData', '_getBrand'));
    }

    public function getInvoiceByBilling(Request $request){
        $data = new Invoice;
//        $data = $data->whereIn('brand', Auth()->user()->brand_list());
        $data = $data->orderBy('id', 'desc');
        $perPage = 10;
        if($request->package != ''){
            $data = $data->where('custom_package', 'LIKE', "%$request->package%");
        }
        if($request->invoice != ''){
            $data = $data->where('invoice_number', 'LIKE', "%$request->invoice%");
        }
        if($request->user != ''){
            $data = $data->where('name', 'LIKE', "%$request->user%")->orWhere('email', 'LIKE', "%$request->user%");
        }
        if($request->status != 0){
            $data = $data->where('payment_status', $request->status);
        }
        //when client_id
        $data->when($request->has('client_id'), function ($q) use ($request) {
            return $q->where('client_id', $request->get('client_id'));
        });

        $data = $data->paginate(10);
        return view('billing.invoice.index', compact('data'));
    }

    public function editInvoiceBilling($id){
        $invoice = Invoice::find($id);
//        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
        $brand = Brand::all();;
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant =  Merchant::all();
        return view('billing.invoice.edit', compact('invoice', 'brand', 'services', 'currencies', 'merchant'));
    }

    public function invoicePaidByIdBilling($id){
        $invoice = Invoice::find($id);
//        $user = Client::where('email', $invoice->client->email)->first();
        if (!$user = Client::find($invoice->client_id)) {
            $user = Client::where('email', $invoice->client->email)->first();
        }
        $user_client = User::where('client_id', $user->id)->first();
        if($user_client != null || $user->user){
            $service_array = explode(',', $invoice->service);
            for($i = 0; $i < count($service_array); $i++){
                if(!$service = Service::find($service_array[$i])) {
                    continue;
                }

                if($service->form == 0){
                    //No Form
                    //if($invoice->createform == 1){
                    $no_form = new NoForm();
                    $no_form->name = $invoice->custom_package;
                    $no_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $no_form->user_id = $user_client->id;
                    }
                    $no_form->client_id = $user->id;
                    $no_form->agent_id = $invoice->sales_agent_id;
                    $no_form->save();
                    //}
                }elseif($service->form == 1){
                    // Logo Form
                    //if($invoice->createform == 1){
                    $logo_form = new LogoForm();
                    $logo_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $logo_form->user_id = $user_client->id;
                    }
                    $logo_form->client_id = $user->id;
                    $logo_form->agent_id = $invoice->sales_agent_id;
                    $logo_form->save();
                    //}
                }elseif($service->form == 2){
                    // Website Form
                    //if($invoice->createform == 1){
                    $web_form = new WebForm();
                    $web_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $web_form->user_id = $user_client->id;
                    }
                    $web_form->client_id = $user->id;
                    $web_form->agent_id = $invoice->sales_agent_id;
                    $web_form->save();
                    //}
                }elseif($service->form == 3){
                    // Smm Form
                    //if($invoice->createform == 1){
                    $smm_form = new SmmForm();
                    $smm_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $smm_form->user_id = $user_client->id;
                    }
                    $smm_form->client_id = $user->id;
                    $smm_form->agent_id = $invoice->sales_agent_id;
                    $smm_form->save();
                    //}
                }elseif($service->form == 4){
                    // Content Writing Form
                    //if($invoice->createform == 1){
                    $content_writing_form = new ContentWritingForm();
                    $content_writing_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $content_writing_form->user_id = $user_client->id;
                    }
                    $content_writing_form->client_id = $user->id;
                    $content_writing_form->agent_id = $invoice->sales_agent_id;
                    $content_writing_form->save();
                    //}
                }elseif($service->form == 5){
                    // Search Engine Optimization Form
                    //if($invoice->createform == 1){
                    $seo_form = new SeoForm();
                    $seo_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $seo_form->user_id = $user_client->id;
                    }
                    $seo_form->client_id = $user->id;
                    $seo_form->agent_id = $invoice->sales_agent_id;
                    $seo_form->save();
                    //}
                }elseif($service->form == 6){
                    // Book Formatting & Publishing
                    //if($invoice->createform == 1){
                    $book_formatting_form = new BookFormatting();
                    $book_formatting_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $book_formatting_form->user_id = $user_client->id;
                    }
                    $book_formatting_form->client_id = $user->id;
                    $book_formatting_form->agent_id = $invoice->sales_agent_id;
                    $book_formatting_form->save();
                    //}
                }elseif($service->form == 7){
                    // Book Formatting & Publishing
                    //if($invoice->createform == 1){
                    $book_writing_form = new BookWriting();
                    $book_writing_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $book_writing_form->user_id = $user_client->id;
                    }
                    $book_writing_form->client_id = $user->id;
                    $book_writing_form->agent_id = $invoice->sales_agent_id;
                    $book_writing_form->save();
                    //}
                }elseif($service->form == 8){
                    // Author Website
                    //if($invoice->createform == 1){
                    $author_website_form = new AuthorWebsite();
                    $author_website_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $author_website_form->user_id = $user_client->id;
                    }
                    $author_website_form->client_id = $user->id;
                    $author_website_form->agent_id = $invoice->sales_agent_id;
                    $author_website_form->save();
                    //}
                }elseif($service->form == 9){
                    // Author Website
                    //if($invoice->createform == 1){
                    $proofreading_form = new Proofreading();
                    $proofreading_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $proofreading_form->user_id = $user_client->id;
                    }
                    $proofreading_form->client_id = $user->id;
                    $proofreading_form->agent_id = $invoice->sales_agent_id;
                    $proofreading_form->save();
                    //}
                }elseif($service->form == 10){
                    // Author Website
                    //if($invoice->createform == 1){
                    $bookcover_form = new BookCover();
                    $bookcover_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $bookcover_form->user_id = $user_client->id;
                    }
                    $bookcover_form->client_id = $user->id;
                    $bookcover_form->agent_id = $invoice->sales_agent_id;
                    $bookcover_form->save();
                    //}
                }elseif($service->form == 11){
                    // Author Website
                    //if($invoice->createform == 1){
                    $isbn_form = new Isbnform();
                    $isbn_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $isbn_form->user_id = $user_client->id;
                    }
                    $isbn_form->client_id = $user->id;
                    $isbn_form->agent_id = $invoice->sales_agent_id;
                    $isbn_form->save();
                    //}
                }
                elseif($service->form == 12){
                    // Author Website
                    //if($invoice->createform == 1){
                    $book_printing = new Bookprinting();
                    $book_printing->invoice_id = $invoice->id;
                    if($user_client != null){
                        $book_printing->user_id = $user_client->id;
                    }
                    $book_printing->client_id = $user->id;
                    $book_printing->agent_id = $invoice->sales_agent_id;
                    $book_printing->save();
                    //}
                }
                elseif($service->form == 13){
                    $seo_form = new SeoBrief();
                    $seo_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $seo_form->user_id = $user_client->id;
                    }
                    $seo_form->client_id = $user->id;
                    $seo_form->agent_id = $invoice->sales_agent_id;
                    $seo_form->save();
                }
                elseif($service->form == 14){
                    $book_marketing_form = new BookMarketing();
                    $book_marketing_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $book_marketing_form->user_id = $user_client->id;
                    }
                    $book_marketing_form->client_id = $user->id;
                    $book_marketing_form->agent_id = $invoice->sales_agent_id;
                    $book_marketing_form->save();
                }
                elseif($service->form == 15){
                    $new_smm_form = new NewSMM();
                    $new_smm_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $new_smm_form->user_id = $user_client->id;
                    }
                    $new_smm_form->client_id = $user->id;
                    $new_smm_form->agent_id = $invoice->sales_agent_id;
                    $new_smm_form->save();
                }


            }
        }
        $invoice->payment_status = 2;
        $invoice->invoice_date = Carbon::today()->toDateTimeString();
        $invoice->save();

        //mail_notification
        $_getBrand = Brand::find($invoice->brand);
        $html = '<p>'.'Sales agent '. Auth::user()->name .' has marked invoice # '.$invoice->invoice_number.' as PAID'.'</p><br />';
        $html .= '<strong>Client:</strong> <span>'.$invoice->client->name.'</span><br />';
        if ($invoice->services()) {
            $html .= '<strong>Service(s):</strong> <span>'.strval($invoice->services()->name).'</span><br />';
        }
//        mail_notification('', ['info@designcrm.net'], 'Invoice payment', $html);
        mail_notification(
            '',
            ['info@designcrm.net'],
            'Invoice payment',
            view('mail.crm-mail-template')->with([
                'subject' => 'Invoice payment',
                'brand_name' => $_getBrand->name,
                'brand_logo' => asset($_getBrand->logo),
                'additional_html' => $html
            ])
        );

        return redirect()->back()->with('success','Invoice# ' . $invoice->invoice_number . ' Mark as Paid.');
    }

    public function createAuthBilling(Request $request){
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
        } catch (\Exception $e) {
            return response()->json(['success' => false , 'message' => $e->getMessage()]);
        }
    }

    public function updateAuthBilling(Request $request){
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

    public function billingUpdateManager(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'brand' => 'required',
            'service' => 'required',
            'package' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'payment_type' => 'required',
            'merchant' => 'required'
        ]);
        $contact = $request->contact;
        if($contact == null){
            $contact = '#';
        }
        $invoice = Invoice::find($request->invoice_id);
        $invoice->name = $request->name;
        $invoice->email = $request->email;
        $invoice->contact = $contact;
        $invoice->brand = $request->brand;
        $invoice->package = $request->package;
        $invoice->currency = $request->currency;
        $invoice->client_id = $request->client_id;
        $invoice->sales_agent_id = Auth()->user()->id;
        $invoice->discription = $request->discription;
        $invoice->amount = $request->amount;
        $invoice->custom_package = $request->custom_package;
        $invoice->payment_type = $request->payment_type;
        $service = implode(",",$request->service);
        $invoice->service = $service;
        $invoice->merchant_id = $request->merchant;
        $invoice->save();
        return redirect()->route('billing.link',($invoice->id));
    }
}