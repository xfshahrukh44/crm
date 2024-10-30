<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\TaskStatusChangedLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QAController extends Controller
{
    public function getUserQA(){
        $user_ids = DB::table('category_users')->whereIn('category_id', auth()->user()->category_list())->pluck('user_id');

        $user = User::where([
            'is_employee' => 7,
            'is_support_head' => false,
        ])->whereIn('id', $user_ids)->get();

        return view('qa.qa.index', compact('user'));
    }

    public function createUserQA(){
        $category = Category::where('status', 1)->whereIn('id', auth()->user()->category_list())->get();
        return view('qa.qa.create', compact('category'));
    }

    public function storeUserQA(Request $request){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'status' => 'required',
            'password' => 'required',
//            'is_employee' => 'required',
            'category' => 'required',
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->password = Hash::make($request->input('password'));
        $user->is_employee = 7;
        $user->is_support_head = false;
        $user->save();
//        $brand = $request->input('brand');
//        $user->brands()->sync($brand);
        $category = $request->input('category');
        $user->category()->sync($category);

        return redirect()->route('qa.user.qa.create')->with('success','QA Person Created Successfully.');
    }

    public function editUserQA($id){
        $data = User::find($id);
//        $brand = Brand::where('status', 1)->get();
        $category = Category::where('status', 1)->get();
        return view('qa.qa.edit', compact('data', 'category'));
    }

    public function updateUserQA(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'status' => 'required',
//            'is_employee' => 'required',
            'category' => 'required',
        ]);


        $user = User::find($id);
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if($request->input('password') != "")
        {
            $user->password = Hash::make($request->input('password'));
        }

        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->is_support_head = $request->input('is_support_head');
        $user->save();
//        $brand = $request->input('brand');
//        $user->brands()->sync($brand);
        $category = $request->input('category');
        $user->category()->sync($category);

        return redirect()->route('qa.user.qa.edit', $id)->with('success','QA Person Updated Successfully.');
    }

    public function assignTaskToMember (Request $request)
    {
        $task = Task::find($request->task_id);
        $task->qa_id = $request->user_id;
        $task->save();

        return redirect()->back()->with('success', 'Task assigned successfully!');
    }

    public function qaDashboard()
    {
        $category_id_array = get_auth_category_ids();

//        $restricted_ids = get_restricted_brand_ids_for_qa();

        $sent_for_qa_count = Task::where('status', 7)
//                                ->whereIn('brand_id', $restricted_ids)
                                ->whereIn('category_id', $category_id_array)
                                ->whereHas('status_logs', function ($q) {
                                    return $q->where([
                                        'column' => 'status',
                                        'new' => '7',
                                    ])->whereDate('created_at', '>=', Carbon::parse('6 August 2024'));
                                })->count();

        $sent_for_approval_count = Task::where('status', 5)
//                                ->whereIn('brand_id', $restricted_ids)
                                ->whereIn('category_id', $category_id_array)
                                ->whereHas('status_logs', function ($q) {
                                    return $q->where([
                                        'column' => 'status',
                                        'new' => '5',
                                    ])->whereDate('created_at', '>=', Carbon::parse('6 August 2024'));
                                })->count();

        $completed_count = Task::where('status', 3)
//                                ->whereIn('brand_id', $restricted_ids)
                                ->whereIn('category_id', $category_id_array)
                                ->whereHas('status_logs', function ($q) {
                                    return $q->where([
                                        'column' => 'status',
                                        'new' => '3',
                                    ])->whereDate('created_at', '>=', Carbon::parse('6 August 2024'));
                                })->count();

        $total_tasks_count = $sent_for_qa_count + $sent_for_approval_count + $completed_count;


        $recent_tasks = Task::where('status', 7)
//                            ->whereIn('brand_id', $restricted_ids)
                            ->whereIn('category_id', $category_id_array)
                            ->whereHas('status_logs', function ($q) {
                                return $q->where([
                                    'column' => 'status',
                                    'new' => '7',
                                ])->whereDate('created_at', '>=', Carbon::parse('6 August 2024'))
                                ->orderBy('created_at', 'DESC');
                            })->take(3)->get();


        $todays_tasks = Task::where('status', 7)
//                            ->whereIn('brand_id', $restricted_ids)
                            ->whereIn('category_id', $category_id_array)
                            ->whereHas('status_logs', function ($q) {
                                return $q->where([
                                    'column' => 'status',
                                    'new' => '7',
                                ])->whereDate('created_at', Carbon::today())
                                ->orderBy('created_at', 'DESC');
                            })->take(3)->get();

        return view('qa.home', compact(
            'total_tasks_count',
            'sent_for_qa_count',
            'sent_for_approval_count',
            'completed_count',
            'recent_tasks',
            'todays_tasks',
        ));
    }
}
