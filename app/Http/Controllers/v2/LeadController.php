<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LeadController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $leads = Lead::orderBy('id', 'desc')
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
            })->paginate(20);

        $brands = DB::table('brands')->get();

        return view('v2.lead.index', compact('leads', 'brands'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = Brand::all();
        $services = Service::all();
        $front_agents = DB::table('users')->where('is_employee', 0)->get();

        return view('v2.lead.create', compact('brands', 'front_agents', 'services'));
    }

    public function store (Request $request)
    {
        if (!v2_acl([2])) {
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
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$lead = Lead::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        $brands = Brand::all();
        $services = Service::all();
        $front_agents = DB::table('users')->where('is_employee', 0)->get();

        return view('v2.lead.edit', compact('lead', 'brands', 'services', 'front_agents'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$lead = Lead::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

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
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$lead = Lead::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.lead.show', compact('lead'));
    }
}
