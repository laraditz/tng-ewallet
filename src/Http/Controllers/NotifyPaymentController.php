<?php

namespace Laraditz\TngEwallet\Http\Controllers;

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;

class NotifyPaymentController
{
    public function __invoke(Request $request)
    {
        $response = response()->json([
            'result' => [
                'resultCode' => 'SUCCESS',
                'resultStatus' => 'S',
                'resultMessage' => 'success',
            ],
        ]);

        ProcessPaymentNotification::dispatch($request->json()->all())->afterResponse();

        return $response;
    }
}
