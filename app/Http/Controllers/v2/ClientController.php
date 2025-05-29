<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.client.index');
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.client.create');
    }

    public function store (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);

        //user check
        if (
            DB::table('users')->where('email', $request->email)->first() ||
            DB::table('clients')->where('email', $request->email)->first()
        ) { return redirect()->back()->with('error', 'Email already taken'); }

        $client = Client::create($request->except('_token'));

        //create user record
        DB::table('users')->insert([
            'name' => $client->name,
            'last_name' => $client->last_name,
            'email' => $client->contact,
            'contact' => $client->contact,
            'status' => 1,
            'password' => Hash::make('qwerty'),
            'is_employee' => 3,
            'client_id' => $client->id,
        ]);

        //create stripe customer
        create_clients_merchant_accounts($client->id);

        return redirect()->route('v2.invoices.create', $client->id)->with('success','Client created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$client = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.client.edit', compact('client'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$client = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        $request->validate([
            'name' => 'required',
            'brand_id' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:clients,email,'.$client->id,
//            'email' => 'required' . !is_null($client->user) ? ('|unique:users,email,'.$client->user->id) : '',
            'status' => 'required',
        ]);

        $client->update($request->all());

        return redirect()->route('v2.clients')->with('success','Client updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$client = Client::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.client.show', compact('client'));
    }

//    public function generatePayment (Request $request, $id)
//    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        $user = Client::find($id);
//        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();
//        $services = Service::all();
//        $currencies =  Currency::all();
//        $merchant = Merchant::where('status', 1)->orderBy('id', 'desc')->get();
//
//        $sale_agents = User::whereIn('is_employee', [0, 4, 6])
//            ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
//            ->get();
//
//        return view('v2.invoice.create', compact('user', 'brand', 'currencies', 'services', 'merchant', 'sale_agents'));
//    }
}
