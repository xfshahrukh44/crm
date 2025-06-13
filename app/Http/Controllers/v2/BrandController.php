<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $brands = Brand::orderBy('created_at', 'DESC')->paginate(33);

        return view('v2.brand.index', compact('brands'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.brand.create');
    }

    public function store (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

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
        return redirect()->route('v2.brands')->with('success','Brand created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$brand = Brand::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.brand.edit', compact('brand'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$brand = Brand::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

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

        return redirect()->route('v2.brands')->with('success','Brand updated Successfully.');
    }

    public function show (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$brand = Brand::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.brand.show', compact('brand'));
    }
}
