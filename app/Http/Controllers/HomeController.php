<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Service;
use Response;
use App\Models\User;
use App\Models\Message;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Events\MessageSent;
use Auth;
use App\Models\LogoForm;
use App\Models\Project;
use App\Models\WebForm;
use App\Models\SmmForm;
use App\Models\ContentWritingForm;
use App\Models\SeoForm;
use App\Models\SubTask;
use DB;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $clients_count = DB::table('clients')->where('user_id', Auth::user()->id)->count();
        $paid_invoice = DB::table('invoices')->where('payment_status', 2)->where('sales_agent_id', Auth::user()->id)->count();
        $un_paid_invoice = DB::table('invoices')->where('payment_status', 1)->where('sales_agent_id', Auth::user()->id)->count();
        $logo_form = LogoForm::where('logo_name', '')->where('agent_id', Auth()->user()->id)->count();
        $web_form = WebForm::where('business_name', null)->where('agent_id', Auth()->user()->id)->count();
        $smm_form = SmmForm::where('business_name', null)->where('agent_id', Auth()->user()->id)->count();
        $content_writing_form = ContentWritingForm::where('company_name', null)->where('agent_id', Auth()->user()->id)->count();
        $seo_form = SeoForm::where('company_name', null)->where('agent_id', Auth()->user()->id)->count();
        $brief_count = $logo_form + $web_form + $smm_form + $content_writing_form + $seo_form;
        return view('sale.home', compact('clients_count', 'paid_invoice', 'un_paid_invoice', 'brief_count'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function productionHome(Request $request)
    {
        $category_id = array();
        foreach(Auth()->user()->category as $category){
            array_push($category_id, $category->id);
        }

        $task = Task::when($request->category != null, function ($q) use ($request, $category_id) {
            if($request->category == 0){
                return $q->whereIn('category_id', $category_id);
            }else{
                return $q->where('category_id', $request->category);
            }
        })
        ->when($request->category == null, function ($q) use($category_id) {
            return $q->whereIn('category_id', $category_id);
        })
        ->when($request->status != null, function ($q) use($request) {
            return $q->whereIn('status', $request->status);
        })
        ->when($request->status == null, function ($q) {
            return $q->where('status', 0);
        })
        ->when($request->has('brand_id') && $request->get('brand_id') != '0', function ($q) use ($request) {
            return $q->where('brand_id', $request->get('brand_id'));
        })
        ->when($request->has('date_from') && $request->get('date_from') != '', function ($q) use ($request) {
            return $q->whereDate('created_at', '>=', Carbon::parse($request->get('date_from')));
        })
        ->when($request->has('date_to') && $request->get('date_to') != '', function ($q) use ($request) {
            return $q->whereDate('created_at', '<=', Carbon::parse($request->get('date_to')));
        })
        ->get();

        return view('production.projects.index', compact('task'));
    }

    public function managerNotification(Request $request){
        return view('manager.notification');
    }

    public function readNotification(Request $request){
        $user = Auth::user();
        $user->unreadNotifications()->get()->map(function($n) {
            $n->markAsRead();
        });
        return back();
    }

    public function markNotification(){
        $user = Auth::user();
        $user->unreadNotifications()->get()->map(function($n) {
            $n->markAsRead();
        });
        return back();
    }

    public function allNotification(){
        return view('production.notification');
    }

    public function productionDashboard()
    {
        $category_id = array();
        foreach(Auth()->user()->category as $category){
            array_push($category_id, $category->id);
        }
        $total_task = Task::whereIn('category_id', $category_id)->count();
        $open_task = Task::whereIn('category_id', $category_id)->where('status', 0)->count();
        $reopen_task = Task::whereIn('category_id', $category_id)->where('status', 1)->count();
        $hold_task = Task::whereIn('category_id', $category_id)->where('status', 2)->count();
        $completed_task = Task::whereIn('category_id', $category_id)->where('status', 3)->count();
        $in_progress_task = Task::whereIn('category_id', $category_id)->where('status', 4)->count();
        $subtasks = Subtask::orderBy('id', 'desc')->groupBy('task_id')->whereHas('task', function($q) use ($category_id){
                        $q->whereIn('category_id', $category_id);
                    })->whereHas('user', function($query) {
                        $query->where('is_employee', '!=', 5);
                        $query->where('is_employee', '!=', 1);
                    })->limit(3)->get();


        $today = date("Y-m-d");
        // dd($today);

        $today_subtasks = Subtask::orderBy('id', 'desc')->groupBy('task_id')->whereHas('task', function($q) use ($category_id){
                        $q->whereIn('category_id', $category_id);
                    })->whereHas('user', function($query) {
                        $query->where('is_employee', '!=', 5);
                        $query->where('is_employee', '!=', 1);
                    })->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") = ?', [now()->toDateString()])->get();


        $cat = Auth::user()->category_list();
        $member = User::where('is_employee', 5)->whereHas('category', function ($query) use ($cat){
            return $query->whereIn('category_id', $cat);
        })->get();

        return view('production.home', compact('total_task', 'reopen_task', 'completed_task', 'open_task', 'hold_task', 'subtasks', 'today_subtasks', 'in_progress_task', 'member'));
    }

    public function memberDashboard(){
        return view('member.home');
    }

    public function productionProfile(){
        return view('production.edit-profile');
    }

    public function editProfile(){
        return view('sale.edit-profile');
    }

    public function editProfileManager(){
        return view('manager.edit-profile');
    }

    public function updateProfileProduction(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
        ]);
        $user = User::find($id);
        if($request->has('file')){
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $file->move('uploads/users', $name);
            $path = 'uploads/users/'.$name;
            if($user->image != ''  && $user->image != null){
                $file_old = $user->image;
                unlink($file_old);
           }
           $user->image = $path;
        }
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $contact = $request->contact;
        if($contact == null){
            $contact = '#';
        }
        $user->contact = $contact;
        $user->update();
        return redirect()->back()->with('success', 'Profile Updated Successfully.');
    }

    public function updateProfileManager(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
        ]);
        $user = User::find($id);
        if($request->has('file')){
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $file->move('uploads/users', $name);
            $path = 'uploads/users/'.$name;
            if($user->image != ''  && $user->image != null){
                $file_old = $user->image;
                unlink($file_old);
           }
           $user->image = $path;
        }
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $contact = $request->contact;
        if($contact == null){
            $contact = '#';
        }
        $user->contact = $contact;
        $user->update();
        return redirect()->back()->with('success', 'Profile Updated Successfully.');
    }

    public function updateProfile(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
        ]);
        $user = User::find($id);
        if($request->has('file')){
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $file->move('uploads/users', $name);
            $path = 'uploads/users/'.$name;
            if($user->image != ''  && $user->image != null){
                $file_old = $user->image;
                unlink($file_old);
           }
           $user->image = $path;
        }
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $contact = $request->contact;
        if($contact == null){
            $contact = '#';
        }
        $user->contact = $contact;
        $user->update();
        return redirect()->back()->with('success', 'Profile Updated Successfully.');
    }

    public function changePasswordManager(){
        return view('manager.change-password');
    }

    public function changePassword(){
        return view('sale.change-password');
    }

    public function updatePasswordManager(Request $request){
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        return redirect()->back()->with('success', 'Password Change Successfully.');
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

    public function serviceList($brand_id){
        if (request()->ajax()){
            $services = Service::where('brand_id', $brand_id)->get();
            return Response::json($services);
        }
    }

    public function packageList($service_id, $brand_id){

    }

    public function saleChat($id){
        return view('sale.chat', compact('id'));
    }

    public function fetchMessages($id)
    {
        return Message::where('user_id', $id)->where('recieve_id', Auth()->user()->id)->Orwhere('sender_id', Auth()->user()->id)->with('user')->get();
    }

    public function sendMessage(Request $request, $id)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message'),
            'sender_id' => $user->id,
            'recieve_id' => $id,
        ]);
        broadcast(new MessageSent($user, $message))->toOthers();
        return ['status' => 'Message Sent!'];
    }

    public function getProjectBySale(){
        $brand_list = Auth::user()->brand_list();
        $data = Project::whereIn('brand_id', $brand_list)->where('user_id', '!=',Auth()->user()->id)->orderBy('id', 'desc');

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
        return view('sale.all-projects', compact('data'));
    }

    public function managerDashboard(){
        $clients_count = DB::table('clients')->whereIn('brand_id', Auth::user()->brand_list())->count();
        $paid_invoice = DB::table('invoices')->where('payment_status', 2)->whereIn('brand', Auth::user()->brand_list())->count();
        $un_paid_invoice = DB::table('invoices')->where('payment_status', 1)->whereIn('brand', Auth::user()->brand_list())->count();
        $logo_form = LogoForm::where('logo_name', '')->whereHas('invoice', function ($query) {
                        return $query->whereIn('brand', Auth::user()->brand_list());
                    })->count();
        $web_form = WebForm::where('business_name', null)->whereHas('invoice', function ($query) {
                        return $query->whereIn('brand', Auth::user()->brand_list());
                    })->count();
        $smm_form = SmmForm::where('business_name', null)->whereHas('invoice', function ($query) {
                        return $query->whereIn('brand', Auth::user()->brand_list());
                    })->count();
        $content_writing_form = ContentWritingForm::where('company_name', null)->whereHas('invoice', function ($query) {
                                    return $query->whereIn('brand', Auth::user()->brand_list());
                                })->count();
        $seo_form = SeoForm::where('company_name', null)->whereHas('invoice', function ($query) {
                        return $query->whereIn('brand', Auth::user()->brand_list());
                    })->count();
        $brief_count = $logo_form + $web_form + $smm_form + $content_writing_form + $seo_form;
        return view('manager.home', compact('clients_count', 'paid_invoice', 'un_paid_invoice', 'brief_count'));
    }

}
