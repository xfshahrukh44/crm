<?php

namespace App\Http\Controllers\v2;

use App\Models\Project;
use App\Models\User;
use App\Models\Message;
use App\Models\Task;
use App\Models\ClientFile;
use App\Notifications\MessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;
use Notification;
use DateTimeZone;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->is_employee == 2) {
            return $this->getMessageByAdmin($request);
        }

        if (Auth::user()->is_employee == 6) {
            return $this->getMessageByManager($request);
        }

        if (Auth::user()->is_employee == 4 && Auth::user()->is_support_head == 1) {
            return $this->getMessageBySupportHead($request);
        }

        if (Auth::user()->is_employee == 4) {
            return $this->getMessageBySupport($request);
        }

        if (Auth::user()->is_employee == 0) {
            return $this->getMessageBySale($request);
        }
    }

    public function getMessageByAdmin(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;

        $clientId = $request->get('clientId');

        $client = null;

        $client_user_ids = array_unique(Project::pluck('client_id')->toArray());

        $clients_with_messages = User::whereIn('users.id', $client_user_ids)
            ->when($request->filled('client_name'), function ($q) use ($request) {
                $search = $request->get('client_name');
                $q->where(function ($query) use ($search) {
                    $query->where(DB::raw('concat(name," ",last_name)'), 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");

                });
            });

        // Restrict by brands if applicable
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true);
        $clients_with_messages->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
            return $q->whereHas('client', function ($q) use ($restricted_brands) {
                $q->where(function ($query) use ($restricted_brands) {
                    $query->whereNotIn('brand_id', $restricted_brands)
                        ->orWhere(function ($subQuery) use ($restricted_brands) {
                            $subQuery->whereIn('brand_id', $restricted_brands)
                                    ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date));

                        });
                });
            });
        });

        // Sort by latest messages
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

        // If a particular clientId is provided, separate it first
        if ($clientId) {
            $client = User::find($clientId);
            // Exclude it from main pagination
            $clients_with_messages = $clients_with_messages->where('users.id', '!=', $clientId);
        }

        $paginated = $clients_with_messages->paginate($perPage, ['*'], 'page', $page);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.client_list', [
                    'clients_with_messages' => $paginated,
                    'client' => $client
                ])->render(),
                'next_page' => $page + 1,
                'has_more' => $paginated->hasMorePages()
            ]);
        }

        return view('v2.message.index', [
            'clients_with_messages' => $paginated,
            'client' => $client,
        ]);
    }

    public function getMessages($client_id)
    {
        $user = User::find($client_id);

        // Get all messages in ascending order (oldest to newest)
        $messages = Message::with('sended_client_files')
                        ->where('client_id', $client_id)
                        ->orderBy('id', 'asc')
                        ->get();

        // Mark as read
        Message::where('client_id', $client_id)
            ->whereNull('is_read')
            ->update(['is_read' => Carbon::now()]);

        $allClientFiles = $messages->pluck('sended_client_files')->flatten();

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

       $recentFiles = $allClientFiles
            ->filter(function ($file) use ($imageExtensions) {
                $ext = strtolower(pathinfo($file->path, PATHINFO_EXTENSION)); // FIXED
                return !in_array($ext, $imageExtensions);
            })
            ->sortByDesc('id')
            ->take(3)
            ->values();

        $recentImages = $allClientFiles
            ->filter(function ($file) use ($imageExtensions) {
                $ext = strtolower(pathinfo($file->path, PATHINFO_EXTENSION)); // FIXED
                return in_array($ext, $imageExtensions);
            })
            ->sortByDesc('id')
            ->take(5)
            ->values();

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.chat_messages', [
                    'messages' => $messages,
                    'user_name' => $user->name.' '.$user->last_name,
                    'is_employee' => $user->is_employee,
                    'user_image' => asset($user->image ?? 'assets/imgs/default-avatar.jpg'),
                    'client_files' => $recentFiles,
                    'client_images' => $recentImages,
                ])->render(),
                'user_name' => $user->name.' '.$user->last_name,
                'user_image' => asset($user->image ?? 'assets/imgs/default-avatar.jpg'),
                'client_files' => $recentFiles,
                'client_images' => $recentImages,
            ]);
        }

        return view('v2.message.index', compact('messages', 'user'));
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'message' => 'required'
        ]);
        $carbon = Carbon::now(new DateTimeZone('America/New_York'))->toDateTimeString();
        $task = Task::find($request->task_id);
        // send Notification to customer
        $message = new Message();
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        if($task == null){
            $message->sender_id = $request->client_id;
            $message->client_id = $request->client_id;
        }else{
            $message->sender_id = $task->projects->client->id;
            $message->client_id = $task->projects->client->id;
        }
        $message->role_id = 4;
        // $message->created_at = $carbon;
        $message->created_at = Carbon::now();
        $message->save();

        $attachments = [];

        if($request->hasfile('images')){
            $i = 0;
            foreach($request->file('images') as $file)
            {
                $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $file_actual_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $file_name = str_replace(" ", "-", $file_name);
                $name = $file_name . '-' . $i .time().'.'.$file->extension();
                $file->move(public_path().'/files/', $name);
                $i++;
                $client_file = new ClientFile();
                $client_file->name = $file_actual_name;
                $client_file->path = $name;
                $client_file->task_id = $request->task_id;
                $client_file->user_id = Auth()->user()->id;
                $client_file->user_check = Auth()->user()->is_employee;
                $client_file->production_check = 2;
                $client_file->message_id = $message->id;
                $client_file->created_at = $carbon;
                $client_file->save();

                $attachments[] = [
                    'name' => $file_name,
                    'path' => asset('files/' . $name),
                    'original_name' => $file->getClientOriginalName()
                ];
            }
        }

        $details = [
            'title' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has message on your task.',
            'body' => 'Please Login into your Dashboard to view it..'
        ];
        if($task != null){
            try {
                \Mail::to($task->projects->client->email)->send(new \App\Mail\ClientNotifyMail($details));
            } catch (\Exception $e) {

                $mail_error_data = json_encode([
                    'emails' => [$task->projects->client->email],
                    'body' => 'Please Login into your Dashboard to view it..',
                    'error' => $e->getMessage(),
                ]);

                \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
            }
        }else{
            $client = User::find($request->client_id);
            try {
                \Mail::to($client->email)->send(new \App\Mail\ClientNotifyMail($details));
            } catch (\Exception $e) {

                $mail_error_data = json_encode([
                    'emails' => [$client->email],
                    'body' => 'Please Login into your Dashboard to view it..',
                    'error' => $e->getMessage(),
                ]);

                \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
            }
        }
        $task_id = 0;
        $project_id = 0;
        if($task != null){
            $task_id = $task->projects->id;
            $project_id = $task->projects->id;
        }

        $messageData = [
            'id' => Auth()->user()->id,
            'task_id' => $task_id,
            'project_id' => $project_id,
            'name' => Auth()->user()->name . ' ' . Auth()->user()->last_name,
            'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
            'details' => Str::limit(filter_var($request->message, FILTER_SANITIZE_STRING), 40 ),
            'url' => '',
        ];
        if($task != null){
            $task->projects->client->notify(new MessageNotification($messageData));
        }else{
            $client = User::find($request->client_id);
            $client->notify(new MessageNotification($messageData));
        }
        // Message Notification sending to Admin
        $adminusers = User::where('is_employee', 2)->get();
        foreach($adminusers as $adminuser){
            Notification::send($adminuser, new MessageNotification($messageData));
        }

        //pusher notification
        if ($client_user = User::where('id', $request->client_id)->first()) {
            $pusher_notification_data = [
                'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
                'redirect_url' => route('client.home'),
            ];
            emit_pusher_notification(('message-channel-for-client-user-' . $client_user->id), 'new-message', $pusher_notification_data);
        }

        //send notification to client
        if ($task and $task->projects && $task->projects->client) {
            Notification::send($task->projects->client, new MessageNotification($messageData));

            //mail_notification
            $project = Project::find($task->project_id);
            $client_user = User::find($project->client_id);
            $client = Client::find($project->client->client->id ?? $client_user->client_id);
            $brand_id = $project->client->client->brand_id ?? ($project->brand_id);
            $brand = Brand::find($project->client->client->brand_id ?? $brand_id);

            $html = '<p>'. 'Hello ' . $client->name . ',' .'</p>';
            $html .= '<p>'. 'You have received a new message from your Project Manager, ('.Auth::user()->name.'), on our CRM platform. Please log in to your account to read the message and respond.' .'</p>';
            $html .= '<p>'. 'Access your messages here: ' . route('client.fetch.messages') .'</p>';
            $html .= '<p>'. 'Thank you for your prompt attention to this matter.' .'</p>';
            $html .= '<p>'. 'Best Regards,' .'</p>';
            $html .= '<p>'. $brand->name .'.</p>';

            mail_notification(
                '',
                [$client->email],
                'New Message from Your Project Manager on ('.$brand->name.') CRM',
                view('mail.crm-mail-template')->with([
                    'subject' => 'New Message from Your Project Manager on ('.$brand->name.') CRM',
                    'brand_name' => $brand->name,
                    'brand_logo' => asset($brand->logo),
                    'additional_html' => $html
                ]),
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message
        ]);
    }

    public function editMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required',
            'editmessage' => 'required',
        ]);
        $message = Message::find($request->message_id);
        if($message != null){
            $message->message = $request->editmessage;
            $message->save();

            return response()->json([
                'success' => true,
                'message' => 'Message edit successfully.',
                'text' => $message->message,
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'Message edit failed.',
        ]);
    }

    public function getMessageByManager(Request $request)
    {
        $client = null;
        $clientId = $request->get('clientId');
        $page = request()->get('page', 1);
        $perPage = 10;
        $filter = 0;
        $brands = DB::table('brands')->whereIn('id', auth()->user()->brand_list())->select('id', 'name')->get();
        $message_array = [];
        $data = User::where('is_employee', 3)->where('users.client_id', '!=', 0)
            ->whereHas('client', function ($q) {
                return $q->whereIn('brand_id', auth()->user()->brand_list());
            })
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
        $data->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
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

        if($request->brand != null){
            $data = $data->whereHas('client', function ($query) use ($request) {
                return $query->where('brand_id', $request->brand);
            });
        }else{
            $data = $data->whereHas('client', function ($query) {
                return $query->whereIn('brand_id', auth()->user()->brand_list());
            });
        }
        if($request->message != null){
            $message = $request->message;
            $data = $data->whereHas('messages', function ($query) use ($message) {
                return $query->where('message', 'like', '%' . $message . '%');
            });
        }



        //sort by latest message
        $latestMessages = DB::table('messages')
            ->select('client_id', DB::raw('MAX(created_at) as latest_message_date'))
            ->groupBy('client_id');


        $data = $data->joinSub($latestMessages, 'latest_messages', function ($join) {
            $join->on('users.id', '=', 'latest_messages.client_id');
        })
            ->join('messages', function ($join) {
                $join->on('users.id', '=', 'messages.client_id')
                    ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
            })
            ->select('users.*', 'messages.is_read') // Ensure only User attributes are selected
            ->orderBy('messages.created_at', 'DESC')
            ->distinct();


        // If a particular clientId is provided, separate it first
        if ($clientId) {
            $client = User::find($clientId);
            // Exclude it from main pagination
            $clients_with_messages = $data->where('users.id', '!=', $clientId);
        }

        $paginated = $data->paginate($perPage, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.client_list', [
                    'clients_with_messages' => $paginated,
                    'client' => $client
                ])->render(),
                'next_page' => $page + 1,
                'has_more' => $paginated->hasMorePages()
            ]);
        }

        return view('v2.message.index', [
            'clients_with_messages' => $paginated,
            'page' => $page,
            'route' => 'v2.messages.manager',
            'brands' => $brands,
            'filter' => $filter,
            'message_array' => $message_array,
            'client' => $client
        ]);
    }

    public function getMessageBySupportHead(Request $request)
    {
        $client = null;
        $clientId = $request->get('clientId');
        $page = request()->get('page', 1);
        $perPage = 10;
        $filter = 0;
        $brands = DB::table('brands')->whereIn('id', auth()->user()->brand_list())->select('id', 'name')->get();
        $message_array = [];
        $data = User::where('is_employee', 3)->where('users.client_id', '!=', 0)
            ->whereHas('client', function ($q) {
                return $q->whereIn('brand_id', auth()->user()->brand_list());
            })
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
        $data->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date), function ($q) use ($restricted_brands) {
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

        if($request->brand != null){
            $data = $data->whereHas('client', function ($query) use ($request) {
                return $query->where('brand_id', $request->brand);
            });
        }else{
            $data = $data->whereHas('client', function ($query) {
                return $query->whereIn('brand_id', auth()->user()->brand_list());
            });
        }
        if($request->message != null){
            $message = $request->message;
            $data = $data->whereHas('messages', function ($query) use ($message) {
                return $query->where('message', 'like', '%' . $message . '%');
            });
        }



        //sort by latest message
        $latestMessages = DB::table('messages')
            ->select('client_id', DB::raw('MAX(created_at) as latest_message_date'))
            ->groupBy('client_id');


        // $data = $data->joinSub($latestMessages, 'latest_messages', function ($join) {
        //     $join->on('users.id', '=', 'latest_messages.client_id');
        // })
        //     ->join('messages', function ($join) {
        //         $join->on('users.id', '=', 'messages.client_id')
        //             ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
        //     })
        //     ->select('users.*', 'messages.is_read') // Ensure only User attributes are selected
        //     ->orderBy('messages.created_at', 'DESC')
        //     ->distinct();


        $data = $data->leftJoinSub($latestMessages, 'latest_messages', function ($join) {
            $join->on('users.id', '=', 'latest_messages.client_id');
        })
            ->leftJoin('messages', function ($join) {
                $join->on('users.id', '=', 'messages.client_id')
                    ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
            })
            ->select('users.*', 'messages.created_at as message_created_at') // Optionally select message fields
            ->orderByDesc(DB::raw('IFNULL(messages.created_at, "1970-01-01 00:00:00")')) // Sort by message if exists, otherwise keep clients
            ->distinct();


        // If a particular clientId is provided, separate it first
        if ($clientId) {
            $client = User::find($clientId);
            // Exclude it from main pagination
            $clients_with_messages = $data->where('users.id', '!=', $clientId);
        }

        $paginated = $data->paginate($perPage, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.client_list', [
                    'clients_with_messages' => $paginated,
                    'client' => $client
                ])->render(),
                'next_page' => $page + 1,
                'has_more' => $paginated->hasMorePages()
            ]);
        }

        return view('v2.message.index', [
            'clients_with_messages' => $paginated,
            'page' => $page,
            'route' => 'v2.messages.manager',
            'brands' => $brands,
            'filter' => $filter,
            'message_array' => $message_array,
            'client' => $client
        ]);
    }

    public function getMessageBySupport(Request $request)
    {
        $client = null;
        $clientId = $request->get('clientId');
        $page = request()->get('page', 1);
        $perPage = 10;

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


        // $clients_with_messages = $clients_with_messages->joinSub($latestMessages, 'latest_messages', function ($join) {
        //     $join->on('users.id', '=', 'latest_messages.client_id');
        // })
        //     ->join('messages', function ($join) {
        //         $join->on('users.id', '=', 'messages.client_id')
        //             ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
        //     })
        //     ->select('users.*') // Ensure only User attributes are selected
        //     ->orderBy('messages.created_at', 'DESC')
        //     ->distinct();

        $clients_with_messages = $clients_with_messages->leftJoinSub($latestMessages, 'latest_messages', function ($join) {
            $join->on('users.id', '=', 'latest_messages.client_id');
        })
            ->leftJoin('messages', function ($join) {
                $join->on('users.id', '=', 'messages.client_id')
                    ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
            })
            ->select('users.*', 'messages.created_at as message_created_at') // Optionally select message fields
            ->orderByDesc(DB::raw('IFNULL(messages.created_at, "1970-01-01 00:00:00")')) // Sort by message if exists, otherwise keep clients
            ->distinct();



        // If a particular clientId is provided, separate it first
        if ($clientId) {
            $client = User::find($clientId);
            // Exclude it from main pagination
            $clients_with_messages = $clients_with_messages->where('users.id', '!=', $clientId);
        }

        $paginated = $clients_with_messages->paginate($perPage, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.client_list', [
                    'clients_with_messages' => $paginated,
                    'client' => $client
                ])->render(),
                'next_page' => $page + 1,
                'has_more' => $paginated->hasMorePages()
            ]);
        }

        return view('v2.message.index', [
            'clients_with_messages' => $clients_with_messages->paginate($perPage),
            'client' => $client
        ]);
    }

    public function getMessageBySale(Request $request)
    {
        return back()->with('error', 'You do not have permission to access this page.');
        $client = null;
        $clientId = $request->get('clientId');
        $page = request()->get('page', 1);
        $perPage = 10;

        $client_user_ids = array_unique(\App\Models\Client::where('user_id', auth()->id())->pluck('id')->toArray());

        $clients_with_messages = User::whereIn('users.client_id', $client_user_ids)
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


        $clients_with_messages = $clients_with_messages
            ->leftJoinSub($latestMessages, 'latest_messages', function ($join) {
                $join->on('users.id', '=', 'latest_messages.client_id');
            })
            ->leftJoin('messages', function ($join) {
                $join->on('users.id', '=', 'messages.client_id')
                    ->on('latest_messages.latest_message_date', '=', 'messages.created_at');
            })
            ->select('users.*', 'messages.created_at as message_created_at')
            ->orderByDesc(DB::raw('message_created_at IS NULL')) // Put users without messages last
            ->orderBy('message_created_at', 'DESC')
            ->distinct();

        // If a particular clientId is provided, separate it first
        if ($clientId) {
            $client = User::find($clientId);
            // Exclude it from main pagination
            $clients_with_messages = $clients_with_messages->where('users.id', '!=', $clientId);
        }

        $paginated = $clients_with_messages->paginate($perPage, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('v2.message.partials.client_list', [
                    'clients_with_messages' => $paginated,
                    'client' => $client
                ])->render(),
                'next_page' => $page + 1,
                'has_more' => $paginated->hasMorePages()
            ]);
        }

        return view('v2.message.index', [
            'clients_with_messages' => $clients_with_messages->paginate($perPage),
            'client' => $client
        ]);
    }
}
