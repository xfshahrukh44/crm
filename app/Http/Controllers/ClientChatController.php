<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Response;
use App\Models\User;
use App\Models\Message;
use App\Models\ClientFile;
use App\Models\Project;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Events\MessageSent;
use Illuminate\Support\Str;
use App\Notifications\MessageNotification;
use Auth;
use Notification;
use \Carbon\Carbon;
use DateTimeZone;

class ClientChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    
    public function clientHome(){
        return view('client.home');
    }

    public function clientChat(){
        return view('client.chat');
    }

    public function fetchMessages()
    {
        return Message::where('user_id', Auth()->user()->id)->orWhere('recieve_id', Auth()->user()->id)->with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        try {
            $this->validate($request, [
                'message' => 'required',
            ]);
            $carbon = Carbon::now(new DateTimeZone('America/Los_Angeles'))->toDateTimeString();
            // send Notification to customer
            $message = new Message();
            $message->user_id = Auth::user()->id;
            $message->sender_id = Auth::user()->id;
            $message->message = $request->message;
            $message->role_id = 3;
            $message->created_at = $carbon;
            $message->client_id = Auth::user()->id;
            $message->save();

            if($request->hasfile('h_Item_Attachments_FileInput'))
            {
                $files_array = array();
                $i = 0;
                foreach($request->file('h_Item_Attachments_FileInput') as $file)
                {
                    $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $name = $file_name . '_' . $i . '_]' .time().'.'.$file->extension();
                    $file->move(public_path().'/files/', $name);
                    $i++;
                    $client_file = new ClientFile();
                    $client_file->name = $file_name;
                    $client_file->path = $name;
                    $client_file->task_id = -1;
                    $client_file->user_id = Auth()->user()->id;
                    $client_file->user_check = Auth()->user()->is_employee;
                    $client_file->production_check = 2;
                    $client_file->message_id = $message->id;
                    $client_file->created_at = $carbon;
                    $client_file->save();
                }
            }
            $details = [
                'title' => Auth::user()->name . ' ' . Auth::user()->last_name . ' send you a message.',
                'body' => 'Please Login into your Dashboard to view it..'
            ];
            $messageData = [
                'id' => Auth()->user()->id,
                'name' => Auth()->user()->name . ' ' . Auth()->user()->last_name,
                'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
                'details' => Str::limit(filter_var($request->message, FILTER_SANITIZE_STRING), 40 ),
                'url' => '',
            ];

            $sale = User::find(Auth::user()->client->user_id);
//            if ($sale) {
//                $sale->notify(new MessageNotification($messageData));
//                try {
//                    \Mail::to($sale->email)->send(new \App\Mail\ClientNotifyMail($details));
//                } catch (\Exception $e) {
//
//                    $mail_error_data = json_encode([
//                        'emails' => [$sale->email],
//                        'body' => 'Please Login into your Dashboard to view it..',
//                        'error' => $e->getMessage(),
//                    ]);
//
//                    \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
//                }
//            }

            //support notification
            if ($client = Client::find(Auth::user()->client_id)) {
                $project_assigned_support_ids = Project::where('client_id', auth()->user()->id)->where('brand_id', $client->brand_id)->pluck('user_id')->toArray();
                if ($sale) { $project_assigned_support_ids []= $sale->id; }
                $support_ids = DB::table('brand_users')->where('brand_id', $client->brand_id)->pluck('user_id')->toArray();
                $support_ids = DB::table('users')->where(['is_employee' => 4, 'is_support_head' => 1])->whereIn('id', $support_ids)->pluck('id')->toArray();
                $project_assigned_support_ids = array_merge($project_assigned_support_ids, $support_ids);
                foreach (
//                    User::whereIn('id', DB::table('brand_users')
//                        ->where('brand_id', $client->brand_id)
//                        ->pluck('user_id')->toArray()
//                    )->where('is_employee', 4)->where('is_support_head', 1)->get()
                    User::whereIn('id', $project_assigned_support_ids)->get()
                    as $support_member
                ) {
                    try {
                        \Mail::to($support_member->email)->send(new \App\Mail\ClientNotifyMail($details));
                            $support_member->notify(new MessageNotification($messageData));
                    } catch (\Exception $e) {

                        $mail_error_data = json_encode([
                            'emails' => [$support_member->email],
                            'body' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
                            'error' => $e->getMessage(),
                        ]);

                        \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
                    }
                }

                //pusher notification
                $pusher_notification_data = [
                    'for_ids' => $project_assigned_support_ids,
                    'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
                    'redirect_url' => route('support.message.show.id', ['id' => Auth()->user()->id, 'name' => Auth()->user()->name]),
                ];
                emit_pusher_notification('message-channel', 'new-message', $pusher_notification_data);
            }

//            $projects = Project::select('user_id')->where('client_id', Auth::user()->id)->get();
//            foreach($projects as $project){
//                try {
//                    \Mail::to($project->added_by->email)->send(new \App\Mail\ClientNotifyMail($details));
//                    $project->added_by->notify(new MessageNotification($messageData));
//                } catch (\Exception $e) {
//
//                    $mail_error_data = json_encode([
//                        'emails' => [$project->added_by->email],
//                        'body' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has sent you a Message',
//                        'error' => $e->getMessage(),
//                    ]);
//
//                    \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
//                }
//            }
//
//            $adminusers = User::where('is_employee', 2)->get();
//            foreach($adminusers as $adminuser){
//                Notification::send($adminuser, new MessageNotification($messageData));
//            }

            //send notification to support members
//        foreach (User::where(['is_employee' => 4, 'is_support_head' => 1])->get() as $support_head_user) {
//            foreach (User::where(['is_employee' => 4])->get() as $support_head_user) {
//                Notification::send($support_head_user, new MessageNotification($messageData));
//            }

            //mail_notification
            $client = Client::find(Auth::user()->id);
            $brand = Brand::find($client->brand_id);

            $sales_head_emails = User::where('is_employee', 6)->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $brand->id)->pluck('user_id')->toArray()))->pluck('email')->toArray();

            $html = '<p>'. (Auth::user()->name.'') . ' has sent a new message' .'</p><br />';
            $html .= $request->message .'<br />';
            $html .= '<strong>Client:</strong> <span>'.Auth::user()->name.'</span><br />';

//        mail_notification('', [$user->email], 'CRM | Project assignment', $html, true);
            mail_notification(
                '',
                $sales_head_emails,
                'New Message',
                view('mail.crm-mail-template')->with([
                    'subject' => 'New Message',
                    'brand_name' => $brand->name,
                    'brand_logo' => asset($brand->logo),
                    'additional_html' => $html
                ]),
                true
            );

            return redirect()->back()->with('success', 'Message Send Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
