<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function stripe_invoice_paid (Request $request)
    {
        Log::info('----STRIPE PAYMENT WEBHOOK START----');
        Log::info(json_encode($request->all()));
        Log::info('----STRIPE PAYMENT WEBHOOK END----');
    }
}
