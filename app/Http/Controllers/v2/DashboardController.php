<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.dashboard');
    }

    public function revenue (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

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
                $daily_target_achieved_in_percentage = ($daily_net_achieved / ($daily_target == 0 ? 1 : $daily_target)) * 100;


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
                $monthly_target_achieved_in_percentage = ($monthly_net_achieved / ($monthly_target == 0 ? 1 : $monthly_target)) * 100;


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


//        return view('livewire.admin-revenue', compact('buh_data'))->extends($this->layout);

        return view('v2.revenue', compact('buh_data'));
    }

    public function briefsPending (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $user_ids = get_brief_client_user_ids($request);

        $client_users_with_brief_pendings = User::whereIn('id', $user_ids)->orderBy('created_at', 'DESC')->paginate(10);

        return view('v2.briefs-pending', compact('client_users_with_brief_pendings'));
    }

    public function pendingProjects (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $user_ids = get_project_client_user_ids();

        $client_users_with_pending_projects = User::whereIn('id', $user_ids)
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('id', $request->get('user_id'));
//            })
            ->when($request->has('client_name') && $request->get('client_name') != '', function ($q) use ($request) {
                return get_user_search($q, $request->get('client_name'));
            })
            ->orderBy('created_at', 'DESC')->paginate(10);

        return view('v2.pending-projects', compact('client_users_with_pending_projects'));
    }
}
