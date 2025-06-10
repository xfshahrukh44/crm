<?php

namespace App\Http\Controllers\v2;

use App\Models\Project;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function index()
    {
        $client_user_ids = array_unique(Project::whereIn('brand_id', auth()->user()->brand_list())->where('user_id', auth()->id())->pluck('client_id')->toArray());
        $clients_with_messages = User::whereIn('users.id', $client_user_ids)
            ->when(request()->has('client_name'), function ($q) {
                return $q->whereHas('client', function ($q) {
                    return $q->when(request()->has('client_name'), function ($q) {
                        return $q->where(DB::raw('concat(name," ",last_name)'), 'like', '%'.request()->get('client_name').'%')
                            ->orWhere('name', 'LIKE', "%".request()->get('client_name')."%")
                            ->orWhere('last_name', 'LIKE', "%".request()->get('client_name')."%")
                            ->orWhere('email', 'LIKE', "%".request()->get('client_name')."%")
                            ->orWhere('contact', 'LIKE', "%".request()->get('client_name')."%");
                    });
                });
            });

        //restricted brand access
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true); // Ensure it's an array
        $clients_with_messages->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
            return $q->whereHas('client', function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand_id', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand_id', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                        });
                });
            });
        });

        //sort by latest message
        $latestMessages = DB::table('messages')
            ->select('client_id', DB::raw('MAX(created_at) as latest_message_date'))
            ->groupBy('client_id');


        $clients_with_messages = $clients_with_messages->joinSub($latestMessages, 'latest_messages', function ($join) {
            $join->on('users.id', '=', 'latest_messages.client_id');
        })
            ->join('messages', function ($join) {
                $join->on('users.id', '=', 'messages.client_id')
                    ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
            })
            ->select('users.*') // Ensure only User attributes are selected
            ->orderBy('messages.created_at', 'DESC')
            ->distinct()
            ->paginate(10);

        return view('v2.message.index', compact('clients_with_messages'));
    }
}
