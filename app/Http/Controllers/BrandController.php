<?php

namespace App\Http\Controllers;

use App\Models\AuthorWebsite;
use App\Models\BookCover;
use App\Models\BookFormatting;
use App\Models\Bookprinting;
use App\Models\BookWriting;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\ContentWritingForm;
use App\Models\Isbnform;
use App\Models\LogoForm;
use App\Models\NoForm;
use App\Models\Project;
use App\Models\Proofreading;
use App\Models\SeoForm;
use App\Models\SmmForm;
use App\Models\Task;
use App\Models\User;
use App\Models\WebForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    protected $layout;

    public function construct()
    {
        if (!Auth::check() || !in_array(Auth::user()->is_employee, [2, 6])) {
            return false;
        }

        if (Auth::user()->is_employee == 2) {
            $this->layout = 'layouts.app-admin';
        } else if (Auth::user()->is_employee == 6) {
            $this->layout = 'layouts.app-manager';
        }

        return true;
    }

    public function brands_dashboard (Request $request)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $brands = Brand::
//        whereHas('projects', function ($q) {
//            return $q->whereHas('tasks', function ($q) {
//                return $q->orderBy('created_at', 'DESC');
//            })->orderBy('created_at', 'DESC');
//        })
        when(Auth::user()->is_employee == 6, function ($q) {
            return $q->whereIn('id', Auth::user()->brand_list());
        })
        ->when($request->has('brand_name'), function ($q) use ($request) {
            return $q->where(function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('url', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('phone', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('phone_tel', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('email', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('address', 'LIKE', '%'.$request->get('brand_name').'%')
                    ->orWhere('address_link', 'LIKE', '%'.$request->get('brand_name').'%');
            });
        })
        ->orderBy('created_at', 'DESC')
        ->paginate(30);


        return view('brand-dashboard', compact('brands'))->with(['layout' => $this->layout]);
    }

    public function brands_detail (Request $request, $id)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $brand= Brand::with('clients')->find($id);
        $clients = Client::where('brand_id', $id)
            ->withCount('projects')->withCount('invoices')
            ->orderBy('projects_count', 'desc')->orderBy('invoices_count', 'desc')
            ->when($request->has('client_name'), function ($q) use ($request) {
                return $q->whereHas('user', function ($q) use ($request) {
                    return $q->where('name', 'LIKE', '%'.$request->get('client_name').'%')
                        ->orWhere('last_name', 'LIKE', '%'.$request->get('client_name').'%')
                        ->orWhere('email', 'LIKE', '%'.$request->get('client_name').'%')
                        ->orWhere('contact', 'LIKE', '%'.$request->get('client_name').'%');
                });
            })
            ->paginate(25);

        $brand_user_ids = DB::table('brand_users')->where('brand_id', $id)->pluck('user_id')->toArray();

        $buhs = User::whereIn('id', $brand_user_ids)->where('is_employee', 6)->get();
        $agents = User::whereIn('id', $brand_user_ids)->where('is_employee', 4)->get();

        return view('brand-detail', compact('brand', 'clients', 'buhs', 'agents'))->with(['layout' => $this->layout]);
    }

    public function clients_detail (Request $request, $id)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $client= Client::find($id);
        $client_user = \App\Models\User::where('client_id', $client->id)->first();

//        $projects = Project::where('client_id', $id)->get();
        $projects = $client_user ? $client_user->projects : [];

        $brief_pending_count = 0;
        $pending_project_count = 0;

        if ($client_user) {
            $brief_pending_count = count(LogoForm::where('logo_name', '')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->where('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(WebForm::where('business_name', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->where('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(SmmForm::where('business_name', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->where('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(ContentWritingForm::where('company_name', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->where('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(SeoForm::where('company_name', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(BookFormatting::where('book_title', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(AuthorWebsite::where('author_name', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(BookWriting::where('book_title', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(Proofreading::where('description', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(BookCover::where('title', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(Isbnform::where('pi_fullname', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $brief_pending_count += count(Bookprinting::where('title', null)->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->where('user_id', $client_user->id)->groupBy('user_id')->get());

            $pending_project_count += count(LogoForm::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(WebForm::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(SmmForm::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(ContentWritingForm::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(SeoForm::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(BookFormatting::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(BookWriting::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(AuthorWebsite::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(Proofreading::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(BookCover::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(Isbnform::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(Bookprinting::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

            $pending_project_count += count(NoForm::with('project')->doesntHave('project')->when(Auth::user()->is_employee != 2, function ($q) {
                return $q->whereHas('invoice', function ($q) {
                    return $q->whereIn('brand', Auth::user()->brand_list());
                });
            })->orderBy('id', 'desc')->where('user_id', $client_user->id)->get());

        }

        return view('client-detail', compact('client', 'projects', 'brief_pending_count', 'pending_project_count'))->with(['layout' => $this->layout]);
    }

    public function projects_detail (Request $request, $id)
    {
        if (!$this->construct()) {
            return redirect()->route('login');
        }

        $project= Project::find($id);

        $category_ids_from_tasks = array_unique(Task::where('project_id', $id)->where('status', '!=', 3)->pluck('category_id')->toArray());

        $categories_with_active_tasks = [];
        foreach ($category_ids_from_tasks as $category_id_from_tasks) {
            if (Auth::user()->is_employee == 2) {
                $tasks = Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])->where('status', '!=', 3)->get();
            } else {
                $tasks = Task::where(['project_id' => $id, 'category_id' => $category_id_from_tasks])
                    ->where('status', '!=', 3)->whereIn('brand_id', Auth::user()->brand_list())->get();
            }
            $categories_with_active_tasks []= [
                'category' => Category::find($category_id_from_tasks),
                'tasks' => $tasks,
            ];
        }


//        $projects = Project::where('project_id', $id)->get();

        return view('project-detail', compact('project', 'categories_with_active_tasks'))->with(['layout' => $this->layout]);
    }
}
