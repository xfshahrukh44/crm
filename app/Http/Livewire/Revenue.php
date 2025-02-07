<?php

namespace App\Http\Livewire;

use App\Models\Brand;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Revenue extends Component
{
    public $layout;

    protected $listeners = [
        'mutate' => 'mutate',
    ];

    public function construct()
    {
        if (!auth()->check() || !in_array(auth()->user()->is_employee, [2, 6])) {
            return false;
        }

        $layout_map = [
            2 => 'layouts.app-admin',
            6 => 'layouts.app-manager',
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

    public function render()
    {
        $buh_users = User::where('is_employee', 6)->orderBy('name', 'ASC')
            //restricted buhs
            ->whereIn('id', [7, 1169, 33, 18, 4191, 4491])
            ->get();

        $daily_data = [];
        foreach ($buh_users as $buh_user) {
            $todays_invoice_ids = DB::table('invoices')->whereIn('brand', $buh_user->brand_list())
                ->whereDate('created_at', '=', Carbon::today())->pluck('id')->toArray();
            $todays_invoice_totals = get_invoice_totals_in_usd($todays_invoice_ids);
            $todays_invoice_refunds = get_invoice_refunds_totals_in_usd($todays_invoice_ids);
            $daily_target = $buh_user->finances->daily_target ?? 0.00;
            $daily_printing_costs = $buh_user->finances->daily_printing_costs ?? 0.00;
            $daily_net_achieved = $todays_invoice_totals - $daily_printing_costs - $todays_invoice_refunds;
            $daily_target_achieved_in_percentage = $daily_net_achieved/($daily_target == 0 ? 1 : $daily_target);


            $daily_data []= [
                'user_body' => $buh_user,
                'daily_target' => $daily_target,
                'daily_net_achieved' => $daily_net_achieved,
                'daily_printing_costs' => $daily_printing_costs,
                'daily_refunds' => $todays_invoice_refunds,
                'daily_achieved' => $todays_invoice_totals,
                'daily_target_achieved_in_percentage' => $daily_target_achieved_in_percentage,
            ];
        }

        return view('livewire.revenue', compact('daily_data'))->extends($this->layout);
    }
}
