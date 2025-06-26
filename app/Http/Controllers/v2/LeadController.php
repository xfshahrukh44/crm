<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 6, 0])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        //restricted brand access
        $restricted_brands = get_restricted_brands();

        $leads = Lead::orderBy('id', 'desc')
            ->when(v2_acl([0]), function ($q) {
                return $q->where('user_id', auth()->id());
            })
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand', auth()->user()->brand_list());
            })
            ->when($request->name != '', function ($q) use ($request) {
                return $q->where(function ($q) use ($request) {
                    return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.$request->name.'%')
                    ->orWhere('name', 'like', '%'.$request->name.'%')
                    ->orWhere('last_name', 'like', '%'.$request->name.'%');
                });
            })
            ->when($request->email != '', function ($q) use ($request) {
                return $q->where('email', 'LIKE', "%$request->email%");
            })
            ->when($request->brand != '', function ($q) use ($request) {
                return $q->where('brand', $request->brand);
            })
            ->when($request->status != '', function ($q) use ($request) {
                return $q->where('status', $request->status);
            })->when(!v2_acl([2]) && !empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            })->paginate(20);

        $brands = $this->getBrands();

        return view('v2.lead.index', compact('leads', 'brands'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = $this->getBrands();
        $services = Service::all();
        $front_agents = $this->getFrontAgents();

        return view('v2.lead.create', compact('brands', 'front_agents', 'services'));
    }

    public function store (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'user_id' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand' => 'required',
        ]);

        //non-admin checks
        if (!v2_acl([2])) {
            if (!in_array($request->user_id, $this->getUserIDs()) || !in_array($request->brand, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        if ($user_check = DB::table('users')->where('email', $request->email)->first()) {
            return redirect()->back()->with('error', 'Email already taken');
        }

//            $request->request->add(['user_id' => auth()->user()->id]);
        $lead = Lead::create($request->except('service'));
        if ($request->has('service')) {
            $lead->service = implode(',', $request->service);
            $lead->save();
        }

        return redirect()->route('v2.leads')->with('success','Lead created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2, 6, 0])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$lead = Lead::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non-admin checks
        if (!v2_acl([2])) {
            if (!v2_acl([0]) && !in_array($lead->brand, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([0]) && $lead->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $brands = $this->getBrands();
        $services = Service::all();
        $front_agents = $this->getFrontAgents();

        return view('v2.lead.edit', compact('lead', 'brands', 'services', 'front_agents'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'user_id' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand' => 'required',
        ]);

        if (!$lead = Lead::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non-admin checks
        if (!v2_acl([2])) {
            if (!in_array($lead->brand, auth()->user()->brand_list()) || !in_array($request->user_id, $this->getUserIDs()) || !in_array($request->brand, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([0]) && $lead->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        $lead->update($request->except('service'));

        if ($request->has('service')) {
            $lead->service = implode(',', $request->service);
            $lead->save();
        }

        //if onboarded
        if($request->status == 'Onboarded' && is_null($lead->client_id)) {
            Client::create([
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

        return redirect()->route('v2.leads')->with('success','Lead updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2, 6, 0])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$lead = Lead::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        //non-admin checks
        if (!v2_acl([2])) {
            if (!v2_acl([0]) && !in_array($lead->brand, auth()->user()->brand_list())) {
                return redirect()->back()->with('error', 'Not allowed.');
            }

            if (v2_acl([0]) && $lead->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Not allowed.');
            }
        }

        return view('v2.lead.show', compact('lead'));
    }

    public function getBrands ()
    {
        return \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();
    }

    public function getFrontAgents ()
    {
        return DB::table('users')->where('is_employee', 0)
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', $this->getUserIDs());
            })
            ->get();
    }

    public function getUserIDs ()
    {
        return DB::table('brand_users')->whereIn('brand_id', auth()->user()->brand_list())->pluck('user_id')->toArray();
    }
}
