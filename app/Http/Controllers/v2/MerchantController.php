<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.merchant.index');
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.merchant.create');
    }

    public function store (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'public_key' => 'required',
            'secret_key' => 'required',
            'status' => 'required',
        ]);

        $merchant = new Merchant();
        $merchant->name = $request->name;
        $merchant->public_key = $request->public_key;
        $merchant->secret_key = $request->secret_key;
        $merchant->status = $request->status;
        $merchant->login_id = $request->login_id;
        $merchant->is_authorized = $request->is_authorized ?? '0';
        $merchant->save();
        return redirect()->route('v2.merchants')->with('success','Merchant created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$merchant = Merchant::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.merchant.edit', compact('merchant'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$merchant = Merchant::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        $request->validate([
            'name' => 'required',
            'public_key' => 'required',
            'secret_key' => 'required',
            'status' => 'required',
        ]);

        $merchant->update($request->all());

        return redirect()->route('v2.merchants')->with('success','Merchant updated Successfully.');
    }
}
