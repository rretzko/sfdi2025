<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handlePayPal(Request $request)
    {
        //validate the request
        //process the payment confirmation
        //update the database
        Log::info('PayPal webhook received', $request->all());
    }

    public function handleSquare(Request $request)
    {
        //validate the request
        //process the payment confirmation
        //update the database
        Log::info('Square webhook received', $request->all());
    }
}
