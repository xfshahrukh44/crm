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

//        $token = get_authorize_token($id);

        $token = '';

        return view('client.invoice.pay-with-authorize', compact('invoice', 'token', 'brand'));
    }

    public function payWithAuthorizeSubmit (Request $request, $id){
        $invoice = Invoice::find($id);

        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvv' => 'required',
            'zip' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $authorize_charge_res = authorize_charge([
            'card_number' => $request->get('card_number'),
            'exp_month' => $request->get('exp_month'),
            'exp_year' => $request->get('exp_year'),
            'cvv' => $request->get('cvv'),
            'zip' => $request->get('zip'),
            'invoice_id' => $id,
            'address' => $request->get('address'),
            'city' => $request->get('city'),
            'state' => $request->get('state'),
            'country' => $request->get('country'),
            'end_user_ip' => request()->ip(),
        ]);

        if ($authorize_charge_res['success'] == true) {
            $invoice->authorize_transaction_id = $authorize_charge_res['data']['transaction_id'];
            $invoice->payment_status = 2;
            $invoice->save();

            if (auth()->check() && auth()->user()->is_employee == 3) {
                return redirect()->route('client.invoice')->with('success', 'Invoice paid successfully!');
            } else {
                return redirect()->back()->with('success', 'Invoice paid successfully!');
            }
        } else {
            return redirect()->back()->with('error', $authorize_charge_res['message']);
        }
    }

    public function confirmAuthorizePayment (Request $request, $id){
        $invoice = Invoice::find($id);
        $invoice->payment_status = 2;
        $invoice->save();

        if (auth()->check() && auth()->user()->is_employee == 3) {
            return redirect()->route('client.pay.with.authorize', $id)->with('success', 'Invoice paid successfully!');
        } else {
            return redirect()->back()->with('success', 'Invoice paid successfully!');
        }
    }
}
