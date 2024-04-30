<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\LogoForm;
use App\Models\WebForm;
use App\Models\SmmForm;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\SeoForm;
use App\Models\BookFormatting;
use App\Models\BookWriting;
use App\Models\AuthorWebsite;
use App\Models\Proofreading;
use App\Models\BookCover;
use App\Models\Isbnform;
use App\Models\Bookprinting;
use App\Models\Brand;
use App\Models\ClientFile;
use App\Models\Task;
use App\Models\SubTask;
use App\Models\SubtasKDueDate;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Notifications\MessageNotification;
use Illuminate\Support\Str;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Auth;
use Notification;
use Mail;
use DB;
use PDF;
use \Carbon\Carbon;
use DateTimeZone;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $project_count = Project::where('user_id', Auth()->user()->id)->orderBy('id', 'desc')->count();
        return view('support.home', compact('project_count'));
    }

    public function projects(Request $request){
        $data = new Project;
        $data = $data->where('user_id', Auth()->user()->id);
        $data = $data->orderBy('id', 'desc');
        if($request->project != ''){
            $data = $data->where('name', 'LIKE', "%$request->project%");
        }
        if($request->project_id != ''){
            $data = $data->where('id', $request->project_id);
        }
        if($request->user != ''){
            $user = $request->user;
            $data = $data->whereHas('client', function ($query) use ($user) {
                return $query->where('name', 'LIKE', "%$user%")->orWhere('email', 'LIKE', "%$user%");
            });
        }
       
        $data = $data->paginate(10);
        return view('support.project', compact('data'));
    }

    public function allProjects(){
        $brand_list = Auth::user()->brand_list();
        $data = Project::whereIn('brand_id', $brand_list)->where('user_id', '!=',Auth()->user()->id)->orderBy('id', 'desc')->paginate(10);
        return view('support.all-projects', compact('data'));
    }

    public function getPdfFormByProduction($form_id, $check, $id){
        $project = Project::find($id);
        if($check == 1){
            $logo_form = LogoForm::find($form_id);
            // return view('production.form.logoform', compact('logo_form'));
        }else if($check == 2){
            $web_form = WebForm::find($form_id);
            $data = [
                'data' => $web_form,
            ];
            $pdf = PDF::loadView('production.pdf.web-form', $data);
            return $pdf->download('testing.pdf');
            // return view('production.form.webform', compact('web_form'));
        }elseif($check == 3){
            $smm_form = SmmForm::find($form_id);
            // return view('production.form.smmform', compact('smm_form'));
        }elseif($check == 4){
            $content_form = ContentWritingForm::find($form_id);
            // return view('production.form.contentform', compact('content_form'));
        }elseif($check == 5){
            $seo_form = SeoForm::find($form_id);
            // return view('production.form.seoform', compact('seo_form'));
        }
    }

    public function getFormByProduction($form_id, $check, $id){
        
        
        $project = Project::find($id);
        // if($project->user_id == Auth()->user()->id){
        if($check == 1){
            $logo_form = LogoForm::find($form_id);
            return view('production.form.logoform', compact('logo_form'));
        }else if($check == 2){
            $web_form = WebForm::find($form_id);
            return view('production.form.webform', compact('web_form'));
        }elseif($check == 3){
            $smm_form = SmmForm::find($form_id);
            return view('production.form.smmform', compact('smm_form'));
        }elseif($check == 4){
            $content_form = ContentWritingForm::find($form_id);
            return view('production.form.contentform', compact('content_form'));
        }elseif($check == 5){
            $seo_form = SeoForm::find($form_id);
            return view('production.form.seoform', compact('seo_form'));
        }elseif($check == 6){
            $data = BookFormatting::find($form_id);
            return view('production.form.bookformatting', compact('data'));
        }elseif($check == 7){
            $data = BookWriting::find($form_id);
            return view('production.form.bookwriting', compact('data'));
        }elseif($check == 8){
            $data = AuthorWebsite::find($form_id);
            return view('production.form.authorwebsite', compact('data'));
        }elseif($check == 9){
            $data = Proofreading::find($form_id);
            return view('production.form.proofreading', compact('data'));
        }elseif($check == 10){
            $data = BookCover::find($form_id);
            return view('production.form.bookcover', compact('data'));
        }elseif($check == 11){
            $data = Isbnform::find($form_id);
            return view('production.form.isbnform', compact('data'));
        }elseif($check == 12){
            $data = Bookprinting::find($form_id);
            return view('production.form.bookprinting', compact('data'));
        }
         
        
    }

    public function getFormByMember($form_id, $check, $id){
        $project = Project::find($id);
        // if($project->user_id == Auth()->user()->id){
        if($check == 1){
            $logo_form = LogoForm::find($form_id);
            return view('member.form.logoform', compact('logo_form'));
        }else if($check == 2){
            $web_form = WebForm::find($form_id);
            return view('member.form.webform', compact('web_form'));
        }elseif($check == 3){
            $smm_form = SmmForm::find($form_id);
            return view('member.form.smmform', compact('smm_form'));
        }elseif($check == 4){
            $content_form = ContentWritingForm::find($form_id);
            return view('member.form.contentform', compact('content_form'));
        }elseif($check == 5){
            $seo_form = SeoForm::find($form_id);
            return view('member.form.seoform', compact('seo_form'));
        }elseif($check == 6){
            $data = BookFormatting::find($form_id);
            return view('member.form.bookformatting', compact('data'));
        }elseif($check == 7){
            $data = BookWriting::find($form_id);
            return view('member.form.bookwriting', compact('data'));
        }elseif($check == 8){
            $data = AuthorWebsite::find($form_id);
            return view('member.form.authorwebsite', compact('data'));
        }elseif($check == 9){
            $data = Proofreading::find($form_id);
            return view('member.form.proofreading', compact('data'));
        }elseif($check == 10){
            $data = BookCover::find($form_id);
            return view('member.form.bookcover', compact('data'));
        }
    }

    public function getForm($form_id, $check, $id){
        
        $project = Project::find($id);
        // if($project->user_id == Auth()->user()->id){
            if($check == 1){
                $logo_form = LogoForm::find($form_id);
                return view('support.logoform', compact('logo_form'));
            }else if($check == 2){
                $web_form = WebForm::find($form_id);
                return view('support.webform', compact('web_form'));
            }elseif($check == 3){
                $smm_form = SmmForm::find($form_id);
                return view('support.smmform', compact('smm_form'));
            }elseif($check == 4){
                $content_form = ContentWritingForm::find($form_id);
                return view('support.contentform', compact('content_form'));
            }elseif($check == 5){
                $seo_form = SeoForm::find($form_id);
                return view('support.seoform', compact('seo_form'));
            }elseif($check == 6){
                $data = BookFormatting::find($form_id);
                return view('support.bookformatting', compact('data'));
            }elseif($check == 7){
                $data = BookWriting::find($form_id);
                return view('support.bookwriting', compact('data'));
            }elseif($check == 8){
                $data = AuthorWebsite::find($form_id);
                return view('support.authorwesbite', compact('data'));
            }elseif($check == 9){
                $data = Proofreading::find($form_id);
                return view('support.proofreading', compact('data'));
            }elseif($check == 10){
                $data = BookCover::find($form_id);
                return view('support.bookcover', compact('data'));
            }
            elseif($check == 11){
                $data = Isbnform::find($form_id);
                return view('support.isbnform', compact('data'));
            }
            elseif($check == 12){
                $data = Bookprinting::find($form_id);
                return view('support.bookprinting', compact('data'));
            }
            
            
        // }else{
        //     return redirect()->back();
        // }
    }

    public function getFormManager($form_id, $check, $id){
        $project = Project::find($id);
        // if($project->user_id == Auth()->user()->id){
            if($check == 1){
                $logo_form = LogoForm::find($form_id);
                return view('manager.form.logoform', compact('logo_form'));
            }else if($check == 2){
                $web_form = WebForm::find($form_id);
                return view('manager.form.webform', compact('web_form'));
            }elseif($check == 3){
                $smm_form = SmmForm::find($form_id);
                return view('manager.form.smmform', compact('smm_form'));
            }elseif($check == 4){
                $content_form = ContentWritingForm::find($form_id);
                return view('manager.form.contentform', compact('content_form'));
            }elseif($check == 5){
                $seo_form = SeoForm::find($form_id);
                return view('manager.form.seoform', compact('seo_form'));
            }elseif($check == 6){
                $data = BookFormatting::find($form_id);
                return view('manager.form.bookformattingform', compact('data'));
            }elseif($check == 7){
                $data = BookWriting::find($form_id);
                return view('manager.form.bookwritingform', compact('data'));
            }elseif($check == 8){
                $data = AuthorWebsite::find($form_id);
                return view('manager.form.authorwebsiteform', compact('data'));
            }elseif($check == 9){
                $data = Proofreading::find($form_id);
                return view('manager.form.proofreadingform', compact('data'));
            }elseif($check == 10){
                $data = BookCover::find($form_id);
                return view('manager.form.bookcoverform', compact('data'));
            }
        // }else{
        //     return redirect()->back();
        // }
    }

    public function getFormSale($form_id, $check, $id){
        $project = Project::find($id);
        // if($project->user_id == Auth()->user()->id){
            if($check == 1){
                $logo_form = LogoForm::find($form_id);
                return view('sale.form.logoform', compact('logo_form'));
            }else if($check == 2){
                $web_form = WebForm::find($form_id);
                return view('sale.form.webform', compact('web_form'));
            }elseif($check == 3){
                $smm_form = SmmForm::find($form_id);
                return view('sale.form.smmform', compact('smm_form'));
            }elseif($check == 4){
                $content_form = ContentWritingForm::find($form_id);
                return view('sale.form.contentform', compact('content_form'));
            }elseif($check == 5){
                $seo_form = SeoForm::find($form_id);
                return view('sale.form.seoform', compact('seo_form'));
            }
        // }else{
        //     return redirect()->back();
        // }
    }

    public function changePassword(){
        return view('support.change-password');
    }

    public function updatePassword(Request $request){
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        return redirect()->back()->with('success', 'Password Change Successfully.');
    }

    public function message(){
        $data = Project::where('user_id', Auth()->user()->id)->orderBy('id', 'desc')->get();
        $project = null;
        return view('support.message', compact('data', 'project'));
    }

    public function showMessage($id){
        $project = Project::find($id);
        $data = Project::where('user_id', Auth()->user()->id)->orderBy('id', 'desc')->get();
        if(Auth()->user()->id == $project->user_id){
            return view('support.message', compact('data', 'project'));
        }else{
            return redirect()->back();
        }
    }

    public function managerSendMessage(Request $request){
        $this->validate($request, [
            'message' => 'required'
        ]);
        $carbon = Carbon::now(new DateTimeZone('America/Los_Angeles'))->toDateTimeString();
        $task = Task::find($request->task_id);
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
        $message->task_id = $request->task_id;
        $message->role_id = 6;
        $message->created_at = $carbon;
        $message->save();
        if($request->hasfile('images')){
            $i = 0;
            foreach($request->file('images') as $file)
            {
                $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $name = $file_name . '_' . $i . '_' .time().'.'.$file->extension();
                $file->move(public_path().'/files/', $name);
                $i++;
                $client_file = new ClientFile();
                $client_file->name = $file_name;
                $client_file->path = $name;
                $client_file->task_id = $request->task_id;
                $client_file->user_id = Auth()->user()->id;
                $client_file->user_check = Auth()->user()->is_employee;
                $client_file->production_check = 2;
                $client_file->message_id = $message->id;
                $client_file->created_at = $carbon;
                $client_file->save();
            }
        }
        $details = [
            'title' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has message on your task.',
            'body' => 'Please Login into your Dashboard to view it..'
        ];
        if($task != null){
            \Mail::to($task->projects->client->email)->send(new \App\Mail\ClientNotifyMail($details));
        }else{
            $client = User::find($request->client_id);
            \Mail::to($client->email)->send(new \App\Mail\ClientNotifyMail($details));
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
            'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has send you a Message',
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
        return redirect()->back()->with('success', 'Message Send Successfully.')->with('data', 'message');;
    }

    public function sendMessage(Request $request){
        $this->validate($request, [
            'message' => 'required'
        ]);
        $carbon = Carbon::now(new DateTimeZone('America/Los_Angeles'))->toDateTimeString();
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
        $message->created_at = $carbon;
        $message->save();

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
            }
        }

        $details = [
            'title' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has message on your task.',
            'body' => 'Please Login into your Dashboard to view it..'
        ];
        if($task != null){
            \Mail::to($task->projects->client->email)->send(new \App\Mail\ClientNotifyMail($details));
        }else{
            $client = User::find($request->client_id);
            \Mail::to($client->email)->send(new \App\Mail\ClientNotifyMail($details));
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
            'text' => Auth()->user()->name . ' ' . Auth()->user()->last_name . ' has send you a Message',
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
        return redirect()->back()->with('success', 'Message Send Successfully.')->with('data', 'message');;
    }

    public function sendMessageClient(Request $request){
        $this->validate($request, [
            'message' => 'required',
            'task_id' => 'required',
        ]);
        $task = Task::find($request->task_id);
        $message = new Message();
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        $message->sender_id = 1;
        $message->task_id = $request->task_id;
        $message->client_id = Auth::user()->id;
        $message->save();
        $details = [
            'title' => $task->projects->client->name . ' ' . $task->projects->client->last_name . ' has message on your task.',
            'body' => 'Please Login into your Dashboard to view it..'
        ];
        \Mail::to($task->projects->added_by->email)->send(new \App\Mail\ClientNotifyMail($details));
        return response()->json(['success' => true, 'data' => $message->message, 'name' => Auth::user()->name . ' ' . Auth::user()->last_name, 'created_at' => $message->created_at->diffForHumans()]);
    }

    public function getMessageByManager(){
        
        $messages = Message::select('messages.*', DB::raw('MAX(messages.id) as max_id'))
                    ->join('users', 'users.id', '=', 'messages.client_id')
                    ->join('clients', 'users.client_id', '=', 'clients.id')
                    ->where('messages.role_id', 3)
                    ->whereIn('clients.brand_id', Auth()->user()->brand_list())
                    ->groupBy('messages.client_id')
                    ->orderBy('max_id', 'desc')
                    ->paginate(20);
        return view('manager.messageshow', compact('messages'));
    }

    public function updateSupportMessage(Request $request){
        $request->validate([
            'message_id' => 'required',
            'editmessage' => 'required',
        ]);
        $message = Message::find($request->message_id);
        if($message != null){
            $message->message = $request->editmessage;
            $message->save();
            return redirect()->back()->with('success', 'Message Updated Successfully.');
        }
        return redirect()->back()->with('success', 'Error Occured');
    }

    public function updateManagerMessage(Request $request){
        $request->validate([
            'message_id' => 'required',
            'editmessage' => 'required',
        ]);
        $message = Message::find($request->message_id);
        if($message != null){
            $message->message = $request->editmessage;
            $message->save();
            return redirect()->back()->with('success', 'Message Updated Successfully.');
        }
        return redirect()->back()->with('success', 'Error Occured');
    }

    public function updateAdminMessage(Request $request){
        $request->validate([
            'message_id' => 'required',
            'editmessage' => 'required',
        ]);
        $message = Message::find($request->message_id);
        if($message != null){
            $message->message = $request->editmessage;
            $message->save();
            return redirect()->back()->with('success', 'Message Updated Successfully.');
        }
        return redirect()->back()->with('success', 'Error Occured');
    }

    public function editMessageByManagerClientId($id){
        $message = Message::find($id);
        return response()->json(['success' => true, 'data' => $message]);
    }

    public function editMessageByAdminClientId($id){
        $message = Message::find($id);
        return response()->json(['success' => true, 'data' => $message]);
    }

    public function editMessageBySupportClientId($id){
        $message = Message::find($id);
        return response()->json(['success' => true, 'data' => $message]);
    }

    public function getMessageBySupportClientId($id, $name){
        $user = User::find($id);
        $messages = Message::where('client_id', $id)->orderBy('id', 'desc')->get();
        return view('support.message.index', compact('messages', 'user'));
    }

    public function getMessageByManagerClientId($id, $name){
        $user = User::find($id);
        $messages = Message::where('client_id', $id)->orderBy('id', 'desc')->get();
        return view('manager.message.index', compact('messages', 'user'));
    }

    public function getMessageBySupport(){
        $datas = Project::where('user_id', Auth()->user()->id)->orderBy('id', 'desc')->get();
        $message_array = [];
        foreach($datas as $data){
            $task_array_id = array();
            $task_id = 0;
            if(count($data->tasks) != 0){
                $task_id = $data->tasks[0]->id;
            }
            $message = Message::where('user_id', $data->client->id)->orWhere('sender_id', $data->client->id)->orderBy('id', 'desc')->first();
            if($message != null){
                $message_array[$data->client->id]['f_name'] = $data->client->name;
                $message_array[$data->client->id]['l_name'] = $data->client->last_name;
                $message_array[$data->client->id]['email'] = $data->client->email;
                $message_array[$data->client->id]['message'] = $message->message;
                $message_array[$data->client->id]['image'] = $data->client->image;
                $message_array[$data->client->id]['task_id'] = $task_id;
            }
        }
        
        return view('support.messageshow', compact('message_array'));
    }

    public function getMessageByAdmin(Request $request){
        // $filter = 0;
        // $message_array = [];
        // $datas = Project::orderBy('id', 'desc')->get();
        // if($request->message != ''){
        //     $task_id = 0;
        //     $messages = Message::where('message', 'like', '%' . $request->message . '%')->orderBy('id', 'desc')->get();
        //     foreach($messages as $message){
        //         if($message->user_name != null){
        //             $message_array[$message->user_name->id]['f_name'] = $message->user_name->name;
        //             $message_array[$message->user_name->id]['l_name'] = $message->user_name->last_name;
        //             $message_array[$message->user_name->id]['email'] = $message->user_name->email;
        //             $message_array[$message->user_name->id]['message'] = $message->message;
        //             $message_array[$message->user_name->id]['image'] = $message->user_name->image;
        //             $projects = Project::where('client_id', $message->user_name->id)->get();
        //             foreach($projects as $project){
        //                 foreach($project->tasks as $key => $tasks){
        //                     $message_array[$message->user_name->id]['task_id'][$key] = $tasks->id;
        //                 }
        //             }
        //         }
        //     }

        // }else{
        //     $filter = 1;
        //     foreach($datas as $data){
        //         $task_array_id = array();
        //         $task_id = 0;
        //         if(count($data->tasks) != 0){
        //             $task_id = $data->tasks[0]->id;
        //         }
        //         $message = Message::where('user_id', $data->client->id)->orWhere('sender_id', $data->client->id)->orderBy('id', 'desc')->first();
        //         if($message != null){
        //             $message_array[$data->client->id]['f_name'] = $data->client->name;
        //             $message_array[$data->client->id]['l_name'] = $data->client->last_name;
        //             $message_array[$data->client->id]['email'] = $data->client->email;
        //             $message_array[$data->client->id]['message'] = $message->message;
        //             $message_array[$data->client->id]['image'] = $data->client->image;
        //             $projects = Project::where('client_id', $data->client->id)->get();
        //             foreach($projects as $project){
        //                 foreach($project->tasks as $key => $tasks){
        //                     $message_array[$data->client->id]['task_id'][$key] = $tasks->id;
        //                 }
        //             }
        //         }
        //     }
        // }
        $filter = 0;
        $brand_array = [];
        $brands = DB::table('brands')->select('id', 'name')->get();
        foreach($brands as $key => $brand){
            array_push($brand_array, $brand->id);
        }
        $message_array = [];
        $data = User::where('is_employee', 3)->where('client_id', '!=', 0);
        if($request->brand != null){
            $get_brand = $request->brand;
            $data = $data->whereHas('client', function ($query) use ($get_brand) {
                return $query->where('brand_id', $get_brand);
            });
        }else{
            $data = $data->whereHas('client', function ($query) use ($brand_array) {
                return $query->whereIn('brand_id', $brand_array);
            });
        }
        if($request->message != null){
            $message = $request->message;
            $data = $data->whereHas('messages', function ($query) use ($message) {
                return $query->where('message', 'like', '%' . $message . '%');
            });
        }
        $data = $data->orderBy('id', 'desc')->paginate(20);
        return view('admin.messageshow', compact('message_array', 'brands', 'filter', 'data'));
    }
    

}
