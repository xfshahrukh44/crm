<?php

namespace App\Http\Controllers;

use App\Models\BookMarketing;
use App\Models\Client;
use App\Models\Brand;
use App\Models\NewSMM;
use App\Models\PressReleaseForm;
use App\Models\SeoBrief;
use App\Models\Task;
use App\Models\Category;
use App\Models\Message;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\LogoForm;
use App\Models\WebForm;
use App\Models\SmmForm;
use App\Models\ContentWritingForm;
use App\Models\SeoForm;
use App\Models\BookFormatting;
use App\Models\BookCover;
use App\Models\BookWriting;
use App\Models\AuthorWebsite;
use App\Models\Proofreading;
use App\Models\Isbnform;
use App\Models\Bookprinting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = new Client;
        $data = $data->where('user_id', Auth()->user()->id);
        $data = $data->when(!is_null(request()->get('start_date')), function ($q) {
            return $q->whereDate('created_at', '>=', request()->get('start_date'));
        });
        $data = $data->when(!is_null(request()->get('end_date')), function ($q) {
            return $q->whereDate('created_at', '<=', request()->get('end_date'));
        });
        $data = $data->orderBy('priority', 'ASC');
        $data = $data->orderBy('id', 'desc');
        if($request->name != ''){
            $data = $data->where('name', 'LIKE', "%$request->name%");
            $data = $data->where('name', 'LIKE', "%$request->last_name%");
        }
        if($request->email != ''){
            $data = $data->where('email', 'LIKE', "%$request->email%");
        }
        if($request->contact != ''){
            $data = $data->where('contact', 'LIKE', "%$request->contact%");
        }
        if($request->status != ''){
            $data = $data->where('status', $request->status);
        }
        if($request->task_id != ''){
            $data = $data
            ->where(function ($q) {
                return $q->whereHas('user', function ($q) {
                    return $q->whereHas('projects', function ($q) {
                        return $q->whereHas('tasks', function ($q) {
                            return $q->where('id', request()->get('task_id'));
                        });
                    });
                });
            })
            ->orWhere(function ($q) {
                return $q->whereHas('projects', function ($q) {
                    return $q->whereHas('tasks', function ($q) {
                        return $q->where('id', request()->get('task_id'));
                    });
                });
            })->whereIn('brand_id', auth()->user()->brand_list());
        }

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
        return view('sale.client.index', compact('data'));
    }

    public function managerClient(Request $request){
        $data = new Client;
        $data = $data->whereIn('brand_id', Auth()->user()->brand_list());
        $data = $data->when(!is_null(request()->get('start_date')), function ($q) {
            return $q->whereDate('created_at', '>=', request()->get('start_date'));
        });
        $data = $data->when(!is_null(request()->get('end_date')), function ($q) {
            return $q->whereDate('created_at', '<=', request()->get('end_date'));
        });
        $data = $data->orderBy('priority', 'ASC');
        $data = $data->orderBy('id', 'desc');
        if($request->name != ''){
            $data = $data->where('name', 'LIKE', "%$request->name%");
            $data = $data->where('name', 'LIKE', "%$request->last_name%");
        }
        if($request->email != ''){
            $data = $data->where('email', 'LIKE', "%$request->email%");
        }
        if($request->contact != ''){
            $data = $data->where('contact', 'LIKE', "%$request->contact%");
        }
        if($request->status != ''){
            $data = $data->where('status', $request->status);
        }
        if($request->task_id != ''){
            $data = $data
                ->where(function ($q) {
                    return $q->whereHas('user', function ($q) {
                        return $q->whereHas('projects', function ($q) {
                            return $q->whereHas('tasks', function ($q) {
                                return $q->where('id', request()->get('task_id'));
                            });
                        });
                    });
                })
                ->orWhere(function ($q) {
                    return $q->whereHas('projects', function ($q) {
                        return $q->whereHas('tasks', function ($q) {
                            return $q->where('id', request()->get('task_id'));
                        });
                    });
                })->whereIn('brand_id', auth()->user()->brand_list());
        }

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

        $data = $data->paginate(20);
        return view('manager.client.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sale.client.create');
    }

    public function managerClientCreate(){
        return view('manager.client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);

        if ($user_check = User::where('email', $request->email)->first()) {
            return redirect()->back()->with('error', 'Email already taken');
        }

        $request->request->add(['user_id' => auth()->user()->id]);
        $client = Client::create($request->all());

        //create stripe customer
        create_clients_merchant_accounts($client->id);

        if ($request->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return redirect()->route('client.generate.payment', $client->id);
    }

    public function managerClientStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:clients,email',
            'status' => 'required',
            'brand_id' => 'required',
        ]);
        $request->request->add(['user_id' => auth()->user()->id]);
        $client = Client::create($request->all());

        if ($request->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return redirect()->route('manager.generate.payment', $client->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Client::where('id', $id)
//            ->where('user_id', Auth::user()->id)
            ->first();
        if($data == null){
            return redirect()->back();
        }else{
            return view('sale.client.edit', compact('data'));
        }
    }

    public function managerClientEdit($id){
        $data = Client::where('id', $id)->whereIn('brand_id', Auth::user()->brand_list())->first();
        if($data == null){
            return redirect()->back();
        }else{
            return view('manager.client.edit', compact('data'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */

    public function managerClientUpdate(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);
        $client->update($request->all());
        return redirect()->back()->with('success', 'Client Updated Successfully.');
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'status' => 'required',
        ]);
        $client->update($request->all());
        return redirect()->back()->with('success', 'Client Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }

    public function paymentLink($id){
        $user = Client::find($id);
        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();;
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant = Merchant::orderBy('id', 'desc')->get();

        $sale_agents = User::whereIn('is_employee', [0, 4, 6])
            ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
            ->get();

        if (request()->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return view('sale.payment.create', compact('user', 'brand', 'currencies', 'services', 'merchant', 'sale_agents'));
    }

    public function managerPaymentLink($id){
        $user = Client::find($id);
        $brand = Brand::whereIn('id', Auth()->user()->brand_list())->get();
        $services = Service::all();
        $currencies =  Currency::all();
        $merchant = Merchant::where('status', 1)->orderBy('id', 'desc')->get();

        $sale_agents = User::whereIn('is_employee', [0, 4, 6])
            ->whereIn('id', array_unique(DB::table('brand_users')->where('brand_id', $user->brand_id)->pluck('user_id')->toArray()))
            ->get();

        if (request()->has('redirect_to_client_detail')) {
            session()->put('redirect_to_client_detail', true);
        }

        return view('manager.payment.create', compact('user', 'brand', 'currencies', 'services', 'merchant', 'sale_agents'));
    }

    public function getClientBrief(){
        $data = array();
        if(count(Auth()->user()->logoForm) != 0){
            foreach(Auth()->user()->logoForm as $logoForm){
                $logo_form = LogoForm::whereHas('invoice')->find($logoForm->id);
                if (!$logo_form) {
                    continue;
                }
                $logo_form->option = $logo_form->logo_name;
                $logo_form->form_type = 1;
                $logo_form->form_name = 'Logo';
                array_push($data, $logo_form);
            }
        }
        if(count(Auth()->user()->webForm) != 0){
            foreach(Auth()->user()->webForm as $webForm){
                $web_form = WebForm::whereHas('invoice')->find($webForm->id);
                if (!$web_form) {
                    continue;
                }
                $web_form->option = $web_form->business_name;
                $web_form->form_type = 2;
                $web_form->form_name = 'Web';
                array_push($data, $web_form);
            }
        }
        if(count(Auth()->user()->smmForm) != 0){
            foreach(Auth()->user()->smmForm as $smmForm){
                $smm_form = SmmForm::whereHas('invoice')->find($smmForm->id);
                if (!$smm_form) {
                    continue;
                }
                $smm_form->option = $smm_form->business_name;
                $smm_form->form_type = 3;
                $smm_form->form_name = 'SMM';
                array_push($data, $smm_form);
            }
        }
        if(count(Auth()->user()->contentWritingForm) != 0){
            foreach(Auth()->user()->contentWritingForm as $contentWritingForm){
                $content_form = ContentWritingForm::whereHas('invoice')->find($contentWritingForm->id);
                if (!$content_form) {
                    continue;
                }
                $content_form->option = $content_form->company_name;
                $content_form->form_type = 4;
                $content_form->form_name = 'Content Writing';
                array_push($data, $content_form);
            }
        }
        if(count(Auth()->user()->soeForm) != 0){
            foreach(Auth()->user()->soeForm as $soeForm){
                $seo_form = SeoForm::whereHas('invoice')->find($soeForm->id);
                if (!$seo_form) {
                    continue;
                }
                $seo_form->option = $seo_form->company_name;
                $seo_form->form_type = 5;
                $seo_form->form_name = 'SEO';
                array_push($data, $seo_form);
            }
        }
        if(count(Auth()->user()->bookFormattingForm) != 0){
            foreach(Auth()->user()->bookFormattingForm as $bookFormatting){
                $bookFormattingForm = BookFormatting::whereHas('invoice')->find($bookFormatting->id);
                if (!$bookFormattingForm) {
                    continue;
                }
                $bookFormattingForm->option = $bookFormatting->book_title;
                $bookFormattingForm->form_type = 6;
                $bookFormattingForm->form_name = 'Book Formatting & Publishing Form';
                array_push($data, $bookFormattingForm);
            }
        }
        if(count(Auth()->user()->bookWritingForm) != 0){
            foreach(Auth()->user()->bookWritingForm as $bookWriting){
                $bookWritingForm = BookWriting::whereHas('invoice')->find($bookWriting->id);
                if (!$bookWritingForm) {
                    continue;
                }
                $bookWritingForm->option = $bookWriting->book_title;
                $bookWritingForm->form_type = 7;
                $bookWritingForm->form_name = 'Book Writing Form';
                array_push($data, $bookWritingForm);
            }
        }

        if(count(Auth()->user()->authorWesbiteForm) != 0){
            foreach(Auth()->user()->authorWesbiteForm as $authorWesbiteForm){
                $authorWebsiteForm = AuthorWebsite::whereHas('invoice')->find($authorWesbiteForm->id);
                if (!$authorWebsiteForm) {
                    continue;
                }
                $authorWebsiteForm->option = $authorWesbiteForm->author_name;
                $authorWebsiteForm->form_type = 8;
                $authorWebsiteForm->form_name = 'Author Website Form';
                array_push($data, $authorWebsiteForm);
            }
        }

        if(count(Auth()->user()->proofreading) != 0){
            foreach(Auth()->user()->proofreading as $proofreading){
                $proofreadingForm = Proofreading::whereHas('invoice')->find($proofreading->id);
                if (!$proofreadingForm) {
                    continue;
                }
                $proofreadingForm->option = $proofreading->author_name;
                $proofreadingForm->form_type = 9;
                $proofreadingForm->form_name = 'Editing & Proofreading Form';
                array_push($data, $proofreadingForm);
            }
        }

        if(count(Auth()->user()->bookcover) != 0){
            foreach(Auth()->user()->bookcover as $bookcover){
                $bookcover = BookCover::whereHas('invoice')->find($bookcover->id);
                if (!$bookcover) {
                    continue;
                }
                $bookcover->option = $bookcover->author_name;
                $bookcover->form_type = 10;
                $bookcover->form_name = 'Book Cover Design Form';
                array_push($data, $bookcover);
            }
        }

        // Add two New Form

        if(count(Auth()->user()->isbnForm) != 0){
            foreach(Auth()->user()->isbnForm as $isbnform){
                $isbnform = Isbnform::whereHas('invoice')->find($isbnform->id);
                if (!$isbnform) {
                    continue;
                }
                $isbnform->option = $isbnform->author_name;
                $isbnform->form_type = 11;
                $isbnform->form_name = 'ISBN Form';
                array_push($data, $isbnform);
            }
        }


        if(count(Auth()->user()->bookPrintingForm) != 0){
            foreach(Auth()->user()->bookPrintingForm as $bookprinting){
                $bookprinting = Bookprinting::whereHas('invoice')->find($bookprinting->id);
                if (!$bookprinting) {
                    continue;
                }
                $bookprinting->option = $bookprinting->author_name;
                $bookprinting->form_type = 12;
                $bookprinting->form_name = 'Book Printing Form';
                array_push($data, $bookprinting);
            }
        }
        if(count(Auth()->user()->seoBrief) != 0){
            foreach(Auth()->user()->seoBrief as $seoBrief){
                $seo_form = SeoBrief::whereHas('invoice')->find($seoBrief->id);
                if (!$seo_form) {
                    continue;
                }
                $seo_form->option = $seo_form->company_name;
                $seo_form->form_type = 13;
                $seo_form->form_name = 'SEO';
                array_push($data, $seo_form);
            }
        }
        if(count(Auth()->user()->bookMarketings) != 0){
            foreach(Auth()->user()->bookMarketings as $bookMarketing){
                $book_marketing_form = BookMarketing::whereHas('invoice')->find($bookMarketing->id);
                if (!$book_marketing_form) {
                    continue;
                }
                $book_marketing_form->option = $book_marketing_form->client_name;
                $book_marketing_form->form_type = 14;
                $book_marketing_form->form_name = 'Book Marketing';
                array_push($data, $book_marketing_form);
            }
        }
        if(count(Auth()->user()->newSMMs) != 0){
            foreach(Auth()->user()->newSMMs as $newSMM){
                $new_smm_form = NewSMM::whereHas('invoice')->find($newSMM->id);
                if (!$new_smm_form) {
                    continue;
                }
                $new_smm_form->option = $new_smm_form->client_name;
                $new_smm_form->form_type = 15;
                $new_smm_form->form_name = 'Social Media Marketing (NEW)';
                array_push($data, $new_smm_form);
            }
        }
        if(count(Auth()->user()->press_releases) != 0){
            foreach(Auth()->user()->press_releases as $press_release){
                $new_smm_form = PressReleaseForm::whereHas('invoice')->find($press_release->id);
                if (!$new_smm_form) {
                    continue;
                }
                $new_smm_form->option = $new_smm_form->client_name;
                $new_smm_form->form_type = 16;
                $new_smm_form->form_name = 'Press Release';
                array_push($data, $new_smm_form);
            }
        }



//        return view('client.brief', compact('data'));
        return view('client.brief-forms', compact('data'));
    }

    public function getAssignedClient(){
        $data = Client::where('assign_id', Auth()->user()->id)->get();
        return view('sale.client.assigned', compact('data'));
    }

    public function clientProject(){
        $data = Task::whereHas('projects', function ($query) {
                    return $query->whereNotNull('user_id')->where('client_id', Auth::user()->id);
                })->get();
        return view('client.project', compact('data'));
    }

    public function clientTaskshow($id, $notify = null){
//        try {
//            $notifications = Auth::user()->Notifications->markAsRead();
//            if($notify != null){
//                $Notification = Auth::user()->Notifications->find($notify);
//                if($Notification){
//                    $Notification->markAsRead();
//                }
//            }
//            $messages = Message::where('user_id', Auth::user()->id)->orWhere('sender_id', Auth::user()->id)
//                ->orWhere('client_id', Auth::user()->id)
//                ->get();
//            return view('client.task-show', compact('messages'));
//        } catch (\Exception $e) {
//            return redirect()->back()->with('error', $e->getMessage());
//        }

        return view('client.messages');
    }

    public function managerClientById($id, $name){
        $user = User::find($id);
        if(in_array($user->client->brand->id, Auth()->user()->brand_list())){
            $messages = Message::where('client_id', $id)->orderBy('id', 'desc')->limit(3)->get();
            return view('manager.client.show', compact('user', 'messages'));
        }else{
            return redirect()->back();
        }
    }

    public function dashboard (Request $request)
    {
        return view('client.dashboard');
    }

    public function profile (Request $request)
    {
        return view('client.profile');
    }

    public function updateProfile (Request $request)
    {
        $validator_arr = [
            'name' => 'required',
            'last_name' => 'required',
            'contact' => 'nullable',
            'alternate_email' => 'email|nullable',
        ];

        if ($request->has('password') && !is_null($request->get('password'))) {
            $validator_arr['password'] = 'required|confirmed';
        }

        $request->validate($validator_arr);

        if (!$user = User::find(auth()->id())) {
            return redirect()->back()->with('error', "Couldn't update profile.");
        }

        $user->update($request->all());

        if(array_key_exists('password', $validator_arr)) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile updated!');
    }

    public function updateProfilePicture (Request $request)
    {
//        dd($request->all());
        if (!$request->has('profile_picture')) {
            return redirect()->back()->with('error', 'Invalid image file');
        }

        $file = $request->file('profile_picture');
        $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = strtolower(str_replace(' ', '-', $file_name)) . '_' .time().'.'.$file->extension();
        $file->move(public_path().'/uploads/users', $name);
        $user = User::find(auth()->id());
        $user->image = '/uploads/users/' . $name;
        $user->save();

        return redirect()->back()->with('success', 'Profile picture updated!');
    }
}
