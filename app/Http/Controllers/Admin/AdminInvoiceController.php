<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminInvoice;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminInvoiceController extends Controller
{
    public function index (Request $request) {
        $admin_invoices = AdminInvoice::all();

        return view('admin.admin-invoice.index', compact('admin_invoices'));
    }

    public function create() {
        return view('admin.admin-invoice.create');
    }

    public function store (Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'client_id' => 'required',
//            'client_name' => 'required',
//            'client_email' => 'required',
//            'client_phone' => 'required',
//            'brand_id' => 'required',
//            'service' => 'required',
//            'department' => 'required',
//            'currency' => 'required',
//            'amount' => 'required',
//            'type' => 'required',
//            'sale_upsell' => 'required',
//            'merchant_id' => 'required',
//            'payment_id' => 'required',
//            'invoice_number' => 'required',
//            'sales_agent_id' => 'required',
//            'transfer_by_id' => 'required',
//            'recurring' => 'required',
//            'refund_cb' => 'required',
//            'refund_cb_date' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            return redirect()->back()->with('error', $validator->errors()->first());
//        }

        $data = $request->all();
        if ($request->has('service')) {
            $data['service'] = implode(',', $request->get('service'));
        }

        $record = AdminInvoice::create($data);

        return redirect()->route('admin.admin-invoice.index')->with('success', 'Admin invoice created!');
    }
}
