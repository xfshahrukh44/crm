<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportClientController extends Controller
{
    public function index(Request $request)
    {
        $data = new Client;
//        $data = $data->where('user_id', Auth()->user()->id);
        $data = $data->whereIn('brand_id', auth()->user()->brand_list());
        $data = $data->orderBy('id', 'desc');
        if($request->name != ''){
            $data = $data->whereHas('user', function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%'.$request->name.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$request->name.'%');
            });
        }
        if($request->email != ''){
            $data = $data->where('email', 'LIKE', '%'.$request->email.'%')->orWhereHas('user', function ($q) use ($request) {
                return $q->where('email', 'LIKE', '%'.$request->email.'%');
            });
        }
        if($request->contact != ''){
            $data = $data->whereHas('user', function ($q) use ($request) {
                return $q->where('contact', 'LIKE', '%'.$request->contact.'%');
            });
        }
        if($request->status != ''){
            $data = $data->where('status', $request->status);
        }
        $data = $data->paginate(10);
        return view('support.client.index', compact('data'));
    }

    public function create()
    {
        return view('support.client.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);

        if ($user_check = User::where('email', $request->email)->first()) {
            return redirect()->back()->with('error', 'Email already taken');
        }

        $request->request->add(['user_id' => auth()->user()->id]);
        $client = Client::create($request->all());

        if ($request->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return redirect()->route('support.client.generate.payment', $client->id);
    }

    public function show(Client $client)
    {
        //
    }

    public function edit($id)
    {
//        $data = Client::where('id', $id)->where('user_id', Auth::user()->id)->first();
        $data = Client::where('id', $id)
//            ->where('user_id', Auth::user()->id)
            ->first();
        if($data == null){
            return redirect()->back();
        }else{
            return view('support.client.edit', compact('data'));
        }
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'status' => 'required',
        ]);
        $client->update($request->all());
        return redirect()->back()->with('success', 'Client Updated Successfully.');
    }


    public function destroy(Client $client)
    {
        //
    }

    public function paymentLink($id){
        $user = Client::find($id);
        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant = Merchant::orderBy('id', 'desc')->get();

        if (request()->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return view('support.payment.create', compact('user', 'brand', 'currencies', 'services', 'merchant'));
    }

    public function getBriefPendingById(Request $request){
        //change
        $client_users_with_brief_pendings = User::whereIn('id', get_brief_client_user_ids($request))->get();

//        return view('sale.brief.pending', compact('client_users_with_brief_pendings', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form'));
        return view('support.brief.pending', compact('client_users_with_brief_pendings'));
    }
}
