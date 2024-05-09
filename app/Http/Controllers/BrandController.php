<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function brands_dashboard (Request $request)
    {
        $brands = Brand::whereHas('projects', function ($q) {
            return $q->whereHas('tasks', function ($q) {
                return $q->orderBy('created_at', 'DESC');
            })->orderBy('created_at', 'DESC');
        })->get();

        return view('brand-dashboard', compact('brands'));
    }

    public function brands_detail (Request $request, $id)
    {
        $brand= Brand::with('clients')->find($id);
        $clients = Client::where('brand_id', $id)
            ->withCount('projects')->withCount('invoices')
            ->orderBy('projects_count', 'desc')->orderBy('invoices_count', 'desc')
//            ->orderBy('name', 'ASC')->get();
            ->get();

        $buhs = User::where('is_employee', 6)->whereHas('brands', function ($q) use($brand) {
            return $q->where('id', $brand->id);
        })->get();

        $agents = User::where('is_employee', 4)->whereHas('brands', function ($q) use($brand) {
            return $q->where('id', $brand->id);
        })->get();

        return view('brand-detail', compact('brand', 'clients', 'buhs', 'agents'));
    }

    public function clients_detail (Request $request, $id)
    {
        $client= Client::find($id);

        $projects = Project::where('client_id', $id)->get();

        return view('client-detail', compact('client', 'projects'));
    }

    public function projects_detail (Request $request, $id)
    {
        $project= Project::find($id);

        $category_ids_from_tasks = array_unique(Task::where('project_id', $id)->where('status', '!=', 3)->pluck('category_id')->toArray());

        $categories_with_active_tasks = [];
        foreach ($category_ids_from_tasks as $category_id_from_tasks) {
            $categories_with_active_tasks []= [
                'category' => Category::find($category_id_from_tasks),
                'tasks' => Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])->where('status', '!=', 3)->get(),
            ];
        }


//        $projects = Project::where('project_id', $id)->get();

        return view('project-detail', compact('project', 'categories_with_active_tasks'));
    }
}
