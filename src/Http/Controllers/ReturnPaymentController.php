<?php

namespace Laraditz\TngEwallet\Http\Controllers;

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Facades\Tng;
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

        $backUrl = $payment->customer_return_url ?? config('tng-ewallet.default_return_url');

        try {
            $inquiry = Tng::payment()->inquiry(['paymentRequestId' => $payment->payment_request_id]);
        } catch (\Throwable) {
            $inquiry = null;
        }

        if (! $inquiry || ! $inquiry->isSuccessful()) {
            return response()->view('tng-ewallet::return', [
                'state' => 'inquiry_failed',
                'backUrl' => $backUrl,
            ]);
        }

        return response()->view('tng-ewallet::return', [
            'state' => 'status',
            'backUrl' => $backUrl,
            'paymentStatus' => $inquiry->paymentStatus,
            'paymentAmount' => $inquiry->paymentAmount,
            'paymentRequestId' => $inquiry->paymentRequestId,
            'paymentTime' => $inquiry->paymentTime,
            'paymentFailReason' => $inquiry->paymentFailReason,
        ]);
    }
}
