<?php

namespace App\Http\Controllers;

use App\Models\BookMarketing;
use App\Models\NewSMM;
use App\Models\SeoBrief;
use Illuminate\Support\Facades\Crypt;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use App\Models\Brand;
use App\Models\NoForm;
use App\Models\Merchant;
use App\Models\LogoForm;
use App\Models\WebForm;
use App\Models\SmmForm;
use App\Models\ContentWritingForm;
use App\Models\SeoForm;
use App\Models\BookFormatting;
use App\Models\BookWriting;
use App\Models\AuthorWebsite;
use App\Models\Proofreading;
use App\Models\BookCover;
use App\Models\Currency;
use App\Models\Service;
use App\Models\Isbnform;
use App\Models\Bookprinting;
use Illuminate\Http\Request;
use Auth;
use Stripe;
use Illuminate\Support\Facades\DB;
use Notification;
use App\Notifications\PaymentNotification;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        try {
            $user = Client::find($id);
            $brand = Brand::all();
            $services = Service::all();
            $currencies =  Currency::all();

            $sale_agents = User::whereIn('is_employee', [0, 4, 6])
                ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
                ->get();

            if (request()->has('redirect_to_client_detail')) {
                session()->put('redirect_to_client_detail', true);
            }

            return view('admin.invoice.create', compact('user', 'brand', 'currencies', 'services', 'sale_agents'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function invoiceAll(Request $request){
        try {
            $data = new Invoice();
            $brands = Brand::all();
            $data = $data->orderBy('id', 'desc');
            if($request->package != ''){
                $data = $data->where('custom_package', 'LIKE', "%$request->package%");
            }
            if($request->invoice != ''){
                $data = $data->where('invoice_number', 'LIKE', "%$request->invoice%");
            }
            if($request->customer != ''){
                $customer = $request->customer;
                $data = $data->whereHas(
                    'client', function($q) use($customer){
                    $q->where('name', 'LIKE', "%$customer%");
                    $q->orWhere('last_name', 'LIKE', "%$customer%");
                }
                );
            }
            if($request->agent != ''){
                $agent = $request->agent;
                $data = $data->whereHas(
                    'sale', function($q) use($agent){
                    $q->where('name', 'LIKE', "%$agent%");
                    $q->orWhere('last_name', 'LIKE', "%$agent%");
                }
                );
            }
            if($request->status != 0){
                $data = $data->where('payment_status', $request->status);
            }
            if($request->brand != 0){
                $brand = $request->brand;
                $data = $data->whereHas('brands', function($q) use($brand){
                    $q->where('id', $brand);
                });
            }
            //when client_id
            $data->when($request->has('client_id'), function ($q) use ($request) {
                return $q->where('client_id', $request->get('client_id'));
            });
            $data = $data->paginate(10);
            $display = '';
            if ($request->ajax()) {
                foreach ($data as $rander) {
                    $form = '';
                    if($rander->payment_status == 1){
                        $form = '<form method="post" action="'.route('admin.invoice.paid', $rander->id).'">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <button type="submit" class="mark-paid btn btn-danger p-0">Mark As Paid</button>
                    </form>';
                    }
                    $display .=
                        '<tr>
                    <td><span class="btn btn-primary btn-sm">#'.$rander->invoice_number.'</span></td>
                    <td>'. ($rander->package == 0 ? $rander->custom_package : $rander->package) .'</td>
                    <td>'. $rander->client->name . ' ' . $rander->client->last_name . '<br>' . $rander->client->email .'</td>
                    <td>'. ($rander->sales_agent_id != 0 ? $rander->sale->name . ' ' . $rander->sale->last_name : 'From Website') .'</td>
                    <td><button class="btn btn-sm btn-secondary">'. $rander->brands->name .'</button></td>
                    <td>'. $rander->currency_show->sign .''. $rander->amount.'</td>
                    <td>
                        <span class="btn btn-'.\App\Models\Invoice::STATUS_COLOR[$rander->payment_status].' btn-sm">
                            '.\App\Models\Invoice::PAYMENT_STATUS[$rander->payment_status].
                        $form.'
                        </span>
                    </td>
                    <td><button class="btn btn-sm btn-secondary">'.date('g:i a - d M, Y', strtotime($rander->created_at)).'</button></td>
                    <td>
                        <a href="'.route('admin.link', $rander->id).'" class="btn btn-info btn-icon btn-sm">
                            <span class="ul-btn__icon"><i class="i-Eye-Visible"></i></span>
                            <span class="ul-btn__text">View</span>
                        </a>
                    </td>
                </tr>';
                }
                return $display;
            }
            return view('admin.invoice.index', compact('data', 'brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getInvoice(){

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
        try {
            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required',
                'brand' => 'required',
                'service' => 'required',
                'package' => 'required',
                'currency' => 'required',
                'amount' => 'required',
                'payment_type' => 'required'
            ]);


            $latest = Invoice::latest()->first();
            if (! $latest) {
                $nextInvoiceNumber = date('Y').'-1';
            }else{
                $expNum = explode('-', $latest->invoice_number);
                $expIncrement = (int)$expNum[1] + 1;
                $nextInvoiceNumber = $expNum[0].'-'.$expIncrement;
            }
            $invoice = new Invoice;
            $invoice->name = $request->name;
            $invoice->email = $request->email;
            $contact = $request->contact;
            if($contact == null){
                $contact = '#';
            }
            $invoice->contact = $contact;
            $invoice->brand = $request->brand;
            $invoice->package = $request->package;
            $invoice->currency = $request->currency;
            $invoice->client_id = $request->client_id;
            $invoice->invoice_number = $nextInvoiceNumber;
            $invoice->sales_agent_id = $request->has('sales_agent_id') && $request->get('sales_agent_id') != '' ? $request->get('sales_agent_id') : Auth()->user()->id;
            $invoice->discription = $request->discription;
            $invoice->amount = $request->amount;
            $invoice->payment_status = '1';
            $invoice->custom_package = $request->custom_package;
            $invoice->payment_type = $request->payment_type;
            $service = implode(",",$request->service);
            $invoice->service = $service;
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

            if (session()->has('redirect_to_client_detail')) {
                session()->remove('redirect_to_client_detail');

                return redirect()->route('clients.detail', $request->client_id)->with('success', 'Client created Successfully.');
            }

            return redirect()->route('admin.link',($invoice->id));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function linkPage($id){
        try {
            $id = Crypt::encrypt($id);
            $invoiceId = Crypt::decrypt($id);
            $_getInvoiceData = Invoice::findOrFail($invoiceId);
            $_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
            return view('admin.invoice.link-page', compact('_getInvoiceData', 'id', '_getInvoiceData', '_getBrand'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function linkPageSale($id){
		$id = Crypt::encrypt($id);
		$invoiceId = Crypt::decrypt($id);
		$_getInvoiceData = Invoice::findOrFail($invoiceId);
		$_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        return view('sale.invoice.link-page', compact('_getInvoiceData', 'id', '_getInvoiceData', '_getBrand'));
    }

    public function linkPageManager($id){
		$id = Crypt::encrypt($id);
		$invoiceId = Crypt::decrypt($id);
		$_getInvoiceData = Invoice::findOrFail($invoiceId);
		$_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        return view('manager.invoice.link-page', compact('_getInvoiceData', 'id', '_getInvoiceData', '_getBrand'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function payNow($id){
        abort(404);
		$id = Crypt::decrypt($id);
		$_getInvoiceData = Invoice::findOrFail($id);
        $_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        return view('invoice.paynow', compact('_getInvoiceData','_getBrand'));
    }

    public function paymentProcess(Request $request)
    {
        $invoiceId = $request->invoice_id;
	    $invoiceData = Invoice::findOrFail($invoiceId);
        $merchant_name;
        if($invoiceData->merchant == null){
            $merchant = DB::table('merchants')->where('secret_key', env('STRIPE_SECRET'))->first();
            $merchant_name = $merchant->name;
        }else{
            $merchant_name = $invoiceData->merchant->name;
        }
        $temp = explode(' ', $invoiceData->brands->name);
        $result = '';
        foreach($temp as $t){
            $result .= $t[0];
        }

	    $customerName = $request->user_name;
        $customerEmail = $request->user_email;
        $customerPhone = $request->user_phone;
        $customerAddress = $request->address;
        $ServiceAmount = $invoiceData->amount;
        $token = $request->stripeToken;
        $packageName = $invoiceData->package;
        if($invoiceData->package == 0){
            $packageName = $invoiceData->custom_package;
        }
        $merchant = 1;
        if($invoiceData->merchant_id == null){
            $merchant = 1;
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        }else{
            if($invoiceData->merchant->is_authorized == 2){
                $merchant = 2;
            }else{
                $merchant = 1;
                Stripe\Stripe::setApiKey($invoiceData->merchant->secret_key);
            }
        }

        if($merchant == 1){

            try{
                $cust_id = \Stripe\Customer::create(array(
                    "name" => $customerName,
                    "email" => $customerEmail,
                    "phone" => $customerPhone,
                    "source" =>$token,
                    "address" => [
                        "line1" => $request->address,
                        "postal_code" => $request->zip,
                        "city" => $request->city,
                        "state" => $request->set_state,
                        "country" => $request->country,
                    ],
                ));

                $customer = $cust_id->id;
                DB::table('clients')->where('id', $invoiceData->client->id)->update(['stripe_token'=>$customer]);

                $service_name = '';
                $service_array = explode(',', $invoiceData->service);
                for($i = 0; $i < count($service_array); $i++){
                    $service = Service::find($service_array[$i]);
                    $service_name .= $service->name;
                    if(($i + 1) == count($service_array)){

                    }else{
                        $service_name .=  ', ';
                    }
                }
                $transaction_id = '';

                /* Creating Customer In Stripe */
                if($invoiceData->payment_type == 0 ){
                    $mainAmount =  $ServiceAmount * 100 ;
                    $charge =  \Stripe\Charge::create(array(
                        "amount" => $mainAmount,
                        "currency" => $invoiceData->currency_show->short_name,
                        "customer" => $customer,
                        "receipt_email" => $request->user_email,
                        "description" => 'Payment for invoice',
                        "shipping" => [
                            "name" => $customerName,
                            "address" => [
                                "line1" => $request->address,
                                "postal_code" => $request->zip,
                                "city" => $request->city,
                                "state" => $request->set_state,
                                "country" => $request->country,
                            ],
                        ]
                        // "description" => $result . ' ' . $merchant_name . ' - ' . $service_name . ' ( ' . $invoiceData->discription . ' )',
                    ));
                    $transaction_id = $charge->id;
                }else{
                    $mainAmount =  $ServiceAmount - 5 ;
                    $paymnetOne = $mainAmount * 100;
                        $charge =  \Stripe\Charge::create(array(
                            "amount" => $paymnetOne,
                            "currency" => $invoiceData->currency_show->short_name,
                            "customer" => $customer,
                            "receipt_email" => $request->user_email,
                            "description" => 'Payment for invoice',
                            "shipping" => [
                                "name" => $customerName,
                                "address" => [
                                    "line1" => $request->address,
                                    "postal_code" => $request->zip,
                                    "city" => $request->city,
                                    "state" => $request->set_state,
                                    "country" => $request->country,
                                ],
                            ]
                            // "description" => $result . ' ' . $merchant_name . ' - ' . $service_name . ' ( ' . $invoiceData->discription . ' )',
                        ));

                        $devCharge =  \Stripe\Charge::create(array(
                            "amount" => '250',
                            "currency" => $invoiceData->currency_show->short_name,
                            "customer" => $customer,
                            "receipt_email" => $request->user_email,
                            "description" => 'Payment for invoice',
                            "shipping" => [
                                "name" => $customerName,
                                "address" => [
                                    "line1" => $request->address,
                                    "postal_code" => $request->zip,
                                    "city" => $request->city,
                                    "state" => $request->set_state,
                                    "country" => $request->country,
                                ],
                            ]
                            // "description" => $result . ' ' . $merchant_name . ' - ' . $service_name . ' ( ' . $invoiceData->discription . ' )',
                        ));

                        $devCharge =  \Stripe\Charge::create(array(
                            "amount" => '250',
                            "currency" => $invoiceData->currency_show->short_name,
                            "customer" => $customer,
                            "receipt_email" => $request->user_email,
                            "description" => 'Payment for invoice',
                            "shipping" => [
                                "name" => $customerName,
                                "address" => [
                                    "line1" => $request->address,
                                    "postal_code" => $request->zip,
                                    "city" => $request->city,
                                    "state" => $request->set_state,
                                    "country" => $request->country,
                                ],
                            ]
                            // "description" => $result . ' ' . $merchant_name . ' - ' . $service_name . ' ( ' . $invoiceData->discription . ' )',
                        ));
                    $transaction_id = $charge->id;
                }
                $is_error = 0;
            }catch(Stripe\Exception\CardException $e){
                $error_message = $e->getError()->message;
                $is_error = 1;
            }catch (\Stripe\Exception\RateLimitException $e) {
                $error_message = $e->getMessage();
                $is_error = 1;
                // Too many requests made to the API too quickly
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $error_message = $e->getMessage();
                $is_error = 1;
                // Invalid parameters were supplied to Stripe's API
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $error_message = $e->getMessage();
                $is_error = 1;
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $error_message = $e->getMessage();
                $is_error = 1;
                // Network communication with Stripe failed
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $error_message = $e->getMessage();
                $is_error = 1;
                // Display a very generic error to the user, and maybe send
                // yourself an email
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                $is_error = 1;
                // Something else happened, completely unrelated to Stripe
            }

            if($is_error == 1){
                return redirect()->back()->with('stripe_error', $error_message);
            }

            if($charge->status == "succeeded"){
                $invoice = array();
                $invoice['request'] = $request;
                $get_invoice = Invoice::findOrFail($request->invoice_id);
                if($get_invoice){
                    $get_invoice->transaction_id = $transaction_id;
                    $get_invoice->payment_status = '2';
                    $get_invoice->invoice_date = Carbon::today()->toDateTimeString();
                    $get_invoice->save();
                }
                $user = Client::where('email', $get_invoice->client->email)->first();
                $user_client = User::where('email', $get_invoice->client->email)->first();
                if($user_client != null){
                    $service_array = explode(',', $get_invoice->service);
                    for($i = 0; $i < count($service_array); $i++){
                        $service = Service::find($service_array[$i]);
                        if($service->form == 0){
                            //No Form
                            if($get_invoice->createform == 1){
                                $no_form = new NoForm();
                                $no_form->name = $get_invoice->custom_package;
                                $no_form->invoice_id = $get_invoice->id;

                                if($user_client != null){
                                    $no_form->user_id = $user_client->id;
                                }
                                $no_form->client_id = $user->id;
                                $no_form->agent_id = $get_invoice->sales_agent_id;
                                $no_form->save();
                            }
                        }elseif($service->form == 1){
                            // Logo Form
                            if($get_invoice->createform == 1){
                                $logo_form = new LogoForm();
                                $logo_form->invoice_id = $get_invoice->id;
                                if($user_client != null){
                                    $logo_form->user_id = $user_client->id;
                                }
                                $logo_form->client_id = $user->id;
                                $logo_form->agent_id = $get_invoice->sales_agent_id;
                                $logo_form->save();
                            }
                        }elseif($service->form == 2){
                            // Website Form
                            if($get_invoice->createform == 1){
                                $web_form = new WebForm();
                                $web_form->invoice_id = $get_invoice->id;
                                if($user_client != null){
                                    $web_form->user_id = $user_client->id;
                                }
                                $web_form->client_id = $user->id;
                                $web_form->agent_id = $get_invoice->sales_agent_id;
                                $web_form->save();
                            }
                        }elseif($service->form == 3){
                            // Smm Form
                            if($get_invoice->createform == 1){
                                $smm_form = new SmmForm();
                                $smm_form->invoice_id = $get_invoice->id;
                                if($user_client != null){
                                    $smm_form->user_id = $user_client->id;
                                }
                                $smm_form->client_id = $user->id;
                                $smm_form->agent_id = $get_invoice->sales_agent_id;
                                $smm_form->save();
                            }
                        }elseif($service->form == 4){
                            // Content Writing Form
                            if($get_invoice->createform == 1){
                                $content_writing_form = new ContentWritingForm();
                                $content_writing_form->invoice_id = $get_invoice->id;
                                if($user_client != null){
                                    $content_writing_form->user_id = $user_client->id;
                                }
                                $content_writing_form->client_id = $user->id;
                                $content_writing_form->agent_id = $get_invoice->sales_agent_id;
                                $content_writing_form->save();
                            }
                        }elseif($service->form == 5){
                            // Search Engine Optimization Form
                            if($get_invoice->createform == 1){
                                $seo_form = new SeoForm();
                                $seo_form->invoice_id = $get_invoice->id;
                                if($user_client != null){
                                    $seo_form->user_id = $user_client->id;
                                }
                                $seo_form->client_id = $user->id;
                                $seo_form->agent_id = $get_invoice->sales_agent_id;
                                $seo_form->save();
                            }
                        }
                    }
                }
                $details = [
                    'title' =>  'Invoice Number #' . $get_invoice->id . ' has been paid by '. $customerName . ' - ' . $customerEmail,
                    'body' => 'Please Login into your Dashboard to view it..'
                ];
                try {
                    \Mail::to($get_invoice->sale->email)->send(new \App\Mail\ClientNotifyMail($details));
                } catch (\Exception $e) {

                    $mail_error_data = json_encode([
                        'emails' => [$get_invoice->sale->email],
                        'body' => 'Please Login into your Dashboard to view it..',
                        'error' => $e->getMessage(),
                    ]);

                    \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
                }

                $messageData = [
                    'id' => $get_invoice->id,
                    'name' => $customerName ,
                    'email' => $customerEmail,
                    'text' => 'Invoice Number #' . $get_invoice->id . ' has been paid by '. $customerName . ' - ' . $customerEmail,
                    'details' => '',
                    'url' => '',
                ];
                if(($get_invoice->sale->is_employee != 2) && ($get_invoice->sale->is_employee != 6)){
                    $get_invoice->sale->notify(new PaymentNotification($messageData));
                }
                // Message Notification sending to Admin & Managers
                $managers = User::where('is_employee', 6)->whereHas('brands', function ($query) use ($get_invoice) {
                                return $query->where('brand_id', $get_invoice->brand);
                            })->get();

                foreach($managers as $manager){
                    Notification::send($manager, new PaymentNotification($messageData));
                }

                $adminusers = User::where('is_employee', 2)->get();
                foreach($adminusers as $adminuser){
                    Notification::send($adminuser, new PaymentNotification($messageData));
                }

                return redirect()->route('thankYou',($get_invoice->id));
            }else{
                return redirect()->route('failed',($get_invoice->id));
            }
        }else{
            // authorized.net
            $input = $request->input();
            $get_expiration = explode('/', $input['expiration']);
            $expirationMonth = $get_expiration[0];
            $expirationYear = $get_expiration[1];
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($invoiceData->merchant->login_id);
            $merchantAuthentication->setTransactionKey($invoiceData->merchant->secret_key);
            $refId = 'ref' . time();
            $cardNumber = preg_replace('/\s+/', '', $input['cardNumber']);
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate('20'.$expirationYear . "-" .$expirationMonth);
            $creditCard->setCardCode($input['cvv']);
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);

            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($invoiceData->invoice_number);
            $order->setDescription($invoiceData->discription);

            // Set the customer's Bill To address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($invoiceData->name);
            $customerAddress->setAddress($request->address);
            $customerAddress->setCity($request->city);
            $customerAddress->setState($request->set_state);
            $customerAddress->setZip($request->zip);
            $customerAddress->setEmail($request->user_email);
            $customerAddress->setCountry($request->country);
            $customerAddress->setPhoneNumber($request->user_phone);

            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setEmail($request->user_email);

            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($invoiceData->amount);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->setCurrencyCode('USD');
            $transactionRequestType->setCustomerIP($request->ip());
            $requests = new AnetAPI\CreateTransactionRequest();
            $requests->setMerchantAuthentication($merchantAuthentication);
            $requests->setRefId($refId);
            $requests->setTransactionRequest($transactionRequestType);
            $controller = new AnetController\CreateTransactionController($requests);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            // $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            if ($response != null) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $message_text = $tresponse->getMessages()[0]->getDescription().", Transaction ID: " . $tresponse->getTransId();
                        $msg_type = "success_msg";

                        // Payment Done By Authorized
                        $get_invoice = Invoice::findOrFail($request->invoice_id);
                        if($get_invoice){
                            $get_invoice->transaction_id = $tresponse->getTransId();
                            $get_invoice->payment_status = '2';
                            $get_invoice->invoice_date = Carbon::today()->toDateTimeString();
                            $get_invoice->save();
                        }
                        $user = Client::where('email', $get_invoice->client->email)->first();
                        $user_client = User::where('email', $get_invoice->client->email)->first();
                        if($user_client != null){
                            $service_array = explode(',', $get_invoice->service);
                            for($i = 0; $i < count($service_array); $i++){
                                $service = Service::find($service_array[$i]);
                                if($service->form == 0){
                                    //No Form
                                    if($get_invoice->createform == 1){
                                        $no_form = new NoForm();
                                        $no_form->name = $get_invoice->custom_package;
                                        $no_form->invoice_id = $get_invoice->id;

                                        if($user_client != null){
                                            $no_form->user_id = $user_client->id;
                                        }
                                        $no_form->client_id = $user->id;
                                        $no_form->agent_id = $get_invoice->sales_agent_id;
                                        $no_form->save();
                                    }
                                }elseif($service->form == 1){
                                    // Logo Form
                                    if($get_invoice->createform == 1){
                                        $logo_form = new LogoForm();
                                        $logo_form->invoice_id = $get_invoice->id;
                                        if($user_client != null){
                                            $logo_form->user_id = $user_client->id;
                                        }
                                        $logo_form->client_id = $user->id;
                                        $logo_form->agent_id = $get_invoice->sales_agent_id;
                                        $logo_form->save();
                                    }
                                }elseif($service->form == 2){
                                    // Website Form
                                    if($get_invoice->createform == 1){
                                        $web_form = new WebForm();
                                        $web_form->invoice_id = $get_invoice->id;
                                        if($user_client != null){
                                            $web_form->user_id = $user_client->id;
                                        }
                                        $web_form->client_id = $user->id;
                                        $web_form->agent_id = $get_invoice->sales_agent_id;
                                        $web_form->save();
                                    }
                                }elseif($service->form == 3){
                                    // Smm Form
                                    if($get_invoice->createform == 1){
                                        $smm_form = new SmmForm();
                                        $smm_form->invoice_id = $get_invoice->id;
                                        if($user_client != null){
                                            $smm_form->user_id = $user_client->id;
                                        }
                                        $smm_form->client_id = $user->id;
                                        $smm_form->agent_id = $get_invoice->sales_agent_id;
                                        $smm_form->save();
                                    }
                                }elseif($service->form == 4){
                                    // Content Writing Form
                                    if($get_invoice->createform == 1){
                                        $content_writing_form = new ContentWritingForm();
                                        $content_writing_form->invoice_id = $get_invoice->id;
                                        if($user_client != null){
                                            $content_writing_form->user_id = $user_client->id;
                                        }
                                        $content_writing_form->client_id = $user->id;
                                        $content_writing_form->agent_id = $get_invoice->sales_agent_id;
                                        $content_writing_form->save();
                                    }
                                }elseif($service->form == 5){
                                    // Search Engine Optimization Form
                                    if($get_invoice->createform == 1){
                                        $seo_form = new SeoForm();
                                        $seo_form->invoice_id = $get_invoice->id;
                                        if($user_client != null){
                                            $seo_form->user_id = $user_client->id;
                                        }
                                        $seo_form->client_id = $user->id;
                                        $seo_form->agent_id = $get_invoice->sales_agent_id;
                                        $seo_form->save();
                                    }
                                }
                            }
                        }
                        $details = [
                            'title' =>  'Invoice Number #' . $get_invoice->id . ' has been paid by '. $customerName . ' - ' . $customerEmail,
                            'body' => 'Please Login into your Dashboard to view it..'
                        ];
                        try {
                            \Mail::to($get_invoice->sale->email)->send(new \App\Mail\ClientNotifyMail($details));
                        } catch (\Exception $e) {

                            $mail_error_data = json_encode([
                                'emails' => [$get_invoice->sale->email],
                                'body' => 'Please Login into your Dashboard to view it..',
                                'error' => $e->getMessage(),
                            ]);

                            \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
                        }

                        $messageData = [
                            'id' => $get_invoice->id,
                            'name' => $customerName . ' - ' . $customerEmail ,
                            'email' => $customerEmail,
                            'text' => 'Invoice Number #' . $get_invoice->invoice_number . ' Paid.',
                            'details' => '',
                            'url' => '',
                        ];
                        if(($get_invoice->sale->is_employee != 2) && ($get_invoice->sale->is_employee != 6)){
                            $get_invoice->sale->notify(new PaymentNotification($messageData));
                        }
                        // Message Notification sending to Admin & Managers
                        $managers = User::where('is_employee', 6)->whereHas('brands', function ($query) use ($get_invoice) {
                                        return $query->where('brand_id', $get_invoice->brand);
                                    })->get();

                        foreach($managers as $manager){
                            Notification::send($manager, new PaymentNotification($messageData));
                        }

                        $adminusers = User::where('is_employee', 2)->get();
                        foreach($adminusers as $adminuser){
                            Notification::send($adminuser, new PaymentNotification($messageData));
                        }
                        return redirect()->route('thankYou',($get_invoice->id));
                    } else {
                        $message_text = 'There were some issue with the payment. Please try again later.';
                        $msg_type = "error_msg";

                        if ($tresponse->getErrors() != null) {
                            $message_text = $tresponse->getErrors()[0]->getErrorText();
                            $msg_type = "error_msg";
                        }
                    }
                    // Or, print errors if the API request wasn't successful
                } else {
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type = "error_msg";

                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type = "error_msg";
                    } else {
                        $message_text = $response->getMessages()->getMessage()[0]->getText();
                        $msg_type = "error_msg";
                    }
                }
            } else {

                $message_text = "No response returned";
                $msg_type = "error_msg";
            }
            return back()->with($msg_type, $message_text);
        }
    }

    public function thankYou($id)
    {
		$id = Crypt::encrypt($id);
		$invoiceId = Crypt::decrypt($id);
		$_getInvoiceData = Invoice::findOrFail($invoiceId);
		$_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        return view('invoice.thank-you')->with(compact('_getInvoiceData'))->with(compact('id','_getInvoiceData','_getBrand'));
    }

    public function failed($id)
    {
		$id = Crypt::encrypt($id);
		$invoiceId = Crypt::decrypt($id);
		$_getInvoiceData = Invoice::findOrFail($invoiceId);
		$_getBrand = Brand::where('brand_name',$_getInvoiceData->brand)->first();
        return view('invoice.failed')->with(compact('_getInvoiceData'))->with(compact('id','_getInvoiceData','_getBrand'));
    }

    public function managerStore(Request $request)
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

		return redirect()->route('manager.link',($invoice->id));
    }

    public function saleStore(Request $request)
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

        if (session()->has('redirect_to_client_detail')) {
            session()->remove('redirect_to_client_detail');

            return redirect()->route('clients.detail', $request->client_id)->with('success', 'Client created Successfully.');
        }

		return redirect()->route('sale.link',($invoice->id));
    }

    public function getInvoiceBySaleManager(Request $request){
        $data = new Invoice;
        $data = $data->whereIn('brand', Auth()->user()->brand_list());
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
        return view('manager.invoice.index', compact('data'));
    }

    public function getInvoiceByUserId (Request $request){
        $data = new Invoice;
//        $data = $data->where('sales_agent_id', Auth()->user()->id);
        $data = $data->orderBy('id', 'desc');
        $perPage = 10;
        if($request->package != ''){
            $data = $data->where('custom_package', 'LIKE', "%$request->package%");
        }
        if($request->invoice != ''){
            $data = $data->where('invoice_number', 'LIKE', "%$request->invoice%");
        }
        if($request->user != 0){
            $data = $data->where('name', 'LIKE', "%$request->user%");
            $data = $data->orWhere('email', 'LIKE', "%$request->user%");
        }
        if($request->status != 0){
            $data = $data->where('payment_status', $request->status);
        }

        //when client_id
        $data->when($request->has('client_id'), function ($q) use ($request) {
            return $q->where('client_id', $request->get('client_id'));
        });
        $data = $data->paginate(10);
        return view('sale.invoice.index', compact('data'));
    }

    public function getSingleInvoice($id){
        $data = Invoice::where('id', $id)->where('sales_agent_id', Auth::user()->id)->first();
        if($data == null){
            return redirect()->back();
        }else{
            return view('sale.invoice.show', compact('data'));
        }
    }

    public function invoicePaidByIdManager($id){
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

    public function invoicePaidByIdSale($id){
        $invoice = Invoice::find($id);
//        $user = Client::where('email', $invoice->client->email)->first();
        if (!$user = Client::find($invoice->client_id)) {
            $user = Client::where('email', $invoice->client->email)->first();
        }
        $user_client = User::where('client_id', $user->id)->first();
        if($user_client != null || $user->user){
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

    public function invoicePaidById($id){
        try {
            $invoice = Invoice::find($id);
            $invoice->payment_status = 2;
            $invoice->invoice_date = Carbon::today()->toDateTimeString();
            $invoice->save();

//            $user = Client::where('email', $invoice->client->email)->first();
            if (!$user = Client::find($invoice->client_id)) {
                $user = Client::where('email', $invoice->client->email)->first();
            }
            $user_client = User::where('client_id', $user->id)->first();
            if($user_client != null || $user->user){
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


                }
            }
            return redirect()->back()->with('success','Invoice# ' . $invoice->invoice_number . ' Mark as Paid.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function editInvoice($id){
        $invoice = Invoice::find($id);
        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant =  Merchant::all();
        return view('sale.invoice.edit', compact('invoice', 'brand', 'services', 'currencies', 'merchant'));
    }

    public function editInvoiceManager($id){
        $invoice = Invoice::find($id);
        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant =  Merchant::all();
        return view('manager.invoice.edit', compact('invoice', 'brand', 'services', 'currencies', 'merchant'));
    }

    public function saleUpdateManager(Request $request){
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
        return redirect()->route('manager.link',($invoice->id));
    }

    public function saleUpdate(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'brand' => 'required',
            'service' => 'required',
            'package' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'payment_type' => 'required',
            'merchant' => 'required',
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
        return redirect()->route('sale.link',($invoice->id));
    }

    public function managerRefundCB (Request $request)
    {
        $refund_cb_invoices = Invoice::whereIn('brand', auth()->user()->brand_list())->whereNotNull('refund_cb_date')->paginate(10);

        return view('manager.invoice.refund', compact('refund_cb_invoices'));
    }

    public function managerRefundCBSubmit (Request $request)
    {
        $request->validate([
            'invoice_number' => 'required',
            'refunded_cb' => 'required',
            'refund_cb_date' => 'required',
        ]);

        if (!$invoice = Invoice::whereIn('brand', auth()->user()->brand_list())->where('invoice_number', $request->get('invoice_number'))->first()) {
            return redirect()->back()->with('error', 'Invalid Invoice ID');
        }

        $invoice->refunded_cb = $request->get('refunded_cb');
        $invoice->refund_cb_date = $request->get('refund_cb_date');
        $invoice->save();

        return redirect()->back()->with('success', 'Refund/CB entry added.');
    }

    public function managerSalesSheet (Request $request)
    {
        $selected_month = $request->get('month') ?? null;
        $request->merge(['month' => $selected_month]);

        $selected_year = $request->get('year') ?? null;
        $request->merge(['year' => $selected_year]);

        $show_all = $request->get('all') ?? '0';
        $request->merge(['all' => $show_all]);

        $data = Invoice::whereIn('brand', auth()->user()->brand_list())
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
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->orderBy('created_at', 'DESC')
            ->get();

        $amount = 0;
        $refund = 0;
        foreach ($data as $item) {
            $amount += $item->amount;
            $refund += $item->refunded_cb;
        }
        $net = $amount - $refund;

        return view('manager.invoice.sales-sheet', compact('data', 'amount', 'refund', 'net'));
    }

    public function adminRefundCB (Request $request)
    {
        $refund_cb_invoices = Invoice::whereNotNull('refund_cb_date')->paginate(10);

        return view('admin.invoice.refund', compact('refund_cb_invoices'));
    }

    public function adminRefundCBSubmit (Request $request)
    {
        $request->validate([
            'invoice_number' => 'required',
            'refunded_cb' => 'required',
            'refund_cb_date' => 'required',
        ]);

        if (!$invoice = Invoice::where('invoice_number', $request->get('invoice_number'))->first()) {
            return redirect()->back()->with('error', 'Invalid Invoice ID');
        }

        $invoice->refunded_cb = $request->get('refunded_cb');
        $invoice->refund_cb_date = $request->get('refund_cb_date');
        $invoice->save();

        return redirect()->back()->with('success', 'Refund/CB entry added.');
    }

    public function adminSalesSheet (Request $request)
    {
//        $selected_month = ($request->has('month') && $request->get('month') != '') ? $request->get('month') : Carbon::now()->month;
        $selected_month = $request->get('month') ?? null;
        $request->merge(['month' => $selected_month]);
//        $selected_year = ($request->has('year') && $request->get('year') != '') ? $request->get('year') : Carbon::now()->year;
        $selected_year = $request->get('year') ?? null;
        $request->merge(['year' => $selected_year]);

        $show_all = $request->get('all') ?? '0';
        $request->merge(['all' => $show_all]);

        $data = Invoice::orderBy('created_at', 'DESC')
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
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->orderBy('created_at', 'DESC')
            ->get();

        $amount = 0;
        $refund = 0;
        foreach ($data as $item) {
            $amount += $item->amount;
            $refund += $item->refunded_cb;
        }
        $net = $amount - $refund;

        return view('admin.invoice.sales-sheet', compact('data', 'amount', 'refund', 'net'));
    }
}
