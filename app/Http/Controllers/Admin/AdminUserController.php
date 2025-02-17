<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\UserCategory;
use App\Models\UserFinance;
use Illuminate\Http\Request;
use Hash;


class AdminUserController extends Controller
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
        $brand = Brand::where('status', 1)->get();
		return view('admin.production.create', compact('category', 'brand'));
    }

    public function getUserProduction(Request $request){
    	$user = User::whereIn('is_employee', [1, 5])->get();
        return view('admin.production.index', compact('user'));
    }

    public function updateStatus(Request $request){
        try {
            $user_id = $request->input('user_id');
            $status = $request->input('status');
            $user = User::find($user_id);
            $user->status = $status;
            $user->save();
            return redirect()->back()->with('success', 'Status Updated');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateSalePassword(Request $request){
        try {
            $user_id = $request->input('user_id');
            $user = User::find($user_id);
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return redirect()->back()->with('success', 'Password Updated Successfully ( ID: ' . $user_id . ' )');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getUserSale(){
    	$user = User::whereIn('is_employee', [0, 4, 6])->get();
		return view('admin.sale-person.index', compact('user'));
    }

    public function createUserSale(){
        $brand = Brand::where('status', 1)->get();
        return view('admin.sale-person.create', compact('brand'));
    }

    public function storeUserSale(Request $request){
        try {
            $request->validate([
                'name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email',
                'status' => 'required',
                'password' => 'required',
                'is_employee' => 'required',
            ]);

//        if(($request->input('is_employee') == 0) || ($request->input('is_employee') == 4) || ($request->input('is_employee') == 6)){
//            $request->validate([
//                'brand' => 'required',
//            ]);
//        }else{
//            $request->validate([
//                'category' => 'required',
//            ]);
//        }
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

            $user->brands()->sync($request->input('brand'));
            $user->category()->sync($request->input('category'));

            if(($request->input('is_employee') == 0) || ($request->input('is_employee') == 4) || ($request->input('is_employee') == 6)){
                return redirect()->route('admin.user.sales.create')->with('success','Sale Person Created Successfully.');
            }else{
                return redirect()->route('admin.user.production.create')->with('success','Production Person Created Successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function editUserSale($id){
        try {
            $data = User::find($id);
            $brand = Brand::where('status', 1)->get();
            return view('admin.sale-person.edit', compact('data', 'brand'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function editUserProduction($id){
        try {
            $data = User::find($id);
            $category = Category::where('status', 1)->get();
            $brand = Brand::where('status', 1)->get();
            return view('admin.production.edit', compact('data', 'category', 'brand'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateUserSale(Request $request, $id){
        try {
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
                'daily_target' => $request->get('daily_target') ?? 1000.00,
                'daily_printing_costs' => $request->get('daily_printing_costs') ?? 0.00,
            ]);

            $user = User::find($id);
            $user->name = $request->input('name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->pseudo_name = $request->input('pseudo_name');
            $user->is_upsell = $request->input('is_upsell');

            if($request->input('password') != "")
            {
                $user->password = Hash::make($request->input('password'));
            }

            $user->contact = $request->input('contact');
            $user->status = $request->input('status');
            $user->is_employee = $request->input('is_employee');
            $user->is_employee = ($request->input('is_employee') == 8) ? 4 : $request->input('is_employee');
            $user->is_support_head = ($request->input('is_employee') == 8) ? true : false;
            $user->save();

            $brand = $request->input('brand');
            $user->brands()->sync($brand);
            $user->category()->sync($request->get('category'));

            $redirect_route = (in_array($user->is_employee, [1, 5])) ? 'admin.user.production.edit' : 'admin.user.sales.edit';
            return redirect()->route($redirect_route, $id)->with('success','Sale Person Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getUserQA(){
        $user = User::whereIn('is_employee', [7])->get();
        return view('admin.qa.index', compact('user'));
    }

    public function createUserQA(){
//        $brand = Brand::where('status', 1)->get();
        $category = Category::where('status', 1)->get();
        return view('admin.qa.create', compact('category'));
    }

    public function storeUserQA(Request $request){
        try {
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
            $user->is_support_head = $request->input('is_support_head');
            $user->save();
//        $brand = $request->input('brand');
//        $user->brands()->sync($brand);
            $category = $request->input('category');
            $user->category()->sync($category);

            return redirect()->route('admin.user.qa.create')->with('success','QA Person Created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function editUserQA($id){
        try {
            $data = User::find($id);
//        $brand = Brand::where('status', 1)->get();
            $category = Category::where('status', 1)->get();
            return view('admin.qa.edit', compact('data', 'category'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateUserQA(Request $request, $id){
        try {
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

            return redirect()->route('admin.user.qa.edit', $id)->with('success','QA Person Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
