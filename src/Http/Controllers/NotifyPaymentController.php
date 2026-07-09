<?php

namespace Laraditz\TngEwallet\Http\Controllers;

class NotifyPaymentController
{
    public function __invoke()
    {
        return response()->json([
            'result' => [
                'resultCode' => 'SUCCESS',
                'resultStatus' => 'S',
                'resultMessage' => 'success',
            ],
        ]);
    }
}
