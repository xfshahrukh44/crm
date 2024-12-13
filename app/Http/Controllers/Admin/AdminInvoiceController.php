<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminInvoice;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AdminInvoiceController extends Controller
{
    public function index (Request $request) {
        $selected_month = $request->get('month') ?? null;
        $request->merge(['month' => $selected_month]);

        $admin_invoices = AdminInvoice::orderBy('date', 'DESC')
            ->when($request->has('brand_name') && $request->get('brand_name') != '', function ($q) use ($request) {
                return $q->where('brand_name', $request->get('brand_name'));
            })
            ->when($selected_month, function ($q) use($selected_month) {
                return $q->whereMonth('date', '=', $selected_month);
            })
            ->get();

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

    public function import (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $raw_data = Excel::toArray([], $request->file('file'));

        foreach ($raw_data[0] as $key => $item) {
            if ($key == 0) {
                continue;
            }

//            dump($item);
//            continue;
            if (is_null($item[0])) {
                AdminInvoice::create([
                    'sr_no' => $item[0] ?? null,
                    'client_id' => intval($item[1]) ?? null,
                    'client_name' => $item[2] ?? null,
                    'client_email' => $item[3] ?? null,
                    'client_phone' => $item[4] ?? null,
                    'service_name' => $item[5] ?? null,
                    'amount' => floatval(preg_replace('/[^0-9.]/', '', $item[6])) ?? null,
                    'recurring' => floatval(preg_replace('/[^0-9.]/', '', $item[7])) ?? null,
                    'date' => Carbon::parse(Date::excelToDateTimeObject($item[8])->format('d-M-y')) ?? null,
                    'sales_person_name' => $item[9] ?? null,
                    'sale_upsell' => $item[10] ?? null,
                    'transfer_by_name' => $item[11] ?? null,
                    'department' => $item[12] ?? null,
                    'brand_name' => $item[13] ?? null,
                    'type' => $item[14] ?? null,
                    'merchant_name' => $item[15] ?? null,
                    'payment_id' => $item[16] ?? null,
                    'invoice_number' => $item[17] ?? null,
                    'refund_cb' => floatval(preg_replace('/[^0-9.]/', '', $item[18])) ?? null,
                    'refund_cb_date' => Carbon::parse(Date::excelToDateTimeObject($item[19])->format('d-M-y')) ?? null,
                    'currency' => 1
                ]);
            } else {
                AdminInvoice::firstOrCreate([
                    'sr_no' => $item[0] ?? null,
                ], [
                    'client_id' => intval($item[1]) ?? null,
                    'client_name' => $item[2] ?? null,
                    'client_email' => $item[3] ?? null,
                    'client_phone' => $item[4] ?? null,
                    'service_name' => $item[5] ?? null,
                    'amount' => floatval(preg_replace('/[^0-9.]/', '', $item[6])) ?? null,
                    'recurring' => floatval(preg_replace('/[^0-9.]/', '', $item[7])) ?? null,
                    'date' => Carbon::parse(Date::excelToDateTimeObject($item[8])->format('d-M-y')) ?? null,
                    'sales_person_name' => $item[9] ?? null,
                    'sale_upsell' => $item[10] ?? null,
                    'transfer_by_name' => $item[11] ?? null,
                    'department' => $item[12] ?? null,
                    'brand_name' => $item[13] ?? null,
                    'type' => $item[14] ?? null,
                    'merchant_name' => $item[15] ?? null,
                    'payment_id' => $item[16] ?? null,
                    'invoice_number' => $item[17] ?? null,
                    'refund_cb' => floatval(preg_replace('/[^0-9.]/', '', $item[18])) ?? null,
                    'refund_cb_date' => Carbon::parse(Date::excelToDateTimeObject($item[19])->format('d-M-y')) ?? null,
                    'currency' => 1
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data imported!');
    }
}
