<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\UserFinance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Revenue extends Component
{
    public $layout;

    protected $listeners = [
        'update_daily_target' => 'update_daily_target',
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
        if (auth()->user()->is_employee == 2) {
            return $this->admin_render();
        } else {
            $my_user_ids = DB::table('brand_users')->whereIn('brand_id', auth()->user()->brand_list())->pluck('user_id')->toArray();
            $buh_users = User::whereIn('is_employee', [0, 4, 6])->whereIn('id', $my_user_ids)->orderBy('name', 'ASC')
                ->get();
        }

        $daily_data = [];
        $monthly_data = [];
        foreach ($buh_users as $buh_user) {
            //front, support head, upsell
            if ($buh_user->is_employee == 4) {
                if ($buh_user->is_support_head != 1 && $buh_user->is_upsell != 1) {
                    continue;
                }
            }

            if ($buh_user->is_employee == 6) {
                if (!in_array($buh_user->id, [7, 1169, 33, 18, 4191, 4491])) {
                    continue;
                }
            }

            $todays_invoice_ids = DB::table('invoices')->whereIn('brand', $buh_user->brand_list())
                ->whereDate('created_at', '=', Carbon::today())
                ->where('sales_agent_id', $buh_user->id)
                ->where('payment_status', 2)->pluck('id')->toArray();
            $todays_invoice_totals = get_invoice_totals_in_usd($todays_invoice_ids);
            $todays_invoice_refunds = get_invoice_refunds_totals_in_usd($todays_invoice_ids);
            $daily_target = $buh_user->finances->daily_target ?? 1000.00;
            $daily_printing_costs = $buh_user->finances->daily_printing_costs ?? 0.00;
            $daily_net_achieved = $todays_invoice_totals - $daily_printing_costs - $todays_invoice_refunds;
            $daily_target_achieved_in_percentage = $daily_net_achieved/($daily_target == 0 ? 1 : $daily_target) * 100;


            $daily_data []= [
                'user_body' => $buh_user,
                'daily_target' => $daily_target,
                'daily_net_achieved' => $daily_net_achieved,
                'daily_printing_costs' => $daily_printing_costs,
                'daily_refunds' => $todays_invoice_refunds,
                'daily_achieved' => $todays_invoice_totals,
                'daily_target_achieved_in_percentage' => $daily_target_achieved_in_percentage,
            ];

            $this_months_invoice_ids = DB::table('invoices')->whereIn('brand', $buh_user->brand_list())
                ->whereDate('created_at', '>=', Carbon::today()->firstOfMonth())
                ->whereDate('created_at', '<=', Carbon::today()->lastOfMonth())
                ->where('sales_agent_id', $buh_user->id)
                ->where('payment_status', 2)->pluck('id')->toArray();
            $this_months_invoice_totals = get_invoice_totals_in_usd($this_months_invoice_ids);
            $this_months_invoice_refunds = get_invoice_refunds_totals_in_usd($this_months_invoice_ids);
            $monthly_target = $buh_user->finances->daily_target ?? 1000.00;
            $monthly_printing_costs = $buh_user->finances->daily_printing_costs ?? 0.00;
            $monthly_net_achieved = $this_months_invoice_totals - $monthly_printing_costs - $this_months_invoice_refunds;
            $monthly_target_achieved_in_percentage = $monthly_net_achieved/($monthly_target == 0 ? 1 : $monthly_target) * 100;


            $monthly_data []= [
                'user_body' => $buh_user,
                'monthly_target' => $monthly_target,
                'monthly_net_achieved' => $monthly_net_achieved,
                'monthly_printing_costs' => $monthly_printing_costs,
                'monthly_refunds' => $this_months_invoice_refunds,
                'monthly_achieved' => $this_months_invoice_totals,
                'monthly_target_achieved_in_percentage' => $monthly_target_achieved_in_percentage,
            ];
        }

//        dd($this->emit('init_update_daily_target', '.daily_target'));
        return view('livewire.revenue', compact('daily_data', 'monthly_data'))->extends($this->layout);
    }

    public function admin_render () {
        $buh_users = User::where('is_employee', 6)->orderBy('name', 'ASC')
            //restricted buhs
            ->whereIn('id', [7, 1169, 33, 18, 4191, 4491])
            ->get();

        $buh_data = [];
        foreach ($buh_users as $buh_user) {
            $my_user_ids = DB::table('brand_users')->whereIn('brand_id', $buh_user->brand_list())->pluck('user_id')->toArray();
            $my_user_ids []= $buh_user->id;
            $my_user_ids = array_unique($my_user_ids);

            $sale_agents = User::whereIn('is_employee', [0, 4, 6])->whereIn('id', $my_user_ids)->orderBy('name', 'ASC')
                ->get();

            $daily_data = [];
            $monthly_data = [];
            foreach ($sale_agents as $sale_agent) {
                //front, support head, upsell
                if ($sale_agent->is_employee == 4) {
                    if ($sale_agent->is_support_head != 1 && $sale_agent->is_upsell != 1) {
                        continue;
                    }
                }

                if ($sale_agent->is_employee == 6) {
                    if (!in_array($sale_agent->id, [7, 1169, 33, 18, 4191, 4491])) {
                        continue;
                    }
                }

                $todays_invoice_ids = DB::table('invoices')->whereIn('brand', $buh_user->brand_list())
                    ->whereDate('created_at', '=', Carbon::today())
                    ->where('sales_agent_id', $sale_agent->id)
                    ->where('payment_status', 2)->pluck('id')->toArray();
                $todays_invoice_totals = get_invoice_totals_in_usd($todays_invoice_ids);
                $todays_invoice_refunds = get_invoice_refunds_totals_in_usd($todays_invoice_ids);
                $daily_target = $sale_agent->finances->daily_target ?? 1000.00;
                $daily_printing_costs = $sale_agent->finances->daily_printing_costs ?? 0.00;
                $daily_net_achieved = $todays_invoice_totals - $daily_printing_costs - $todays_invoice_refunds;
                $daily_target_achieved_in_percentage = $daily_net_achieved/($daily_target == 0 ? 1 : $daily_target) * 100;


                $daily_data []= [
                    'user_body' => $sale_agent,
                    'daily_target' => $daily_target,
                    'daily_net_achieved' => $daily_net_achieved,
                    'daily_printing_costs' => $daily_printing_costs,
                    'daily_refunds' => $todays_invoice_refunds,
                    'daily_achieved' => $todays_invoice_totals,
                    'daily_target_achieved_in_percentage' => $daily_target_achieved_in_percentage,
                ];

                $this_months_invoice_ids = DB::table('invoices')->whereIn('brand', $buh_user->brand_list())
                    ->whereDate('created_at', '>=', Carbon::today()->firstOfMonth())
                    ->whereDate('created_at', '<=', Carbon::today()->lastOfMonth())
                    ->where('sales_agent_id', $sale_agent->id)
                    ->where('payment_status', 2)->pluck('id')->toArray();
                $this_months_invoice_totals = get_invoice_totals_in_usd($this_months_invoice_ids);
                $this_months_invoice_refunds = get_invoice_refunds_totals_in_usd($this_months_invoice_ids);
                $monthly_target = $sale_agent->finances->daily_target ?? 1000.00;
                $monthly_printing_costs = $sale_agent->finances->daily_printing_costs ?? 0.00;
                $monthly_net_achieved = $this_months_invoice_totals - $monthly_printing_costs - $this_months_invoice_refunds;
                $monthly_target_achieved_in_percentage = $monthly_net_achieved/($monthly_target == 0 ? 1 : $monthly_target) * 100;


                $monthly_data []= [
                    'user_body' => $sale_agent,
                    'monthly_target' => $monthly_target,
                    'monthly_net_achieved' => $monthly_net_achieved,
                    'monthly_printing_costs' => $monthly_printing_costs,
                    'monthly_refunds' => $this_months_invoice_refunds,
                    'monthly_achieved' => $this_months_invoice_totals,
                    'monthly_target_achieved_in_percentage' => $monthly_target_achieved_in_percentage,
                ];
            }

            $buh_data []= [
                'team_name' => $buh_user->name,
                'buh_id' => $buh_user->id,
                'buh' => $buh_user,
                'daily_data' => $daily_data,
                'monthly_data' => $monthly_data,
            ];
        }


        return view('livewire.admin-revenue', compact('buh_data'))->extends($this->layout);
    }

    public function update_daily_target($user_id, $amount){
        UserFinance::updateOrCreate([
            'user_id' => $user_id,
        ], [
            'daily_target' => $amount
        ]);

        $this->render();
    }

    public function update_daily_printing_costs($user_id, $amount){
        UserFinance::updateOrCreate([
            'user_id' => $user_id,
        ], [
            'daily_printing_costs' => $amount
        ]);

        $this->render();
    }
}
