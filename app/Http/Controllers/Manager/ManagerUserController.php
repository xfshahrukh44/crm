<?php

namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\UserCategory;
use App\Models\UserFinance;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Auth;


class ManagerUserController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getProductionUser(){
        $category = Category::where('status', 1)->get();
		return view('manager.production.create', compact('category'));
    }

    public function getUserProduction(Request $request){
    	$user = User::whereIn('is_employee', [1, 5])->get();
        return view('manager.production.index', compact('user'));
    }

    public function updateStatus(Request $request){
    	$user_id = $request->input('user_id');
    	$status = $request->input('status');
    	$user = User::find($user_id);
    	$user->status = $status;
    	$user->save();
    	return redirect()->back()->with('success', 'Status Updated');
    }

    public function updateSalePassword(Request $request){
        $user_id = $request->input('user_id');
        $user = User::find($user_id);
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return redirect()->back()->with('success', 'Password Updated Successfully ( ID: ' . $user_id . ' )');
    }

    public function getUserSale(){
//    	$user = User::whereIn('is_employee', [0, 4])->get();
    	$user = User::whereIn('is_employee', [0, 4])->whereHas('brands', function ($q) {
    	    return $q->whereIn('id', Auth::user()->brand_list());
        })->get();
		return view('manager.sale-person.index', compact('user'));
    }

    public function createUserSale(){
//        $brand = Brand::where('status', 1)->get();
        $brand = Brand::where('status', 1)->whereIn('id', Auth::user()->brand_list())->get();
        return view('manager.sale-person.create', compact('brand'));
    }

    public function storeUserSale(Request $request){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'status' => 'required',
            'password' => 'required',
            'is_employee' => 'required',
        ]);

        if(($request->input('is_employee') == 0) || ($request->input('is_employee') == 4) || ($request->input('is_employee') == 8) || ($request->input('is_employee') == 6)){
            $request->validate([
                'brand' => 'required',
            ]);
        }else{
            $request->validate([
                'category' => 'required',
            ]);
        }
        $user = new User();
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->password = Hash::make($request->input('password'));
//        $user->is_employee = $request->input('is_employee');
        $user->is_employee = ($request->input('is_employee') == 8) ? 4 : $request->input('is_employee');
        $user->is_support_head = ($request->input('is_employee') == 8) ? true : false;
        $user->save();

        $brand = $request->input('brand');
        $user->brands()->sync($brand);
        return redirect()->route('manager.user.sales.create')->with('success','Sale Person Created Successfully.');
    }

    public function editUserSale($id){
        $data = User::find($id);
//        $brand = Brand::where('status', 1)->get();
        $brand = Brand::where('status', 1)->whereIn('id', Auth::user()->brand_list())->get();
        return view('manager.sale-person.edit', compact('data', 'brand'));
    }

    public function editUserProduction($id){
        $data = User::find($id);
        $category = Category::where('status', 1)->get();
        return view('manager.production.edit', compact('data', 'category'));
    }

    public function updateUserSale(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'status' => 'required',
            'is_employee' => 'required',
        ]);
        if(($request->input('is_employee') == 0) || ($request->input('is_employee') == 4) || ($request->input('is_employee') == 6)){
            $request->validate([
                'brand' => 'required',
            ]);
        }else{
//            $request->validate([
//                'category' => 'required',
//            ]);
        }

        UserFinance::updateOrCreate([
            'user_id' => $id,
        ], [
            'daily_target' => $request->get('daily_target')
        ]);

        $user = User::find($id);
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->pseudo_name = $request->input('pseudo_name');

        if($request->input('password') != "")
        {
            $user->password = Hash::make($request->input('password'));
        }

        $user->contact = $request->input('contact');
        $user->status = $request->input('status');
        $user->is_employee = ($request->input('is_employee') == 8) ? 4 : $request->input('is_employee');
        $user->is_support_head = ($request->input('is_employee') == 8) ? true : false;
        $user->save();

        $brand = $request->input('brand');
        $user->brands()->sync($brand);
        return redirect()->route('manager.user.sales.edit', $id)->with('success','Sale Person Updated Successfully.');
    }
}
