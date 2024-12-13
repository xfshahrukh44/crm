<?php

namespace App\Http\Controllers;

use App\Models\AdminInvoice;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ManagerAdminInvoiceController extends Controller
{
    public function index (Request $request) {
        $brand_names = Brand::whereIn('id', auth()->user()->brand_list())->pluck('name')->toArray();

        $admin_invoices = AdminInvoice::whereIn('brand_name', $brand_names)->orderBy('date', 'DESC')
            ->when($request->has('brand_name') && $request->get('brand_name') != '', function ($q) use ($request) {
                return $q->where('brand_name', $request->get('brand_name'));
            })
            ->get();

        return view('manager.admin-invoice.index', compact('admin_invoices'));
    }

    public function create() {
        return view('manager.admin-invoice.create');
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

        return redirect()->route('manager.admin-invoice.index')->with('success', 'Admin invoice created!');
    }
}
