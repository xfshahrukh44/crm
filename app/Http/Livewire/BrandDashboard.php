<?php

namespace App\Http\Livewire;

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\ClientFile;
use App\Models\ContentWritingForm;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\NoForm;
use App\Models\Project;
use App\Models\Proofreading;
use App\Models\SeoForm;
use App\Models\Service;
use App\Models\SmmForm;
use App\Models\Task;
use App\Models\User;
use App\Models\WebForm;
use App\Notifications\AssignProjectNotification;
use App\Notifications\MessageNotification;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BrandDashboard extends Component
{
    use WithPagination, WithFileUploads;

    public $layout;
    protected $paginationTheme = 'bootstrap';
    public $active_page = 'brands_dashboard';
    public $history = ['brands_dashboard'];

    protected $listeners = [
        'client_auth_create' => 'client_auth_create',
        'client_auth_update' => 'client_auth_update',
        'mutate' => 'mutate',
        'set_select2_field_value' => 'set_select2_field_value',
        'set_tiny_mce_field_value' => 'set_tiny_mce_field_value',
        'assign_pending' => 'assign_pending',
        'reassign_pending' => 'reassign_pending',
    ];

    public $brand_name;
    public $client_name;

    public $client_create_name = '';
    public $client_create_last_name = '';
    public $client_create_email = '';
    public $client_create_contact = '';
    public $client_create_brand_id = '';
    public $client_create_status = '1';

    public $client_payment_create_client_id = '';
    public $client_payment_create_name = '';
    public $client_payment_create_email = '';
    public $client_payment_create_contact = '';
    public $client_payment_create_brand = '';
    public $client_payment_create_service = '';
    public $client_payment_create_package = '';
    public $client_payment_create_createform = '';
    public $client_payment_create_custom_package = '';
    public $client_payment_create_currency = '';
    public $client_payment_create_amount = '';
    public $client_payment_create_payment_type = '';
    public $client_payment_create_merchant = '';
    public $client_payment_create_sendemail = '';
    public $client_payment_create_discription = '';
    public $client_payment_create_sales_agent_id = '';

    public $message_client_client_id = '';
    public $message_client_message = '';
    public $message_client_edit_message = '';
    public $message_client_files = [];

    public $assign_pending_agent_id = '';
    public $assign_pending_id = '';
    public $assign_pending_form = '';

    public $reassign_pending_project_id = '';
    public $reassign_pending_agent_id = '';

    public function construct()
    {
        if (!auth()->check() || !in_array(auth()->user()->is_employee, [2, 6, 4, 0])) {
            return false;
        }

        $layout_map = [
            2 => 'layouts.app-admin',
            6 => 'layouts.app-manager',
            0 => 'layouts.app-sale',
            4 => 'layouts.app-support',
        ];

        $this->layout = $layout_map[auth()->user()->is_employee];

        return true;
    }

    public function mount()
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }
    }

    public function set_active_page ($page)
    {
        $this->active_page = $page;
        $this->history[] = $this->active_page;

        $this->resetPage(); // Reset pagination

        $this->emit('emit_pre_render');
        $this->render(); // Trigger rendering
    }

    public function back () {
        array_pop($this->history);
        $this->active_page = end($this->history);

        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        if ($this->active_page == 'brands_dashboard') {
            $view = $this->brands_dashboard();
        } else if (str_contains($this->active_page, 'brands_detail')) {
            $brand_id = intval(str_replace('brands_detail-', '', $this->active_page));
            $view = $this->brands_detail($brand_id);
        } else if (str_contains($this->active_page, 'manager_notification')) {
            $brand_id = intval(str_replace('manager_notification-', '', $this->active_page));
            $view = $this->manager_notification($brand_id);
        } else if (str_contains($this->active_page, 'brief_pending')) {
            $brief_pending_explode = explode('_brief_pending-', $this->active_page);
            $role = $brief_pending_explode[0];
            $brand_id = $brief_pending_explode[1];
            $view = $this->brief_pending($role, $brand_id);
        } else if (str_contains($this->active_page, 'client_create')) {
            $client_create_explode = explode('_client_create-', $this->active_page);
            $role = $client_create_explode[0];
            $brand_id = $client_create_explode[1];
            $view = $this->client_create($role, $brand_id);
        } else if (str_contains($this->active_page, 'clients_detail')) {
            $client_id = intval(str_replace('clients_detail-', '', $this->active_page));
            $view = $this->clients_detail($client_id);
        } else if (str_contains($this->active_page, 'projects_detail')) {
            $project_id = intval(str_replace('projects_detail-', '', $this->active_page));
            $view = $this->projects_detail($project_id);
        } else if (str_contains($this->active_page, 'client_payment_link')) {
            $client_id = intval(str_replace('client_payment_link-', '', $this->active_page));
            $view = $this->client_payment_link($client_id);
        } else if (str_contains($this->active_page, 'client_message_show')) {
            $client_user_id = intval(str_replace('client_message_show-', '', $this->active_page));
            $view = $this->client_message_show($client_user_id);
        }

        return $view;
    }

    public function mutate ($data)
    {
        $property = $data['name'];
        $this->$property = $data['value'];
    }

    public function set_select2_field_value ($data)
    {
        $property = $data['name'];
        $this->$property = $data['value'];
    }

    public function set_tiny_mce_field_value ($data)
    {
        $property = $data['name'];
        $this->$property = $data['value'];
    }

    public function brands_dashboard ()
    {
        $brands = Brand::
        when(in_array(Auth::user()->is_employee, [6, 4, 0]), function ($q) {
            return $q->whereIn('id', Auth::user()->brand_list());
        })
            ->when($this->brand_name && $this->brand_name != '', function ($q) {
                return $q->where(function ($q) {
                    return $q->where('name', 'LIKE', '%'. $this->brand_name .'%')
                        ->orWhere('url', 'LIKE', '%'. $this->brand_name .'%')
                        ->orWhere('phone', 'LIKE', '%'. $this->brand_name .'%')
                        ->orWhere('phone_tel', 'LIKE', '%'. $this->brand_name .'%')
                        ->orWhere('email', 'LIKE', '%'. $this->brand_name .'%')
                        ->orWhere('address', 'LIKE', '%'. $this->brand_name .'%')
                        ->orWhere('address_link', 'LIKE', '%'. $this->brand_name .'%');
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        return view('livewire.brand-dashboard', compact('brands'))->extends($this->layout);
    }

    public function brands_detail ($brand_id)
    {
        $projects = Project::where('brand_id', $brand_id);
        $total_projects_count = $projects->count();
        $completed_projects_count = 0;
        foreach ($projects->get() as $project) {
            $completed_projects_count += no_pending_tasks_left($project->id) ? 1 : 0;
        }

        $brand= Brand::find($brand_id);
        $clients = Client::where('brand_id', $brand_id)
            ->withCount('projects')->withCount('invoices')
            ->orderBy('created_at', 'DESC')
            ->when($this->client_name && $this->client_name != "", function ($q) {
                return $q->whereHas('user', function ($q) {
                    return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'. $this->client_name .'%')
                        ->orWhere('name', 'LIKE', '%'. $this->client_name .'%')
                        ->orWhere('last_name', 'LIKE', '%'. $this->client_name .'%')
                        ->orWhere('email', 'LIKE', '%'. $this->client_name .'%')
                        ->orWhere('contact', 'LIKE', '%'. $this->client_name .'%');
                });
            })
            ->paginate(10);

        $brand_user_ids = DB::table('brand_users')->where('brand_id', $brand_id)->pluck('user_id')->toArray();

        $buhs = User::whereIn('id', $brand_user_ids)->where('is_employee', 6)->get();
        $support_heads = User::whereIn('id', $brand_user_ids)->where('is_employee', 4)->where('is_support_head', 1)->get();
        $customer_supports = User::whereIn('id', $brand_user_ids)->where('is_employee', 4)->where('is_support_head', 0)->get();
        $agents = User::whereIn('id', $brand_user_ids)->where('is_employee', 0)->get();

        return view('livewire.brand-detail', compact('brand', 'clients', 'buhs', 'support_heads', 'customer_supports', 'agents', 'total_projects_count', 'completed_projects_count'))->extends($this->layout);
    }

    public function clients_detail ($client_id)
    {
        $client= Client::find($client_id);
        $client_user = \App\Models\User::where('client_id', $client->id)->first();
        $projects = $client_user ? $client_user->recent_projects : [];

        $this->emit('emit_select2', ['selector' => '.agent-name-wrapper-2', 'name' => 'assign_pending_agent_id' ]);

        return view('livewire.client-detail', compact('client', 'projects'))->extends($this->layout);
    }

    public function projects_detail ($project_id)
    {
        $project= Project::find($project_id);
        $category_ids_from_tasks = array_unique(Task::where('project_id', $project_id)->pluck('category_id')->toArray());

        $categories_with_active_tasks = [];
        foreach ($category_ids_from_tasks as $category_id_from_tasks) {
            if (Auth::user()->is_employee == 2) {
//                $tasks = Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])->where('status', '!=', 3)->get();
                $tasks = Task::where(['project_id' => $project_id, 'category_id' => $category_id_from_tasks])
                    ->orderBy('created_at', 'DESC')->get();
            } else {
                $tasks = Task::where(['project_id' => $project_id, 'category_id' => $category_id_from_tasks])
//                    ->where('status', '!=', 3)
                    ->whereIn('brand_id', Auth::user()->brand_list())
                    ->orderBy('created_at', 'DESC')->get();
            }
            $categories_with_active_tasks []= [
                'category' => Category::find($category_id_from_tasks),
                'tasks' => $tasks,
            ];
        }

        return view('livewire.project-detail', compact('project', 'categories_with_active_tasks'))->extends($this->layout);
    }

    public function manager_notification ($brand_id)
    {
        $notifications = sale_manager_notifications($brand_id);

        return view('livewire.manager.notification', compact('notifications'))->extends($this->layout);
    }

    public function brief_pending ($role, $brand_id)
    {
        $client_users_with_brief_pendings = User::whereIn('id', get_brief_client_user_ids(null, $brand_id))->get();

        return view('livewire.'.$role.'.brief.pending', compact('client_users_with_brief_pendings'))->extends($this->layout);
    }

    public function client_create ($role, $brand_id)
    {
        $brands = Brand::when(auth()->user()->is_employee != 2, function ($q) {
            return $q->whereIn('id', auth()->user()->brand_list());
        })->get();

        $this->client_create_brand_id = $brand_id;

        return view('livewire.'.$role.'.client.create', compact('brands'))->extends($this->layout);
    }

    public function client_save ()
    {
        $this->validate([
            'client_create_name' => 'required',
            'client_create_last_name' => 'required',
            'client_create_email' => [
                'required',
                'email',
                'unique:clients,email',
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->exists()) {
                        $fail('Email already taken.');
                    }
                },
            ],
            'client_create_brand_id' => 'required',
            'client_create_status' => 'required',
        ]);

        $client = Client::create([
            'user_id' => auth()->user()->id,
            'name' => $this->client_create_name,
            'last_name' => $this->client_create_last_name,
            'email' => $this->client_create_email,
            'contact' => $this->client_create_contact,
            'brand_id' => $this->client_create_brand_id,
            'status' => $this->client_create_status,
        ]);

        $this->emit('success', 'Client created successfully');

        return $this->set_active_page('client_payment_link-' . $client->id);
    }

    public function client_auth_create ($data)
    {
        if (create_client_auth($data)) {
            $this->emit('success', 'Account created successfully');
        } else {
            $this->emit('error', "Couldn't create account.");
        }

        return $this->render();
    }

    public function client_auth_update ($data)
    {
        if (create_update_auth($data)) {
            $this->emit('success', 'Account updated successfully');
        } else {
            $this->emit('error', "Couldn't update account.");
        }

        return $this->render();
    }

    public function client_payment_link ($client_id)
    {
        $user = Client::find($client_id);
        $brands = Brand::whereIn('id', [$user->brand_id])->get();
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant = Merchant::where('status', 1)->orderBy('id', 'desc')->get();

        $sale_agents = User::whereIn('is_employee', [0, 4, 6])
            ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
            ->get();

        $this->client_payment_create_client_id = $client_id;
        $this->client_payment_create_name = $user->name . ' ' . $user->last_name;
        $this->client_payment_create_email = $user->email;
        $this->client_payment_create_contact = $user->contact;
        $this->client_payment_create_brand = $user->brand_id;

        $this->emit('emit_select2', ['selector' => '#service', 'name' => 'client_payment_create_service' ]);
        $this->emit('emit_select2', ['selector' => '#sales_agent_id', 'name' => 'client_payment_create_sales_agent_id' ]);

        return view('livewire.payment.create', compact('user', 'brands', 'currencies', 'services', 'merchant', 'sale_agents'))->extends($this->layout);
    }

    public function client_payment_save ()
    {
        $this->validate([
            'client_payment_create_name' => 'required',
            'client_payment_create_email' => 'required',
            'client_payment_create_brand' => 'required',
            'client_payment_create_service' => 'required',
            'client_payment_create_package' => 'required',
            'client_payment_create_currency' => 'required',
            'client_payment_create_amount' => 'required',
            'client_payment_create_payment_type' => 'required',
            'client_payment_create_merchant' => 'required'
        ]);

        $latest = Invoice::latest()->first();
        if (! $latest) {
            $nextInvoiceNumber = date('Y').'-1';
        }else{
            $expNum = explode('-', $latest->invoice_number);
            $expIncrement = (int)$expNum[1] + 1;
            $nextInvoiceNumber = $expNum[0].'-'.$expIncrement;
        }
        $contact = $this->client_payment_create_contact;
        if($contact == null){
            $contact = '#';
        }
        $invoice = new Invoice;
        if($this->client_payment_create_createform != null){
            $invoice->createform = $this->client_payment_create_createform;
        }else{
            $invoice->createform = 1;
        }
        $invoice->name = $this->client_payment_create_name;
        $invoice->email = $this->client_payment_create_email;
        $invoice->contact = $contact;
        $invoice->brand = $this->client_payment_create_brand;
        $invoice->package = $this->client_payment_create_package;
        $invoice->currency = $this->client_payment_create_currency;
        $invoice->client_id = $this->client_payment_create_client_id;
        $invoice->invoice_number = $nextInvoiceNumber;
        $invoice->sales_agent_id = $this->client_payment_create_sales_agent_id && $this->client_payment_create_sales_agent_id != '' ? $this->client_payment_create_sales_agent_id : Auth()->user()->id;
        $invoice->discription = $this->client_payment_create_discription;
        $invoice->amount = $this->client_payment_create_amount;
        $invoice->payment_status = '1';
        $invoice->custom_package = $this->client_payment_create_custom_package;
        $invoice->payment_type = $this->client_payment_create_payment_type;
        $service = $this->client_payment_create_service;
        $invoice->service = $service;
        $invoice->merchant_id = $this->client_payment_create_merchant;
        $invoice->save();
        $id = $invoice->id;

        $id = Crypt::encrypt($id);
        $invoiceId = Crypt::decrypt($id);
        $_getInvoiceData = Invoice::findOrFail($invoiceId);
        $_getBrand = Brand::where('id',$_getInvoiceData->brand)->first();
        $package_name = '';
        if($_getInvoiceData->package == 0){
            $package_name = strip_tags($_getInvoiceData->custom_package);
        }
        $sendemail = $this->client_payment_create_sendemail;
        if($sendemail == 1) {
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
                Mail::to($_getInvoiceData->email)->send(new \App\Mail\InoviceMail($details));
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

        $this->emit('success', 'Invoice created successfully.');

        return $this->set_active_page('clients_detail-' . $this->client_payment_create_client_id);
    }

    public function mark_invoice_as_paid ($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (!$user = Client::find($invoice->client_id)) {
            $user = Client::where('email', $invoice->client->email)->first();
        }
        $user_client = User::where('client_id', $user->id)->first();
        if($user_client != null || $user->user){
            $service = Service::find($invoice->service);
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
        }
        $invoice->payment_status = 2;
        $invoice->invoice_date = Carbon::today()->toDateTimeString();
        $invoice->save();

        //mail_notification
        $_getBrand = Brand::find($invoice->brand);
        $html = '<p>'.'Support member '. Auth::user()->name .' has marked invoice # '.$invoice->invoice_number.' as PAID'.'</p><br />';
        $html .= '<strong>Client:</strong> <span>'.$invoice->client->name.'</span><br />';
        $html .= '<strong>Service(s):</strong> <span>'.strval($invoice->services()->name).'</span><br />';
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

        $this->emit('success', 'Invoice# ' . $invoice->invoice_number . ' marked as paid.');

        $this->render();
    }

    public function client_message_show ($client_user_id)
    {
        $user = User::find($client_user_id);
        $messages = Message::where('client_id', $client_user_id)->orderBy('id', 'asc')->get();

        $this->emit('emit_init_image_uploader', '.input-images');
        $this->emit('emit_init_tiny_mce', ['selector' => '#message', 'name' => 'message_client_message' ]);
        $this->emit('emit_init_tiny_mce', ['selector' => '#editmessage', 'name' => 'message_client_edit_message' ]);
        $this->emit('emit_scroll_to_bottom', 1400);

        $this->message_client_client_id = $client_user_id;

        return view('livewire.message.index', compact('messages', 'user'))->extends($this->layout);
    }

    public function message_client_send ()
    {
        $this->validate([
            'message_client_message' => 'required',
        ]);

        $carbon = Carbon::now(new DateTimeZone('America/Los_Angeles'))->toDateTimeString();
        // send Notification to customer
        $message = new Message();
        $message->user_id = Auth::user()->id;
        $message->message = $this->message_client_message;
        $message->sender_id = $this->message_client_client_id;
        $message->client_id = $this->message_client_client_id;
        $message->role_id = auth()->user()->is_employee;
        $message->created_at = Carbon::now();
        $message->save();

//        if(count($this->message_client_files)){
//            $i = 0;
//            foreach($this->message_client_files as $file)
//            {
//                $file_actual_name = $file['name'];
//                $file_name_explode = explode('.', $file['name']);
//                $file_name = generateRandomString(10) . '.' . $file_name_explode[1];
//                $file->move(public_path().'/files/', $name);
//                $i++;
//                $client_file = new ClientFile();
//                $client_file->name = $file_actual_name;
//                $client_file->path = $file_name;
//                $client_file->task_id = 0;
//                $client_file->user_id = auth()->user()->id;
//                $client_file->user_check = auth()->user()->is_employee;
//                $client_file->production_check = 2;
//                $client_file->message_id = $message->id;
//                $client_file->created_at = Carbon::now();
//                $client_file->save();
//            }
//        }

        $details = [
            'title' => auth()->user()->name . ' ' . auth()->user()->last_name . ' has message on your task.',
            'body' => 'Please Login into your Dashboard to view it..'
        ];

        $client = User::find($this->message_client_client_id);
        try {
            Mail::to($client->email)->send(new \App\Mail\ClientNotifyMail($details));
        } catch (\Exception $e) {

            $mail_error_data = json_encode([
                'emails' => [$client->email],
                'body' => 'Please Login into your Dashboard to view it..',
                'error' => $e->getMessage(),
            ]);

            \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
        }

        $task_id = 0;
        $project_id = 0;

        $messageData = [
            'id' => auth()->user()->id,
            'task_id' => $task_id,
            'project_id' => $project_id,
            'name' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'text' => auth()->user()->name . ' ' . auth()->user()->last_name . ' has sent you a Message',
            'details' => Str::limit(filter_var($this->message_client_message, FILTER_SANITIZE_STRING), 40 ),
            'url' => '',
        ];

        // Message Notification sending to Admin
        $adminusers = User::where('is_employee', 2)->get();
        foreach($adminusers as $adminuser){
            $adminuser->notify(new MessageNotification($messageData));
        }

        //notification
        if ($client_user = User::where('id', $this->message_client_client_id)->first()) {
            //pusher notification
            $client_user->notify(new MessageNotification($messageData));
            $pusher_notification_data = [
                'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
                'redirect_url' => route('client.home'),
            ];
            emit_pusher_notification(('message-channel-for-client-user-' . $client_user->id), 'new-message', $pusher_notification_data);

            //mail_notification
            $client = Client::find($client_user->client_id);
            $brand_id = $client->brand_id;
            $brand = Brand::find($brand_id);

            $html = '<p>'. 'Hello ' . $client->name . ',' .'</p>';
            $html .= '<p>'. 'You have received a new message from your Project Manager, ('.auth::user()->name.'), on our CRM platform. Please log in to your account to read the message and respond.' .'</p>';
            $html .= '<p>'. 'Access your messages here: ' . route('client.fetch.messages') .'</p>';
            $html .= '<p>'. 'Thank you for your prompt attention to this matter.' .'</p>';
            $html .= '<p>'. 'Best Regards,' .'</p>';
            $html .= '<p>'. $brand->name .'.</p>';

            mail_notification(
                '',
                [$client->email],
                'New Message from Your Project Manager on ('.$brand->name.') CRM',
                view('mail.crm-mail-template')->with([
                    'subject' => 'New Message from Your Project Manager on ('.$brand->name.') CRM',
                    'brand_name' => $brand->name,
                    'brand_logo' => asset($brand->logo),
                    'additional_html' => $html
                ]),
            //            true
            );
        }

        $this->emit('success', 'Message Send Successfully.');

        return $this->set_active_page('client_message_show-' . $this->message_client_client_id);
    }

    public function assign_pending ()
    {
        $this->validate([
            'assign_pending_agent_id' => 'required',
            'assign_pending_id' => 'required',
            'assign_pending_form' => 'required'
        ]);

        $agent_id  = $this->assign_pending_agent_id;
        $form_id  = $this->assign_pending_id;
        $form_checker  = $this->assign_pending_form;
        $name = '';
        $client_id = 0;
        $brand_id = 0;
        $description = '';
        if($form_checker == 0){
            $no_form = NoForm::find($form_id);
            $client = Client::find($no_form->client_id);
            if($no_form->name != null){
                $name = $no_form->name . ' - ' . $client->name;
            }else{
                $name = $no_form->name . ' - ' . $client->name;
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

        $this->assign_pending_agent_id = '';
        $this->assign_pending_id = '';
        $this->assign_pending_form = '';
        $this->emit('success', $user->name . ' ' . $user->last_name . ' Assigned Successfully');

        $this->render();
    }

    public function reassign_pending ()
    {
        $this->validate([
            'reassign_pending_project_id' => 'required',
            'reassign_pending_agent_id' => 'required'
        ]);

        $project = Project::find($this->reassign_pending_project_id);
        $project->user_id = $this->reassign_pending_agent_id;
        $project->save();

        //mail_notification
        $user = User::find($this->reassign_pending_agent_id);
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

        $this->reassign_pending_project_id = '';
        $this->reassign_pending_agent_id = '';
        $this->emit('success', $project->name . ' Reassigned Successfully');

        $this->render();
    }
}
