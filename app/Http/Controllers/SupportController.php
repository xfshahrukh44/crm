<?php

namespace App\Http\Controllers;
use App\Models\NoForm;
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
use App\Notifications\AssignProjectNotification;
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
        if (!Auth::user()->is_support_head) {
            $data = $data->where('user_id', Auth()->user()->id);
        } else {
            $data = $data->whereIn('brand_id', Auth()->user()->brand_list());
        }
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

        //mail_notification
        if ($client = Client::find($request->client_id)) {
            if ($brand = Brand::find($client->brand_id)) {
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
                //            true
                );
            }
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

        //send notification to client
        if ($task and $task->projects && $task->projects->client) {
            Notification::send($task->projects->client, new MessageNotification($messageData));

            //mail_notification
            $project = Project::find($task->project_id);
            $client = Client::find($project->client->client->id);
            $brand = Brand::find($project->client->client->brand_id);

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
    //            true
            );
        }


        return redirect()->back()->with('success', 'Message Send Successfully.')->with('data', 'message');;
    }

    public function sendMessageClient(Request $request){
        try {
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
            try {
                \Mail::to($task->projects->added_by->email)->send(new \App\Mail\ClientNotifyMail($details));
            } catch (\Exception $e) {

                $mail_error_data = json_encode([
                    'emails' => [$task->projects->added_by->email],
                    'body' => 'Please Login into your Dashboard to view it..',
                    'error' => $e->getMessage(),
                ]);

                \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
            }
            return response()->json(['success' => true, 'data' => $message->message, 'name' => Auth::user()->name . ' ' . Auth::user()->last_name, 'created_at' => $message->created_at->diffForHumans()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'data' => $e->getMessage()]);
        }
    }

    public function getMessageByManager(){
        
        $messages = Message::select('messages.*', DB::raw('MAX(messages.id) as max_id'))
                    ->join('users', 'users.id', '=', 'messages.client_id')
                    ->join('clients', 'users.client_id', '=', 'clients.id')
                    ->where('messages.role_id', 3)
//                    ->whereIn('clients.brand_id', Brand::all()->pluck('id'))
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
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function editMessageByManagerClientId($id){
        $message = Message::find($id);
        return response()->json(['success' => true, 'data' => $message]);
    }

    public function editMessageByAdminClientId($id){
        try {
            $message = Message::find($id);
            return response()->json(['success' => true, 'data' => $message]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function editMessageBySupportClientId($id){
        $message = Message::find($id);
        return response()->json(['success' => true, 'data' => $message]);
    }

    public function getMessageBySupportClientId($id, $name){
        $user = User::find($id);
        $messages = Message::where('client_id', $id)->orderBy('id', 'asc')->get();
        return view('support.message.index', compact('messages', 'user'));
    }

    public function getMessageByManagerClientId($id, $name){
        $user = User::find($id);
        $messages = Message::where('client_id', $id)->orderBy('id', 'asc')->get();
        return view('manager.message.index', compact('messages', 'user'));
    }

    public function getMessageBySupport(){
//        $clients_with_messages = Client::with('user')
//            ->whereIn('brand_id', auth()->user()->brand_list())
//            ->whereHas('projects', function ($q) {
//                return $q->orderBy('id', 'desc');
//            })
//            ->whereHas('user', function ($q) {
//                return $q->when(request()->has('client_name'), function ($q) {
//                    return $q->orWhere('name', 'LIKE', "%".request()->get('client_name')."%")
//                        ->orWhere('last_name', 'LIKE', "%".request()->get('client_name')."%")
//                        ->orWhere('email', 'LIKE', "%".request()->get('client_name')."%")
//                        ->orWhere('contact', 'LIKE', "%".request()->get('client_name')."%");
//                });
//            })->get();

        $clients_with_messages = User::whereHas('client', function ($q) {
           return $q->whereIn('brand_id', auth()->user()->brand_list())
               ->whereHas('projects', function ($q) {
                   return $q->orderBy('id', 'desc');
               })
               ->when(request()->has('client_name'), function ($q) {
                   return $q->where('name', 'LIKE', "%".request()->get('client_name')."%")
                       ->orWhere('last_name', 'LIKE', "%".request()->get('client_name')."%")
                       ->orWhere('email', 'LIKE', "%".request()->get('client_name')."%")
                       ->orWhere('contact', 'LIKE', "%".request()->get('client_name')."%");
               });
        })->paginate(10);

//        $datas = Project::where('user_id', Auth()->user()->id)
////            ->when(auth()->user()->is_support_head == 1, function ($q) {
////                return $q->orWhereIn('brand_id', auth()->user()->brand_list());
////            })
//            ->when(request()->has('client_name'), function ($q) {
//                return $q->where(function ($q) {
//                    return $q->whereHas('client', function ($q) {
//                            return $q->orWhere('name', 'LIKE', "%".request()->get('client_name')."%")
//                                ->orWhere('last_name', 'LIKE', "%".request()->get('client_name')."%")
//                                ->orWhere('email', 'LIKE', "%".request()->get('client_name')."%")
//                                ->orWhere('contact', 'LIKE', "%".request()->get('client_name')."%");
//                        });
//                });
//            })
//            ->orderBy('id', 'desc')->get();
//        $message_array = [];
//        foreach($datas as $data){
//            $task_array_id = array();
//            $task_id = 0;
//            if(count($data->tasks) != 0){
//                $task_id = $data->tasks[0]->id;
//            }
//            $message = Message::where('user_id', $data->client->id)->orWhere('sender_id', $data->client->id)->orderBy('id', 'desc')->first();
//            if($message != null){
//                $message_array[$data->client->id]['f_name'] = $data->client->name;
//                $message_array[$data->client->id]['l_name'] = $data->client->last_name;
//                $message_array[$data->client->id]['email'] = $data->client->email;
//                $message_array[$data->client->id]['message'] = $message->message;
//                $message_array[$data->client->id]['image'] = $data->client->image;
//                $message_array[$data->client->id]['task_id'] = $task_id;
//                $message_array[$data->client->id]['client_id'] = $data->client->id;
//            }
//        }

        return view('support.messageshow', compact('clients_with_messages'));
    }

    public function getMessageByAdmin(Request $request){
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getMessageByAdminClientId($id, $name){
        try {
            $user = User::find($id);
            $messages = Message::where('client_id', $id)->orderBy('id', 'desc')->get();
            return view('admin.message-index', compact('messages', 'user'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getPendingProjectManager(Request $request){
//        $logo_form = LogoForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $web_form = WebForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $smm_form = SmmForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $content_writing_form = ContentWritingForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $seo_form = SeoForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $book_formatting_form = BookFormatting::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//        $book_writing_form = BookWriting::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//
//        $author_website_form = AuthorWebsite::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//
//        $proofreading_form = Proofreading::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//
//        $bookcover_form = BookCover::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//
//
//        $isbn_form = Isbnform::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//
//        $bookprinting_form = Bookprinting::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();
//
//        $no_form = NoForm::with('project')->doesntHave('project')->whereHas('invoice', function ($query) {
//            return $query->whereIn('brand', Auth::user()->brand_list());
//        })->orderBy('id', 'desc')
//            ->when($request->has('user_id'), function ($q) use ($request) {
//                return $q->where('user_id', $request->get('user_id'));
//            })->get();

        //change
        $client_users_with_pending_projects = User::whereIn('id', get_project_client_user_ids())->when($request->has('user_id'), function ($q) use ($request) {
            return $q->where('id', $request->get('user_id'));
        })->get();

//        return view('support.brief.fill', compact('client_users_with_pending_projects', 'logo_form', 'web_form', 'smm_form', 'content_writing_form', 'seo_form', 'book_formatting_form', 'book_writing_form', 'author_website_form', 'no_form', 'proofreading_form', 'bookcover_form', 'isbn_form', 'bookprinting_form'));
        return view('support.brief.fill', compact('client_users_with_pending_projects'));
    }

    public function  getPendingProjectbyIdManager($id, $form){
        if($form == 1){
            $logo_form = LogoForm::find($id);
            return view('support.brief.logoform', compact('logo_form'));
        }elseif($form == 2){
            $web_form = WebForm::find($id);
            return view('support.brief.webform', compact('web_form'));
        }elseif($form == 3){
            $smm_form = SmmForm::find($id);
            return view('support.brief.smmform', compact('smm_form'));
        }elseif($form == 4){
            $content_form = ContentWritingForm::find($id);
            return view('support.brief.contentform', compact('content_form'));
        }elseif($form == 5){
            $seo_form = SeoForm::find($id);
            return view('support.brief.seoform', compact('seo_form'));
        }elseif($form == 6){
            $book_formatting_form = BookFormatting::find($id);
            return view('support.brief.bookformattingform', compact('book_formatting_form'));
        }elseif($form == 7){
            $data = BookWriting::find($id);
            return view('support.brief.bookwritingform', compact('data'));
        }elseif($form == 8){
            $data = AuthorWebsite::find($id);
            return view('support.brief.authorwebsiteform', compact('data'));
        }elseif($form == 9){
            $data = Proofreading::find($id);
            return view('support.brief.proofreadingform', compact('data'));
        }elseif($form == 10){
            $data = BookCover::find($id);
            return view('support.brief.bookcoverform', compact('data'));
        }elseif($form == 11){
            $data = Isbnform::find($id);
            return view('support.brief.isbnform', compact('data'));
        }elseif($form == 12){
            $data = Bookprinting::find($id);
            return view('support.brief.bookprintingform', compact('data'));
        }


    }

    public function assignSupportManager(Request $request){

        // dd($request->all());

        $form_id  = $request->id;
        $agent_id  = $request->agent_id;
        $form_checker  = $request->form;
        $name = '';
        $client_id = 0;
        $brand_id = 0;
        $description = '';
        if($form_checker == 0){
            $no_form = NoForm::find($form_id);
            if($no_form->name != null){
                $name = $no_form->name . ' - OTHER';
            }else{
                $name = $no_form->name . ' - OTHER';
            }
            $client_id = $no_form->user->id;
            $brand_id = $no_form->invoice->brand;
            $description = $no_form->business;

        }elseif($form_checker == 1){
            // Logo form
            $logo_form = LogoForm::find($form_id);
            if($logo_form->logo_name != null){
                $name = $logo_form->logo_name . ' - LOGO';
            }else{
                $name = $logo_form->user->name . ' - LOGO';
            }
            $client_id = $logo_form->user->id;
            $brand_id = $logo_form->invoice->brand;
            $description = $logo_form->business;
        }elseif($form_checker == 2){
            // Web form
            $web_form = WebForm::find($form_id);
            if($web_form->business_name != null){
                $name = $web_form->business_name . ' - WEBSITE';
            }else{
                $name = $web_form->user->name . ' - WEBSITE';
            }
            $client_id = $web_form->user->id;
            $brand_id = $web_form->invoice->brand;
            $description = $web_form->about_companys;
        }elseif($form_checker == 3){
            // Social Media Marketing Form
            $smm_form = SmmForm::find($form_id);
            if($smm_form->business_name != null){
                $name = $smm_form->business_name . ' - SMM';
            }else{
                $name = $smm_form->user->name . ' - SMM';
            }
            $client_id = $smm_form->user->id;
            $brand_id = $smm_form->invoice->brand;
            $description = $smm_form->business_category;
        }elseif($form_checker == 4){
            // Content Writing Form
            $content_form = ContentWritingForm::find($form_id);
            if($content_form->company_name != null){
                $name = $content_form->company_name . ' - CONTENT WRITING';
            }else{
                $name = $content_form->user->name . ' - CONTENT WRITING';
            }
            $client_id = $content_form->user->id;
            $brand_id = $content_form->invoice->brand;
            $description = $content_form->company_details;
        }elseif($form_checker == 5){
            // Search Engine Optimization Form
            $seo_form = SeoForm::find($form_id);
            if($seo_form->company_name != null){
                $name = $seo_form->company_name . ' - SEO';
            }else{
                $name = $seo_form->user->name . ' - SEO';
            }
            $client_id = $seo_form->user->id;
            $brand_id = $seo_form->invoice->brand;
            $description = $seo_form->top_goals;
        }elseif($form_checker == 6){
            // Book Formatting & Publishing Form
            $book_formatting_form = BookFormatting::find($form_id);
            if($book_formatting_form->book_title != null){
                $name = $book_formatting_form->book_title . ' - Book Formatting & Publishing';
            }else{
                $name = $book_formatting_form->user->name . ' - Book Formatting & Publishing';
            }
            $client_id = $book_formatting_form->user->id;
            $brand_id = $book_formatting_form->invoice->brand;
            $description = $book_formatting_form->additional_instructions;
        }elseif($form_checker == 7){
            // Book Writing Form
            $book_writing_form = BookWriting::find($form_id);
            if($book_writing_form->book_title != null){
                $name = $book_writing_form->book_title . ' - Book Writing';
            }else{
                $name = $book_writing_form->user->name . ' - Book Writing';
            }
            $client_id = $book_writing_form->user->id;
            $brand_id = $book_writing_form->invoice->brand;
            $description = $book_writing_form->brief_summary;
        }elseif($form_checker == 8){
            // Author Website Form
            $author_website_form = AuthorWebsite::find($form_id);
            if($author_website_form->author_name != null){
                $name = $author_website_form->author_name . ' - Author Website';
            }else{
                $name = $author_website_form->user->name . ' - Author Website';
            }
            $client_id = $author_website_form->user->id;
            $brand_id = $author_website_form->invoice->brand;
            $description = $author_website_form->brief_overview;
        }elseif($form_checker == 9){
            // Editing & Proofreading Form
            $proofreading_form = Proofreading::find($form_id);
            if($proofreading_form->author_name != null){
                $name = $proofreading_form->description . ' - Editing & Proofreading';
            }else{
                $name = $proofreading_form->user->name . ' - Editing & Proofreading';
            }
            $client_id = $proofreading_form->user->id;
            $brand_id = $proofreading_form->invoice->brand;
            $description = $proofreading_form->guide;
        }elseif($form_checker == 10){
            // Cover Design Form
            $bookcover_form = BookCover::find($form_id);
            if($bookcover_form->author_name != null){
                $name = $bookcover_form->title . ' - Cover Design';
            }else{
                $name = $bookcover_form->user->name . ' - Cover Design';
            }
            $client_id = $bookcover_form->user->id;
            $brand_id = $bookcover_form->invoice->brand;
            $description = $bookcover_form->information;
        }
        elseif($form_checker == 11){
            // Cover Design Form
            $isbn_form = Isbnform::find($form_id);
            if($isbn_form->author_name != null){
                $name = $isbn_form->title . ' - ISBN Form';
            }else{
                $name = $isbn_form->user->name . ' - ISBN Form';
            }
            $client_id = $isbn_form->user->id;
            $brand_id = $isbn_form->invoice->brand;
            $description = $isbn_form->information;
        }
        elseif($form_checker == 12){
            // Cover Design Form
            $bookprinting_form = Bookprinting::find($form_id);
            if($bookprinting_form->author_name != null){
                $name = $bookprinting_form->title . ' - Book Printing Form';
            }else{
                $name = $bookprinting_form->user->name . ' - Book Printing Form';
            }
            $client_id = $bookprinting_form->user->id;
            $brand_id = $bookprinting_form->invoice->brand;
            $description = $bookprinting_form->information;
        }

        $project = new Project();
        $project->name = $name;
        $project->description = $description;
        $project->status = 1;
        $project->user_id = $agent_id;
        $project->client_id = $client_id;
        $project->brand_id = $brand_id;
        $project->form_id = $form_id;
        $project->form_checker = $form_checker;
        $project->save();
        $assignData = [
            'id' => Auth()->user()->id,
            'project_id' => $project->id,
            'name' => Auth()->user()->name . ' ' . Auth()->user()->last_name,
            'text' => $project->name . ' has assign. ('.Auth()->user()->name.')',
            'url' => '',
        ];
        $user = User::find($agent_id);
        $user->notify(new AssignProjectNotification($assignData));

//        dd('here');
        //mail_notification
        $html = '<p>'.'New project `'.$project->name.'`'.'</p><br />';
        $html .= '<strong>Assigned by:</strong> <span>'.Auth::user()->name.'</span><br />';
        $html .= '<strong>Assigned to:</strong> <span>'.$user->name.' ('.$user->email.')'.'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';
//        mail_notification('', [$user->email], 'CRM | New project', $html, true);
        mail_notification(
            '',
            [$user->email],
            'New project',
            view('mail.crm-mail-template')->with([
                'subject' => 'New project',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return redirect()->back()->with('success', $user->name . ' ' . $user->last_name . ' Assigned Successfully');
    }

    public function reassignSupportManager(Request $request){
        $project = Project::find($request->id);
        $project->user_id = $request->agent_id;
        $project->save();

        //mail_notification
        $user = User::find($request->agent_id);
        $html = '<p>'.'Project `'.$project->name.'` has been reassigned.'.'</p><br />';
        $html .= '<strong>Reassigned by:</strong> <span>'.Auth::user()->name.'</span><br />';
        $html .= '<strong>Reassigned to:</strong> <span>'.$user->name.' ('.$user->email.') '.'</span><br />';
        $html .= '<strong>Client:</strong> <span>'.$project->client->name.'</span><br />';

//        mail_notification('', [$user->email], 'CRM | Project assignment', $html, true);
        mail_notification(
            '',
            [$user->email],
            'Project assignment',
            view('mail.crm-mail-template')->with([
                'subject' => 'Project assignment',
                'brand_name' => $project->brand->name,
                'brand_logo' => asset($project->brand->logo),
                'additional_html' => $html
            ]),
            true
        );

        return redirect()->back()->with('success', $project->name . ' Reassigned Successfully');
    }

    public function getAgentManager($brand_id = null){
        $user = User::select('id', 'name', 'last_name')
            ->where('id', '!=', auth()->id())
            ->where('is_support_head',  0)
            ->where('is_employee', 4)
            ->whereHas('brands', function ($query) use ($brand_id) {
                return $query->where('brand_id', $brand_id);
            })->get()->toArray();

        $user []= auth()->user();

        return response()->json(['success' => true , 'data' => $user]);
    }

    public function getFormByQA($form_id, $check, $id){


        $project = Project::find($id);
        // if($project->user_id == Auth()->user()->id){
        if($check == 1){
            $logo_form = LogoForm::find($form_id);
            return view('qa.form.logoform', compact('logo_form'));
        }else if($check == 2){
            $web_form = WebForm::find($form_id);
            return view('qa.form.webform', compact('web_form'));
        }elseif($check == 3){
            $smm_form = SmmForm::find($form_id);
            return view('qa.form.smmform', compact('smm_form'));
        }elseif($check == 4){
            $content_form = ContentWritingForm::find($form_id);
            return view('qa.form.contentform', compact('content_form'));
        }elseif($check == 5){
            $seo_form = SeoForm::find($form_id);
            return view('qa.form.seoform', compact('seo_form'));
        }elseif($check == 6){
            $data = BookFormatting::find($form_id);
            return view('qa.form.bookformatting', compact('data'));
        }elseif($check == 7){
            $data = BookWriting::find($form_id);
            return view('qa.form.bookwriting', compact('data'));
        }elseif($check == 8){
            $data = AuthorWebsite::find($form_id);
            return view('qa.form.authorwebsite', compact('data'));
        }elseif($check == 9){
            $data = Proofreading::find($form_id);
            return view('qa.form.proofreading', compact('data'));
        }elseif($check == 10){
            $data = BookCover::find($form_id);
            return view('qa.form.bookcover', compact('data'));
        }elseif($check == 11){
            $data = Isbnform::find($form_id);
            return view('qa.form.isbnform', compact('data'));
        }elseif($check == 12){
            $data = Bookprinting::find($form_id);
            return view('qa.form.bookprinting', compact('data'));
        }


    }

}
