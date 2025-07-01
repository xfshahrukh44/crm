<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientInvoiceController extends Controller
{
    public function getInvoiceByUserId (Request $request){
        $user_client = Client::find(auth()->user()->client_id);

        $data = new Invoice;
        $data = $data
//            ->where('payment_status', '!=', 2)
            ->orderBy('id', 'desc')->where('client_id', auth()->user()->client_id)->where('brand', $user_client->brand_id);
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

        $token = '';

//        return view('client.invoice.pay-with-authorize', compact('invoice', 'token', 'brand'));
        return view('client.invoice.pay-with-authorize-new', compact('invoice', 'token', 'brand'));
    }

    public function payWithAuthorize2 (Request $request, $id){
        $invoice = Invoice::find($id);
        $brand = Brand::find($invoice->brand);

        $token = '';

        return view('client.invoice.pay-with-authorize-new', compact('invoice', 'token', 'brand'));
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

        $res = $authorize_charge_res['success'];
        $res_message = $authorize_charge_res['message'];

        //notification for charge
        $user_ids = array_unique(DB::table('brand_users')->where('brand_id', $invoice->brand)->pluck('user_id')->toArray());
        $user_ids []= 1;
        $client = DB::table('clients')->where('id', $invoice->client_id)->first();
        $notification_data = json_encode([
            'id' => auth()->id(),
            'invoice_id' => $invoice->id,
            'name' => $res_message,
            'text' => (($client->name ?? '') . ' ' . ($client->last_name ?? '')) . (" INV#".$invoice->id." payment " . ($res ? 'successful.' : 'failed.')),
            'details' => $res_message,
        ]);
        $notification_type = $res ? 'App\CustomInvoicePaidNotification' : 'App\CustomInvoiceNotification';
        $users = DB::table('users')->whereIn('id', $user_ids)
            ->where(function ($q) use ($invoice) {
                return $q->whereIn('is_employee', [2, 6, 4])
                    ->orWhere('id', $invoice->sales_agent_id);
            })->get();
        foreach ($users as $user) {
            if ($user->is_employee == 4 && $user->is_support_head == 0 && $user->id != $invoice->sales_agent_id) {
                continue;
            }

            DB::table('notifications')->insert([
                'id' => Str::uuid(), //
                'type' => $notification_type,
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => $notification_data,
                'read_at' => null,
                'created_at' => \Carbon\Carbon::now(),
            ]);
        }

        if ($res) {
            $invoice->authorize_transaction_id = $authorize_charge_res['data']['transaction_id'];
            $invoice->payment_status = 2;
            $invoice->save();

            if (v2_acl([3])) {
                return redirect()->route('client.invoice')->with('success', 'Invoice paid successfully!');
            } else {
                return redirect()->back()->with('success', 'Invoice paid successfully!');
            }
        } else {
            //for storing failed charge message
            DB::table('failed_invoice_attempts')->insert([
                'invoice_id' => $invoice->id,
                'text' => $res_message,
            ]);

            return redirect()->back()->with('error', $res_message);
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
