<?php

namespace Laraditz\TngEwallet\Http\Controllers;

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Models\Payment;

class ReturnPaymentController
{
    public function __invoke(Request $request)
    {
        $payment = Payment::where('payment_request_id', $request->query('payment_request_id'))->first();

        if (! $payment) {
            return response()->view('tng-ewallet::return', [
                'state' => 'not_found',
                'backUrl' => config('tng-ewallet.default_return_url'),
            ]);
        }

        return response('', 200);
    }
}
