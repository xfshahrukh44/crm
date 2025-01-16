<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerLeadController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Lead::where('brand', auth()->user()->brand_list());
            $data = $data->orderBy('id', 'desc');
            if($request->name != ''){
                $data = $data->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$request->name.'%')
                    ->orWhere('name', 'like', '%'.$request->name.'%')
                    ->orWhere('last_name', 'like', '%'.$request->name.'%');
            }
            if($request->email != ''){
                $data = $data->where('email', 'LIKE', "%$request->email%");
            }
            if($request->brand != ''){
                $data = $data->where('brand', $request->brand);
            }
            if($request->status != ''){
                $data = $data->where('status', $request->status);
            }
            $data = $data->paginate(20);
            $brands = DB::table('brands')->whereIn('id', auth()->user()->brand_list())->get();
            return view('manager.lead.index', compact('data', 'brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $brands = Brand::whereIn('id', auth()->user()->brand_list())->get();
        $services = Service::all();
        $user_ids = DB::table('brand_users')->whereIn('brand_id', auth()->user()->brand_list())->pluck('user_id')->toArray();
        $front_agents = DB::table('users')->where('is_employee', 0)->whereIn('id', $user_ids)->get();

        return view('manager.lead.create', compact('brands', 'front_agents', 'services'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'user_id' => 'required',
                'email' => 'required|email|unique:clients,email',
                'status' => 'required',
                'brand' => 'required',
            ]);

            if ($user_check = DB::table('users')->where('email', $request->email)->first()) {
                return redirect()->back()->with('error', 'Email already taken');
            }

//            $request->request->add(['user_id' => auth()->user()->id]);
            $lead = Lead::create($request->except('service'));
            if ($request->has('service')) {
                $lead->service = implode(',', $request->service);
                $lead->save();
            }

//            //create stripe customer
//            create_leads_merchant_accounts($lead->id);

//            if ($request->has('redirect_to_lead_detail')) {
//                return redirect()->route('leads.detail', $lead->id)->with('success', 'Client created Successfully.');
//            }

            return redirect()->route('manager.lead.index')->with('success', 'Lead created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Lead $lead)
    {
        return view('manager.lead.show', compact('lead'));
    }

    public function edit($id)
    {
        try {
            $data = Lead::find($id);
            $brands = Brand::whereIn('id', auth()->user()->brand_list())->get();
            $services = Service::all();
            $user_ids = DB::table('brand_users')->whereIn('brand_id', auth()->user()->brand_list())->pluck('user_id')->toArray();
            $front_agents = DB::table('users')->where('is_employee', 0)->whereIn('id', $user_ids)->get();

            return view('manager.lead.edit', compact('data', 'brands', 'services', 'front_agents'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Lead $lead)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'user_id' => 'required',
                'email' => 'required|email|unique:clients,email',
                'status' => 'required',
                'brand' => 'required',
            ]);

            $lead->update($request->except('service'));

            if ($request->has('service')) {
                $lead->service = implode(',', $request->service);
                $lead->save();
            }

            //if onboarded
            if($request->status == 'Onboarded' && is_null($lead->client_id)) {
                $client = Client::create([
                    'name' => $lead->name,
                    'last_name' => $lead->last_name,
                    'email' => $lead->email,
                    'contact' => $lead->contact,
                    'brand_id' => $lead->brand,
                    'service' => $lead->service,
                    'show_service_forms' => $lead->service,
                ]);

                return redirect()->route('manager.generate.payment', $client->id)->with('success', 'Client Onboarded Successfully.');
            }

            return redirect()->route('manager.lead.index')->with('success', 'Lead Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Lead $lead)
    {
        try {
            $lead->delete();
            return redirect()->back()->with('success', 'Lead Deleted Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
