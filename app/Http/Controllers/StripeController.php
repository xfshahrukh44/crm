<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function stripe_invoice_paid (Request $request)
    {
        Log::info('----STRIPE PAYMENT WEBHOOK START----');
        Log::info(json_encode($request->all()));

        if ($request->data->object->id && $invoice = Invoice::where('stripe_invoice_id', $request->data->object->id)->first()) {
            $invoice->payment_status = 2;
            $invoice->save();
        }

        Log::info('----STRIPE PAYMENT WEBHOOK END----');
    }
}
