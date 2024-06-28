<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Brand::all();
        return view('admin.brand.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'url' => 'required',
                'image' => 'required',
                'phone' => 'required',
                'phone_tel' => 'required',
                'email' => 'required',
                'address' => 'required',
                'address_link' => 'required',
                'sign' => 'required'
            ]);

            $request->request->add(['auth_key' => sha1(time())]);
            if($request->has('image')){
                $file = $request->file('image');
                $name = $file->getClientOriginalName();
                $file->move('uploads/brands', $name);
                $path = 'uploads/brands/'.$name;
                $request->request->add(['logo' => $path]);
            }
            Brand::create($request->all());
            return redirect()->route('brand.index')->with('success','Brand created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Brand $brand)
    {
        try {
            $client_datas = Client::where('brand_id', $brand->id)->orderBy('id', 'desc')->paginate(20);
            $invoice_datas = Invoice::where('brand', $brand->id)->orderBy('id', 'desc')->where('payment_status', 2)->paginate(20);
            $un_paid_invoice_datas = Invoice::where('brand', $brand->id)->orderBy('id', 'desc')->where('payment_status', 1)->paginate(20);
            $project_datas = Project::where('brand_id', $brand->id)->orderBy('id', 'desc')->paginate(20);
            $task_datas = Task::where('brand_id', $brand->id)->paginate(20);
            return view('admin.brand.show', compact('brand', 'client_datas', 'invoice_datas', 'un_paid_invoice_datas', 'project_datas', 'task_datas'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Brand::find($id);
        return view('admin.brand.edit', compact('data'));
    }

    public function update(Request $request, Brand $brand)
    {
        try {
            $request->validate([
                'name' => 'required',
                'url' => 'required',
                'phone' => 'required',
                'phone_tel' => 'required',
                'email' => 'required',
                'address' => 'required',
                'address_link' => 'required',
                'sign' => 'required'
            ]);
            if($request->has('image')){
                $file = $request->file('image');
                $name = $file->getClientOriginalName();
                $file->move('uploads/brands', $name);
                $path = 'uploads/brands/'.$name;
                $request->request->add(['logo' => $path]);
                if($brand->logo != ''  && $brand->logo != null){
                    $file_old = $brand->logo;
                    unlink($file_old);
                }
            }
            $brand->update($request->all());
            return redirect()->route('brand.edit', $brand->id)->with('success','Brand Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
