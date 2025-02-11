<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientInvoiceController extends Controller
{
    public function getInvoiceByUserId (Request $request){
        $user_client = Client::find(auth()->user()->client_id);

        $data = new Invoice;
        $data = $data->where('payment_status', '!=', 2)->orderBy('id', 'desc')->where('client_id', auth()->user()->client_id)->where('brand', $user_client->brand_id);
        $data = $data->paginate(10);

//        return view('client.invoice.index', compact('data'));
        return view('client.new-invoice-list', compact('data'));
    }
    public function invoiceDetail (Request $request, $id){
        if (!$invoice = Invoice::find($id)) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        return view('client.invoice-detail', compact('invoice'));
    }

    public function payWithAuthorize (Request $request, $id){
        $invoice = Invoice::find($id);
        $brand = Brand::find($invoice->brand);

        return view('client.invoice.pay-with-authorize', compact('invoice', 'brand'));
    }

    public function payWithAuthorizeSubmit (Request $request, $id){
        $invoice = Invoice::find($id);

        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvv' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $authorize_charge_res = authorize_charge($request->get('card_number'), $request->get('exp_month'), $request->get('exp_year'), $request->get('cvv'), $id);
        if ($authorize_charge_res['success'] == true) {
            $invoice->authorize_transaction_id = $authorize_charge_res['data']['transaction_id'];
            $invoice->payment_status = 2;
            $invoice->save();

            return redirect()->route('client.invoice')->with('success', 'Invoice paid successfully!');
        } else {
            return redirect()->back()->with('error', $authorize_charge_res['message']);
        }
    }
}
