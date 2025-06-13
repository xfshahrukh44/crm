<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $services = Service::all();

        return view('v2.service.index', compact('services'));
    }

    public function create (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.service.create');
    }

    public function store (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $request->validate([
            'name' => 'required',
            'form' => 'required',
        ]);

        $request->request->add(['brand_id' => 1]);
        Service::create($request->all());
        return redirect()->route('v2.services')->with('success', 'Service created Successfully.');
    }

    public function edit (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$service = Service::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        return view('v2.service.edit', compact('service'));
    }

    public function update (Request $request, $id)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!$service = Service::find($id)) {
            return redirect()->back()->with('error', 'Not found.');
        }

        $request->validate([
            'name' => 'required',
            'form' => 'required',
        ]);
        $request->request->add(['brand_id' => 1]);
        $service->update($request->all());

        return redirect()->route('v2.services')->with('success', 'Service created Successfully.');
    }

    public function show (Request $request, $id)
    {
//        if (!v2_acl([2])) {
//            return redirect()->back()->with('error', 'Access denied.');
//        }
//
//        if (!$service = Service::find($id)) {
//            return redirect()->back()->with('error', 'Not found.');
//        }
//
//        return view('v2.service.show', compact('service'));
    }
}
