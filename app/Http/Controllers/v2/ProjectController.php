<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProjectController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = $this->getBrands();
        $categories = \App\Models\Category::all();
        $projects = \App\Models\Project::whereHas('client')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('brand_id', auth()->user()->brand_list());
            })
            ->when(!is_null(request()->get('start_date')) && request()->get('start_date') != '', function ($q) {
                return $q->whereDate('created_at', '>=', request()->get('start_date'));
            })->when(!is_null(request()->get('end_date')) && request()->get('end_date') != '', function ($q) {
                return $q->whereDate('created_at', '<=', request()->get('end_date'));
            })->when(request()->get('brand') != null && request()->get('brand') != '', function ($q) {
                return $q->where('brand_id', request()->get('brand'));
            })->when(request()->get('client') != null && request()->get('client') != '', function ($q) {
                $name = request()->get('client');
                return $q->whereHas('client', function ($query) use ($name){
                    return $query->where('name', 'LIKE', "%{$name}%")
                        ->orWhere('last_name', 'LIKE', "%{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%");
                });
            })->when(request()->get('user') != null && request()->get('user') != '', function ($q) {
                $name = request()->get('user');
                return $q->whereHas('added_by', function ($query) use ($name){
                    return $query->where('name', 'LIKE', "%{$name}%")
                        ->orWhere('last_name', 'LIKE', "%{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%");
                });
            })->orderBy('id', 'desc')->paginate(20);

        return view('v2.project.index', compact('projects', 'brands', 'categories'));
    }

    public function create (Request $request)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        return view('v2.project.create');
    }

    public function store (Request $request)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        $request->validate([
//            'name' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|email|unique:projects,email',
//            'status' => 'required',
//            'brand_id' => 'required',
//        ]);
//
//        //user check
//        if (
//            DB::table('users')->where('email', $request->email)->first() ||
//            DB::table('projects')->where('email', $request->email)->first()
//        ) { return redirect()->back()->with('error', 'Email already taken'); }
//
//        $project = Project::create($request->except('_token'));
//
//        //create user record
//        DB::table('users')->insert([
//            'name' => $project->name,
//            'last_name' => $project->last_name,
//            'email' => $project->contact,
//            'contact' => $project->contact,
//            'status' => 1,
//            'password' => Hash::make('qwerty'),
//            'is_employee' => 3,
//            'project_id' => $project->id,
//        ]);
//
//        //create stripe customer
//        create_clients_merchant_accounts($project->id);
//
//        return redirect()->route('v2.invoices.create', $project->id)->with('success','Project created Successfully.');
    }

    public function edit (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$project = Project::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.project.edit', compact('project'));
    }

    public function update (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$project = Project::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        $request->validate([
//            'name' => 'required',
//            'brand_id' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|unique:projects,email,'.$project->id,
////            'email' => 'required' . !is_null($project->user) ? ('|unique:users,email,'.$project->user->id) : '',
//            'status' => 'required',
//        ]);
//
//        $project->update($request->all());
//
//        return redirect()->route('v2.projects')->with('success','Project updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$project = Project::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.project.show', compact('project'));
    }

    public function getBrands ()
    {
        return \Illuminate\Support\Facades\DB::table('brands')
            ->when(!v2_acl([2]), function ($q) {
                return $q->whereIn('id', auth()->user()->brand_list());
            })
            ->get();
    }
}
