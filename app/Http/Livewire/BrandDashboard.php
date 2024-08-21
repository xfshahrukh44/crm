<?php

namespace App\Http\Livewire;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class BrandDashboard extends Component
{
    use WithPagination;

    public $layout;
    protected $paginationTheme = 'bootstrap';
    public $active_page = 'brands_dashboard';
    public $history = ['brands_dashboard'];

    public $brand_name;
    public $client_name;

    public $client_create_name = '';
    public $client_create_last_name = '';
    public $client_create_email = '';
    public $client_create_contact = '';
    public $client_create_brand_id = '';
    public $client_create_status = '1';

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

    public function set_active_page ($page) {
        $this->active_page = $page;
        $this->history []= $this->active_page;

        $this->render();
    }

    public function back () {
        unset($this->history[count($this->history) - 1]);
        $this->active_page = $this->history[count($this->history) - 1];

        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        if ($this->active_page == 'brands_dashboard') {
            return $this->brands_dashboard();
        } else if (str_contains($this->active_page, 'brands_detail')) {
            $brand_id = intval(str_replace('brands_detail-', '', $this->active_page));
            return $this->brands_detail($brand_id);
        } else if (str_contains($this->active_page, 'manager_notification')) {
            $brand_id = intval(str_replace('manager_notification-', '', $this->active_page));
            return $this->manager_notification($brand_id);
        } else if (str_contains($this->active_page, 'brief_pending')) {
            $brief_pending_explode = explode('_brief_pending-', $this->active_page);
            $role = $brief_pending_explode[0];
            $brand_id = $brief_pending_explode[1];
            return $this->brief_pending($role, $brand_id);
        } else if (str_contains($this->active_page, 'client_create')) {
            $client_create_explode = explode('_client_create-', $this->active_page);
            $role = $client_create_explode[0];
            $brand_id = $client_create_explode[1];
            return $this->client_create($role, $brand_id);
        } else if (str_contains($this->active_page, 'clients_detail')) {
            $client_id = intval(str_replace('clients_detail-', '', $this->active_page));
            return $this->clients_detail($client_id);
        }
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

        return view('livewire.client-detail', compact('client', 'projects'))->extends($this->layout);
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

        session()->flash('success', 'Client created successfully');

        return $this->set_active_page('clients_detail-' . $client->id);
    }
}
