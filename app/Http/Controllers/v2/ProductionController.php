<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $search = request()->get('search');
        $users = User::whereIn('is_employee', [1, 5])
            ->when(request()->has('search') && !is_null($search) && $search != '', function ($q) use ($search) {
                return get_user_search($q, $search);
            })->orderBy('created_at', 'DESC')->paginate(10);

        return view('v2.production.index', compact('users'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.production.create');
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
            'is_employee' => 'required|in:1,5',
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->password = Hash::make($request->input('password'));
        $user->is_employee = $request->input('is_employee');
        $user->is_support_head = false;
        $user->save();

//        if ($request->has('brand')) {
//            $user->brands()->sync($request->input('brand'));
//        }
        if ($request->has('category_id')) {
            $user->category()->sync($request->input('category_id'));
        }

        return redirect()->route('v2.users.production')->with('success','Production created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$user = User::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.production.edit', compact('user'));
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
            'is_employee' => 'required|in:1,5',
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
        $user->is_employee = $request->input('is_employee');
        $user->is_support_head = false;
        $user->save();
        $user->category()->sync($request->get('category_id'));

        return redirect()->route('v2.users.production')->with('success','Production Updated Successfully.');
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
