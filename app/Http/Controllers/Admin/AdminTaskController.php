<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\SubTask;
use App\Models\Project;
use App\Models\Message;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Auth;
use DB;

class AdminTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = new Task;
            $data = $data->orderBy('id', 'desc');
            if($request->brand != ''){
                $data = $data->where('brand_id', $request->brand);
            }
            if($request->name != ''){
                $customer = $request->name;
                $data = $data->whereHas(
                    'projects.client', function($q) use($customer){
                    $q->where('name', 'LIKE', "%$customer%");
                    $q->orWhere('last_name', 'LIKE', "%$customer%");
                    $q->orWhere('email', 'LIKE', "%$customer%");
                }
                );
            }
            if($request->agent != ''){
                $agent = $request->agent;
                $data = $data->whereHas(
                    'projects.added_by', function($q) use($agent){
                    $q->where('name', 'LIKE', "%$agent%");
                    $q->orWhere('last_name', 'LIKE', "%$agent%");
                    $q->orWhere('email', 'LIKE', "%$agent%");
                }
                );
            }
            if($request->category != ''){
                $data = $data->where('category_id', $request->category);
            }

            if($request->status != ''){
                $data = $data->where('status', $request->status);
            }
            //when project_id
            $data->when($request->has('project_id'), function ($q) use ($request) {
                return $q->where('project_id', $request->get('project_id'));
            });
            $data = $data->paginate(20);
            $brands = DB::table('brands')->select('id', 'name')->get();
            $categorys = DB::table('create_categories')->select('id', 'name')->get();
            $users = User::where('is_employee', 0)->get();
            return view('admin.task.index', compact('data', 'brands', 'users', 'categorys'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function indexManager(Request $request)
    {
        try {
            $data = new Task();
            $data = $data->orderBy('id', 'desc');
            //hide tasks in QA
            $data = $data->where('status', '!=', 5);
            $data = $data->whereIn('brand_id', Auth()->user()->brand_list());

            if($request->project != ''){
                $project = $request->project;
                $data = $data->whereHas(
                    'projects', function($q) use($project){
                    $q->where('name', 'LIKE', "%$project%");
                }
                );
            }

            if($request->customer != ''){
                $customer = $request->customer;
                $data = $data->whereHas(
                    'projects.client', function($q) use($customer){
                    $q->where('name', 'LIKE', "%$customer%");
                    $q->orWhere('last_name', 'LIKE', "%$customer%");
                    $q->orWhere('email', 'LIKE', "%$customer%");
                }
                );
            }
            if($request->agent != ''){
                $agent = $request->agent;
                $data = $data->whereHas(
                    'user', function($q) use($agent){
                    $q->where('name', 'LIKE', "%$agent%");
                    $q->orWhere('last_name', 'LIKE', "%$agent%");
                    $q->orWhere('email', 'LIKE', "%$agent%");
                }
                );
            }

            if($request->brand != 0){
                $brand = $request->brand;
                $data = $data->whereHas('brand', function($q) use($brand){
                    $q->where('id', $brand);
                });
            }

            if($request->status != null){
                $data = $data->where('status', $request->status);
            }

            if($request->category != 0){
                $data = $data->where('category_id', $request->category);
            }


            //when project_id
            $data->when($request->has('project_id'), function ($q) use ($request) {
                return $q->where('project_id', $request->get('project_id'));
            });
            $data = $data->paginate(10);
            // $data = $data->get();

            // $data = Task::whereIn('brand_id', Auth()->user()->brand_list())->get();
            $brands = Brand::whereIn('id', Auth()->user()->brand_list())->get();
            $clients = Client::whereIn('brand_id', Auth()->user()->brand_list())->get();
            $users = User::where('is_employee', 0)->whereHas('brands', function ($query){
                return $query->whereIn('brand_id', Auth()->user()->brand_list());
            })->get();

            $categorys = Category::all();

            $task_array = [];
            $support_staff_users = User::where('is_employee', 4)
                ->whereIn('id', DB::table('brand_users')->whereIn('brand_id', auth()->user()->brand_list())->pluck('user_id')->toArray())
                ->get();
            foreach ($support_staff_users as $support_staff_user) {
                $notification_task = $support_staff_user->notifications->where('type', 'App\Notifications\TaskNotification')->all();
                foreach($notification_task as $notification_tasks){
                    array_push($task_array, $notification_tasks['data']['task_id']);
                }
            }
            $task_array = array_unique($task_array);

            $notify_data = Task::whereIn('brand_id', Auth()->user()->brand_list())
                //hide tasks in QA
                ->where('status', '!=', 5)
                ->whereIn('id', $task_array)->orderBy('id', 'desc')->paginate(10);

            $display = '';


            if ($request->ajax()) {
                foreach ($data as $rander) {

                    if(auth()->user()->id == "33"){

                        $display .=
                            '<tr>
                        <td>'.$rander->id.'</td> 
                        <td><a href="'.route('manager.task.show', $rander->id).'">'.\Illuminate\Support\Str::limit(strip_tags($rander->description), 30, $end='...').'</a></td>
                        <td>'.$rander->projects->name.'</td>
                        <td>
                            '.$rander->projects->client->name . ' ' . $rander->projects->client->last_name . '<br>
                            '. $rander->projects->client->email .'
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm">'. $rander->user->name . ' ' . $rander->user->last_name . '</button>
                            <button class="btn btn-primary btn-sm" onclick="openReassign('.$rander->id.', '.$rander->brand->id.')">Reassign</button>
                        </td>
                        <td><button class="btn btn-primary btn-sm">'. implode('', array_map(function($v) { return $v[0]; }, explode(' ', $rander->brand->name))) .'</button></td>
                        <td><button class="btn btn-secondary btn-sm">'.$rander->category->name.'</button></td>
                        <td>'.$rander->project_status() .'</td>
                        <td>
                            <a href="'.route('manager.task.show', $rander->id).'" class="btn btn-primary btn-icon btn-sm">
                                <span class="ul-btn__text">Details</span>
                            </a>
                        </td>
                    </tr>';

                    }else{

                        $display .=
                            '<tr>
                        <td>'.$rander->id.'</td>
                        <td><a href="'.route('manager.task.show', $rander->id).'">'.\Illuminate\Support\Str::limit(strip_tags($rander->description), 30, $end='...').'</a></td>
                        <td>'.$rander->projects->name.'</td>
                        <td>
                            '.$rander->projects->client->name . ' ' . $rander->projects->client->last_name . '<br>
                            '. $rander->projects->client->email .'
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm">'. $rander->user->name . ' ' . $rander->user->last_name . '</button>
                        </td>
                        <td><button class="btn btn-primary btn-sm">'. implode('', array_map(function($v) { return $v[0]; }, explode(' ', $rander->brand->name))) .'</button></td>
                        <td><button class="btn btn-secondary btn-sm">'.$rander->category->name.'</button></td>
                        <td>'.$rander->project_status() .'</td>
                        <td>
                            <a href="'.route('manager.task.show', $rander->id).'" class="btn btn-primary btn-icon btn-sm">
                                <span class="ul-btn__text">Details</span>
                            </a>
                        </td>
                    </tr>';

                    }


                }


                return $display;
            }


            // dump($users);

            return view('manager.task.index', compact('data', 'brands', 'clients', 'users', 'categorys', 'notify_data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.task.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:tasks,email',
                'contact' => 'required',
                'status' => 'required',
                'brand_id' => 'required',
            ]);
            $request->request->add(['user_id' => auth()->user()->id]);
            Task::create($request->all());
            return redirect()->back()->with('success', 'Task created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task 
     * @return \Illuminate\Http\Response
     */

    public function show(Task $task )
    {
        $messages = Message::where('sender_id', $task->projects->client->id)->orWhere('user_id', $task->projects->client->id)->orderBy('id', 'desc')->get();
        return view('admin.task.show', compact('task', 'messages'));
    }

    public function showManager($id)
    {
        try {
            $task = Task::where('id', $id)->whereIn('brand_id', Auth()->user()->brand_list())->first();
            if($task != null){
                return view('manager.task.show', compact('task'));
            }else{
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email,'.$task->id,
                'contact' => 'required',
                'status' => 'required',
            ]);
            $request->request->add(['brand_id' => auth()->user()->brand_id]);
            $project->update($request->all());
            return redirect()->back()->with('success', 'Task Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }

    public function adminSubtaskStore(Request $request){
        try {
            $request->validate([
                'description' => 'required',
            ]);
            $request->request->add(['user_id' => auth()->user()->id]);
            $sub_task = SubTask::create($request->all());
            Task::where('id', $request->input('task_id'))->update(['status' => 1]);
            $data = SubTask::find($sub_task->id);
            $duedate = null;
            if($data->duedate != null){
                $duedate = date('d M, y', strtotime($data->duedate));
            }
            return response()->json(['success' => 'Sub Task created Successfully.', 'data' => $data, 'user_name' => auth()->user()->name, 'duedate' => $duedate, 'created_at' => $data->created_at->diffForHumans()]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
