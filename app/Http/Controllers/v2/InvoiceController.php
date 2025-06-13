<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\AdminInvoice;
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
use App\Models\NoForm;
use App\Models\PressReleaseForm;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 6, 4, 0])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = $this->getBrands();

        //restricted brand access
        $restricted_brands = $this->getRestrictedBrands();

        $invoices = Invoice::orderBy('id', 'desc')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand', auth()->user()->brand_list());
            })
            ->when(user_is_cs(), function ($q) {
                return $q->where('sales_agent_id', auth()->id());
            })
            ->when($request->package != '', function ($q) {
                return $q->where('custom_package', 'LIKE', "%".request()->package."%");
            })
            ->when($request->invoice != '', function ($q) {
    //            return $q->where('invoice_number', 'LIKE', "%".request()->invoice."%");
                return $q->where('id', request()->invoice);
            })
            ->when($request->customer != '', function ($q) {
                $customer = request()->customer;
                return $q->whereHas('client', function($q) use($customer){
                    $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$customer.'%');
                });
            })
            ->when($request->agent != '', function ($q) {
                $agent = request()->agent;
                return $q->whereHas('sale', function($q) use($agent){
                    $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$agent.'%');
                });
            })
            ->when($request->status != 0, function ($q) {
                return $q->where('payment_status', request()->status);
            })
            ->when($request->brand != 0, function ($q) {
                $brand = request()->brand;
                return $q->whereHas('brands', function($q) use($brand){
                    $q->where('id', $brand);
                });
            })
            ->when($request->has('client_id'), function ($q) use ($request) {
                return $q->where('client_id', $request->get('client_id'));
            })->when(!v2_acl([2]) && !empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            })->paginate(10);

        return view('v2.invoice.index', compact('invoices', 'brands'));
    }

    public function create (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4, 0]) || user_is_cs()) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if(!$user = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non admin checks
        if (!v2_acl([2])) {
            if (!in_array($user->brand_id, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $brands = $this->getBrands();
        $services = Service::all();
        $sale_agents = $this->getSaleAgents();

        return view('v2.invoice.create', compact('brands', 'user', 'services', 'sale_agents'));
    }

    public function store (Request $request)
    {
        if (!v2_acl([2, 6, 4, 0]) || user_is_cs()) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'brand' => 'required',
            'service' => 'required',
//            'package' => 'required',
//            'currency' => 'required',
            'amount' => 'required',
//            'payment_type' => 'required',
            'merchant' => 'required'
        ]);

        $is_closing_payment = $request->get('is_closing_payment') ?? 0;

        //service work
        if ($request->has('show_service_forms') && $is_closing_payment != 1) {
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
        $nextInvoiceNumber = $latest ?
            (explode('-', $latest->invoice_number))[0].'-'.((int)(explode('-', $latest->invoice_number))[1] + 1) :
            (date('Y').'-1');

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
        $invoice->package = '0';
        $invoice->currency = '1';
        $invoice->client_id = $request->client_id;
        $invoice->invoice_number = $nextInvoiceNumber;
        $invoice->sales_agent_id = $request->has('sales_agent_id') && $request->get('sales_agent_id') != '' ? $request->get('sales_agent_id') : auth()->user()->id;
        $invoice->recurring = $request->get('recurring');
        $invoice->sale_or_upsell = $request->get('sale_or_upsell');
        $invoice->discription = $request->discription;
        $invoice->amount = $request->amount;
        $invoice->payment_status = '1';
        $invoice->custom_package = $request->custom_package;
        $invoice->payment_type = 0;
        $service = implode(",",$request->service);
        $invoice->service = $service;
        $invoice->merchant_id = $request->merchant;
        $invoice->is_closing_payment = $is_closing_payment;

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

        //authorize net
        if (in_array($request->get('merchant'), get_authorize_merchant_ids())) {
            $invoice->is_authorize = true;
            $invoice->save();
        }

        $brand = Brand::where('id',$invoice->brand)->first();
        $package_name = '';
        if($invoice->package == '0'){
            $package_name = strip_tags($invoice->custom_package);
        }
        $sendemail = $request->sendemail;
        if($sendemail == 1){
            // Send Invoice Link To Email
            $details = [
                'brand_name' => $brand->name,
                'brand_logo' => $brand->logo,
                'brand_phone' => $brand->phone,
                'brand_email' => $brand->email,
                'brand_address' => $brand->address,
                'invoice_number' => $invoice->invoice_number,
                'currency_sign' => $invoice->currency_show->sign,
                'amount' => $invoice->amount,
                'name' => $invoice->name,
                'email' => $invoice->email,
                'contact' => $invoice->contact,
                'date' => $invoice->created_at->format('jS M, Y'),
                'link' => route('client.paynow', $invoice->id),
                'package_name' => $package_name,
                'discription' => $invoice->discription
            ];
            try {
                Mail::to($invoice->email)->send(new \App\Mail\InoviceMail($details));
            } catch (\Exception $e) {

                $mail_error_data = json_encode([
                    'emails' => [$invoice->email],
                    'body' => [
                        'brand_name' => $brand->name,
                        'brand_logo' => $brand->logo,
                        'brand_phone' => $brand->phone,
                        'brand_email' => $brand->email,
                        'brand_address' => $brand->address,
                        'invoice_number' => $invoice->invoice_number,
                        'currency_sign' => $invoice->currency_show->sign,
                        'amount' => $invoice->amount,
                        'name' => $invoice->name,
                        'email' => $invoice->email,
                        'contact' => $invoice->contact,
                        'date' => $invoice->created_at->format('jS M, Y'),
                        'link' => route('client.paynow', $invoice->id),
                        'package_name' => $package_name,
                        'discription' => $invoice->discription
                    ],
                    'error' => $e->getMessage(),
                ]);

                \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
            }
        }

        return redirect()->route('v2.clients.show', $request->client_id)->with('success', 'Invoice created.');
    }

    public function edit (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$client = Client::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.client.edit', compact('client'));
    }

    public function update (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$client = Client::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        $request->validate([
//            'name' => 'required',
//            'brand_id' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|unique:clients,email,'.$client->id,
////            'email' => 'required' . !is_null($client->user) ? ('|unique:users,email,'.$client->user->id) : '',
//            'status' => 'required',
//        ]);
//
//        $client->update($request->all());
//
//        return redirect()->route('v2.clients')->with('success','Client updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4, 0])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$invoice = Invoice::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non admin checks
        if (!v2_acl([2])) {
            if (!in_array($invoice->brand, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        return view('v2.invoice.show', compact('invoice'));
    }

    public function markPaid (Request $request, $id)
    {
        if (!v2_acl([2, 6, 4, 0]) || user_is_cs()) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $invoice = Invoice::find($id);

        //non admin checks
        if (!v2_acl([2])) {
            if (!v2_acl([0]) && !in_array($invoice->brand, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([0]) && $invoice->client->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        if (!$user = Client::find($invoice->client_id)) {
            $user = Client::where('email', $invoice->client->email)->first();
        }
        $user_client = User::where('client_id', $user->id)->first();
        if(($user_client != null || $user->user) && $invoice->is_closing_payment != 1){
            $service_array = explode(',', $invoice->service);
            for($i = 0; $i < count($service_array); $i++){
                if (!$service = Service::find($service_array[$i])) {
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
                elseif($service->form == 16){
                    $press_release_form = new PressReleaseForm();
                    $press_release_form->invoice_id = $invoice->id;
                    if($user_client != null){
                        $press_release_form->user_id = $user_client->id;
                    }
                    $press_release_form->client_id = $user->id;
                    $press_release_form->agent_id = $invoice->sales_agent_id;
                    $press_release_form->save();
                }


            }
        }
        $invoice->payment_status = 2;
        $invoice->invoice_date = Carbon::today()->toDateTimeString();
        $invoice->save();

        //buh pusher notification
        $buh_ids = User::where('is_employee', 6)->whereIn('id', DB::table('brand_users')->where('brand_id', $invoice->brand)->pluck('user_id')->toArray())->pluck('id')->toArray();
        $inv_arr = $invoice->toArray();
        $inv_arr['redirect_url'] = route('v2.invoices.show', $invoice->id);

        foreach ($buh_ids as $buh_id) {
            emit_pusher_notification('buh-'.$buh_id.'-invoice-channel', 'invoice-paid', ['invoice' => $inv_arr]);
        }

        //mail_notification
        $_getBrand = Brand::find($invoice->brand);
        $html = '<p>'.'Support member '. Auth::user()->name .' has marked invoice # '.$invoice->invoice_number.' as PAID'.'</p><br />';
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

        return redirect()->route('v2.clients.show', $invoice->client_id)->with('success','Invoice# ' . $invoice->invoice_number . ' Marked as Paid.');
    }

    public function refundCB (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = $this->getBrands();

        //restricted brand access
        $restricted_brands = $this->getRestrictedBrands();

        $invoices = Invoice::whereNotNull('refund_cb_date')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand', auth()->user()->brand_list());
            })
            ->when(request()->has('invoice_number') && request()->get('invoice_number') != '', function ($q) {
                return $q->where(function ($q) {
                    return $q->where('invoice_number', request()->get('invoice_number'))
                        ->orWhere('id', request()->get('invoice_number'));
                });
            })->when(request()->has('refunded_cb') && request()->get('refunded_cb') != '', function ($q) {
                return $q->where('refunded_cb', request()->get('refunded_cb'));
            })->when(request()->has('refund_cb_date') && request()->get('refund_cb_date') != '', function ($q) {
                return $q->whereDate('refund_cb_date', Carbon::parse(request()->get('refund_cb_date')));
            })->when(!v2_acl([2]) && !empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            })->paginate(10);

        return view('v2.invoice.refundcb', compact('invoices', 'brands'));
    }

    public function salesSheet (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $selected_month = $request->get('month') ?? null;
        $request->merge(['month' => $selected_month]);
        $selected_year = $request->get('year') ?? null;
        $request->merge(['year' => $selected_year]);

        $invoices = Invoice::orderBy('created_at', 'DESC')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand', auth()->user()->brand_list());
            })
            ->when($selected_month, function ($q) use($selected_month) {
                return $q->whereMonth('created_at', '=', $selected_month);
            })
            ->when($selected_year, function ($q) use($selected_year) {
                return $q->whereYear('created_at', '=', $selected_year);
            })
            ->when($request->has('brand') && $request->get('brand') != '', function ($q) use($request) {
                return $q->where('brand', '=', $request->get('brand'));
            })
            ->when($request->has('agent') && $request->get('agent') != '', function ($q) use($request) {
                return $q->where('sales_agent_id', '=', $request->get('agent'));
            })
            ->when($request->has('merchant') && $request->get('merchant') != '', function ($q) use($request) {
                return $q->where('merchant_id', '=', $request->get('merchant'));
            })
//            ->whereYear('created_at', '=', Carbon::now()->year)
            ->orderBy('created_at', 'DESC');

        //restricted brand access
        $restricted_brands = $this->getRestrictedBrands();
        $invoices->when(!v2_acl([2]) && !empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
            return $q->where(function ($query) use ($restricted_brands) {
                $query->whereNotIn('brand', $restricted_brands)
                    ->orWhere(function ($subQuery) use ($restricted_brands) {
                        $subQuery->whereIn('brand', $restricted_brands)
                            ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                    });
            });
        });

        $amount = $invoices->sum('amount');
        $refund = $invoices->sum('refunded_cb');
        $net = $amount - $refund;

        $invoices = $invoices->paginate(10);

        $brands = $this->getBrands();
        $sale_agents = $this->getSaleAgents();
        $merchants = get_my_merchants();

        return view('v2.invoice.salesheet', compact('invoices', 'amount', 'refund', 'net', 'brands', 'sale_agents', 'merchants'));
    }

    public function adminInvoices (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $selected_month = $request->get('month') ?? null;
        $request->merge(['month' => $selected_month]);

        $brands = $this->getBrands();
        $admin_invoices = AdminInvoice::orderBy('date', 'DESC')
            ->when($request->has('brand_name') && $request->get('brand_name') != '', function ($q) use ($request) {
                return $q->where('department', $request->get('brand_name'));
            })
            ->when($selected_month, function ($q) use($selected_month) {
                return $q->whereMonth('date', '=', $selected_month);
            })
            ->when(!v2_acl([2]), function ($q) {
                $brand_names = Brand::whereIn('id', auth()->user()->brand_list())->pluck('name')->toArray();
                return $q->whereIn('brand_name', $brand_names);
            })
            ->paginate(10);

        return view('v2.invoice.admin-invoices', compact('admin_invoices', 'brands'));
    }

    public function getBrands ()
    {
        return \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();
    }

    public function getSaleAgents ()
    {
        return DB::table('users')->whereIn('is_employee', [0, 4, 6])
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', array_unique(
                    DB::table('brand_users')->whereIn('brand_id', auth()->user()->brand_list())->pluck('user_id')->toArray()
                ))->where('is_employee', '!=', 6);
            })
            ->get();
    }

    public function getRestrictedBrands ()
    {
        return json_decode(auth()->user()->restricted_brands, true); // Ensure it's an array
    }
}
