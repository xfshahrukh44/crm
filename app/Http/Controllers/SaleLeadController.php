<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleLeadController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Lead::where('brand', auth()->user()->brand_list())->where('user_id', auth()->id());
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

            //restricted brand access
            $restricted_brands = json_decode(auth()->user()->restricted_brands, true); // Ensure it's an array
            $data->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            });

            $data = $data->paginate(20);
            $brands = DB::table('brands')->whereIn('id', auth()->user()->brand_list())->get();
            return view('sale.lead.index', compact('data', 'brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $brands = Brand::all();
        $services = Service::all();

        return view('sale.lead.create', compact('brands', 'services'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
//                'user_id' => 'required',
                'email' => 'required|email|unique:clients,email',
                'status' => 'required',
                'brand' => 'required',
            ]);

            if ($user_check = DB::table('users')->where('email', $request->email)->first()) {
                return redirect()->back()->with('error', 'Email already taken');
            }

//            $request->request->add(['user_id' => auth()->user()->id]);
            $data = $request->except('service');
            $data['user_id'] = auth()->id();
            $lead = Lead::create($data);
            if ($request->has('service')) {
                $lead->service = implode(',', $request->service);
                $lead->save();
            }

//            //create stripe customer
//            create_leads_merchant_accounts($lead->id);

//            if ($request->has('redirect_to_lead_detail')) {
//                return redirect()->route('leads.detail', $lead->id)->with('success', 'Client created Successfully.');
//            }

            return redirect()->route('sale.lead.index')->with('success', 'Lead created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Lead $lead)
    {
        return view('sale.lead.show', compact('lead'));
    }

    public function edit($id)
    {
        try {
            $data = Lead::find($id);
            $brands = Brand::all();
            $services = Service::all();

            return view('sale.lead.edit', compact('data', 'brands', 'services'));
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
                'email' => 'required|email|unique:clients,email',
                'status' => 'required',
                'brand' => 'required',
            ]);

            $data = $request->except('service');
            $data['user_id'] = auth()->id();
            $lead->update($data);

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

                return redirect()->back()->with('success', 'Client Onboarded Successfully.');
            }

            return redirect()->route('sale.lead.index')->with('success', 'Lead Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Lead $lead)
    {
        return redirect()->back()->with('error', 'Permission denied.');
    }
}
