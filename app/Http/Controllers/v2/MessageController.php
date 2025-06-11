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
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index()
    {
        $page = request()->get('page', 1);
        $perPage = 10;

        $client_user_ids = array_unique(Project::pluck('client_id')->toArray());
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

        // Restrict brand access logic
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true);
        $clients_with_messages->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
            return $q->whereHas('client', function ($q) use ($restricted_brands) {
                return $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand_id', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand_id', $restricted_brands)
                                ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date));
                        });
                });
            });
        });

        // Sort by latest message
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
            ->select('users.*')
            ->orderBy('messages.created_at', 'DESC')
            ->distinct();

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.client_list', [
                    'clients_with_messages' => $clients_with_messages->paginate($perPage, ['*'], 'page', $page)
                ])->render(),
                'next_page' => $page + 1,
                'has_more' => $clients_with_messages->paginate($perPage, ['*'], 'page', $page)->hasMorePages()
            ]);
        }

        return view('v2.message.index', [
            'clients_with_messages' => $clients_with_messages->paginate($perPage),
            'page' => $page
        ]);
    }

    public function getMessages($client_id)
    {
        $user = User::find($client_id);

        // Get all messages in ascending order (oldest to newest)
        $messages = Message::with('sended_client_files')
                        ->where('client_id', $client_id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        // Mark as read
        Message::where('client_id', $client_id)
            ->whereNull('is_read')
            ->update(['is_read' => Carbon::now()]);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.chat_messages', [
                    'messages' => $messages,
                    'user_name' => $user->name,
                    'is_employee' => $user->is_employee,
                    'user_image' => $user->image,
                ])->render(),
                'user_name' => $user->name
            ]);
        }

        return view('v2.message.index', compact('messages', 'user'));
    }
}
