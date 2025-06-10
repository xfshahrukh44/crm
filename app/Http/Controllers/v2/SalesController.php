<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use App\Models\UserFinance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SalesController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $search = request()->get('search');
        $users = User::whereIn('is_employee', [0, 4, 6])
            ->when(request()->has('search') && !is_null($search) && $search != '', function ($q) use ($search) {
                return $q->where(function ($q) use ($search) {
                    return get_user_search($q, $search);
                })->orWhereHas('brands', function ($q) use ($search) {
                    return $q->where('name', 'LIKE', '%'.$search.'%');
                });
            })->orderBy('created_at', 'DESC')->paginate(20);

        return view('v2.sales.index', compact('users'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.sales.create');
    }

    public function store (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'status' => 'required',
            'password' => 'required',
            'is_employee' => 'required|in:0,4,6,8',
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->password = Hash::make($request->input('password'));
        $user->is_employee = ($request->input('is_employee') == 8) ? 4 : $request->input('is_employee');
        $user->is_support_head = ($request->input('is_employee') == 8) ? true : false;

        //restricted brands
        $user->restricted_brands = json_encode($request->get('restricted_brands') ?? []);
        $user->restricted_brands_cutoff_date = $request->get('restricted_brands_cutoff_date') ?? null;

        $user->save();

        UserFinance::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'daily_target' => $request->get('daily_target') ?? 1000.00,
            'daily_printing_costs' => $request->get('daily_printing_costs') ?? 0.00,
        ]);

        if ($request->has('brand_id')) {
            $user->brands()->sync($request->input('brand_id'));
        }

        return redirect()->route('v2.users.sales')->with('success','Sale Person Created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$user = User::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.sales.edit', compact('user'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'status' => 'required',
            'is_employee' => 'required|in:0,4,6,8',
        ]);

        if (!$user = User::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->pseudo_name = $request->input('pseudo_name');
        $user->is_upsell = $request->input('is_upsell');

        if($request->input('password') != "")
        {
            $user->password = Hash::make($request->input('password'));
        }

        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->is_employee = ($request->input('is_employee') == 8) ? 4 : $request->input('is_employee');
        $user->is_support_head = ($request->input('is_employee') == 8) ? true : false;

        //restricted brands
        $user->restricted_brands = json_encode($request->get('restricted_brands') ?? []);
        $user->restricted_brands_cutoff_date = $request->get('restricted_brands_cutoff_date') ?? null;

        $user->brands()->sync($request->get('brand_id'));
        $user->save();

        UserFinance::updateOrCreate([
            'user_id' => $id,
        ], [
            'daily_target' => $request->get('daily_target') ?? 1000.00,
            'daily_printing_costs' => $request->get('daily_printing_costs') ?? 0.00,
        ]);

        return redirect()->route('v2.users.sales')->with('success','Sale Person Updated Successfully.');
    }

    public function show (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$client = Client::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.client.show', compact('client'));
    }
}
