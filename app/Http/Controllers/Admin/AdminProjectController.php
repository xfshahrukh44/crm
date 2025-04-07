<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Category;
use App\Models\User;
use Auth;
use DB;

class AdminProjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = new Project();
            $data = $data->whereHas('client')->select('id', 'name', 'status', 'product_status', 'user_id', 'client_id', 'brand_id', 'created_at');
            if($request->brand != null){
                $data = $data->where('brand_id', $request->brand);
            }
            if($request->client != null){
                $name = $request->client;
                $data = $data->whereHas('client', function ($query) use ($name){
                    return $query->where('name', 'LIKE', "%{$name}%")->orWhere('last_name', 'LIKE', "%{$name}%")->orWhere('email', 'LIKE', "%{$name}%");
                });
            }
            if($request->user != null){
                $name = $request->user;
                $data = $data->whereHas('added_by', function ($query) use ($name){
                    return $query->where('name', 'LIKE', "%{$name}%")->orWhere('last_name', 'LIKE', "%{$name}%")->orWhere('email', 'LIKE', "%{$name}%");
                });
            }
            $data = $data->orderBy('id', 'desc')->paginate(20);
            $brands = DB::table('brands')->select('id', 'name')->get();
            $categorys = DB::table('create_categories')->select('id', 'name')->get();
            return view('admin.project.index', compact('data', 'brands', 'categorys'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function indexManager()
    {
        try {
            $data = Project::whereHas('client')->whereIn('brand_id', Auth()->user()->brand_list());

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

            $data = $data->get();

            $brands = Brand::whereIn('id', Auth()->user()->brand_list())->get();
            $clients = User::where('is_employee', 3)->whereHas('client', function ($query){
                return $query->whereIn('brand_id', Auth()->user()->brand_list());
            })->get();
            $users = User::where('is_employee', 0)->whereHas('brands', function ($query){
                return $query->whereIn('brand_id', Auth()->user()->brand_list());
            })->get();
            $categorys = Category::all();

            //filter by invoice id
            if (isset($_GET['invoice_id'])) {
                $data = $data->filter(function ($data_item) {
                    $check = false;

                    $form_checker_check = form_checker_model_map($data_item->form_checker);
                    if ($form_checker_check) {
                        $query = new $form_checker_check;
                        $query = $query->where('invoice_id', $_GET['invoice_id'])->first();

                        if ($query) {
                            $check = true;
                        }
                    }

                    return $check;
                });

            }

            // dd($data);

            return view('manager.project.index', compact('data', 'brands', 'clients', 'users', 'categorys'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.project.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:projects,email',
                'contact' => 'required',
                'status' => 'required',
                'brand_id' => 'required',
            ]);
            $request->request->add(['user_id' => auth()->user()->id]);
            Project::create($request->all());
            return redirect()->back()->with('success', 'Project created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function showNotification(Project $project, $id){
        try {
            $notification = auth()->user()->notifications()->where('id', $id)->first();
            $notification->markAsRead();
            return view('admin.project.show', compact('project'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        return view('admin.project.show', compact('project'));
    }

    public function indexEdit($id)
    {
        try {
            $data = Project::find($id);
            $clients = Client::where('status', 1)->whereIn('brand_id', Auth()->user()->brand_list())->get();
            $category = Category::where('status', 1)->get();
            return view('manager.project.edit', compact('data', 'category', 'clients'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $data = Project::find($id);
            $clients = Client::where('status', 1)->get();
            $category = Category::where('status', 1)->get();
            return view('admin.project.edit', compact('data', 'category', 'clients'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Project $project)
    {
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email,'.$project->id,
                'contact' => 'required',
                'status' => 'required',
            ]);
            $request->request->add(['brand_id' => auth()->user()->brand_id]);
            $project->update($request->all());
            return redirect()->back()->with('success', 'Project Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }

    public function paymentLink($id){
        try {
            $user = Project::find($id);
            $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
            return view('sale.payment.create', compact('user', 'brand'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
