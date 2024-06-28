<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Service;
use App\Models\Brand;
use App\Models\Currency;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Package::all();
        $data = Service::all();
        $brands = Brand::where('status', 1)->get();
        return view('admin.package.index', compact('data', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::where('status', 1)->get();
        $currencys = Currency::all();
        return view('admin.package.create', compact('brands', 'currencys'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'brand' => 'required'
            ]);
            $request->request->add(['brand_id' => $request->brand]);
            Service::create($request->all());
            return redirect()->back()->with('success', 'Service created Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        //
    }

    public function edit($id)
    {
        try {
            $data = Service::find($id);
            $brands = Brand::where('status', 1)->get();
            return view('admin.service.edit', compact('data', 'brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Service $service)
    {
        try {
            $request->validate([
                'name' => 'required',
                'brand' => 'required'
            ]);
            $request->request->add(['brand_id' => $request->brand]);
            $service->update($request->all());
            return redirect()->back()->with('success', 'Service Updated Successfully.');
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
