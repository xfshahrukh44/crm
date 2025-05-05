<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Project;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportClientController extends Controller
{
    public function index(Request $request)
    {
        $data = new Client;
//        $data = $data->where('user_id', Auth()->user()->id);
        if (auth()->user()->is_support_head == 1) {
            $data = $data->whereIn('brand_id', auth()->user()->brand_list());
        } else {
            $client_ids = array_unique(Project::where('user_id', auth()->id())->pluck('client_id')->toArray());
            dump('clients count')
            dd(count($client_ids));
            $data = $data->whereIn('brand_id', auth()->user()->brand_list())->whereIn('id', $client_ids);
        }
        $data = $data->orderBy('priority', 'ASC');
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
        if($request->task_id != ''){
            $data = $data
                ->where(function ($q) {
                    return $q->whereHas('user', function ($q) {
                        return $q->whereHas('projects', function ($q) {
                            return $q->whereHas('tasks', function ($q) {
                                return $q->where('id', request()->get('task_id'));
                            });
                        });
                    });
                })
                ->orWhere(function ($q) {
                    return $q->whereHas('projects', function ($q) {
                        return $q->whereHas('tasks', function ($q) {
                            return $q->where('id', request()->get('task_id'));
                        });
                    });
                })->whereIn('brand_id', auth()->user()->brand_list());
        }

        //restricted brand access
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true); // Ensure it's an array
        $data->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
            return $q->where(function ($query) use ($restricted_brands) {
                $query->whereNotIn('brand_id', $restricted_brands)
                    ->orWhere(function ($subQuery) use ($restricted_brands) {
                        $subQuery->whereIn('brand_id', $restricted_brands)
                            ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                    });
            });
        });

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

        //create stripe customer
        create_clients_merchant_accounts($client->id);

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

        $sale_agents = User::whereIn('is_employee', [0, 4, 6])
            ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
            ->get();

        if (request()->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return view('support.payment.create', compact('user', 'brand', 'currencies', 'services', 'merchant', 'sale_agents'));
    }

    public function getBriefPendingById(Request $request){
        //change
        $client_users_with_brief_pendings = User::whereIn('id', get_brief_client_user_ids($request))->get();

//        return view('sale.brief.pending', compact('client_users_with_brief_pendings', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form'));
        return view('support.brief.pending', compact('client_users_with_brief_pendings'));
    }
}
