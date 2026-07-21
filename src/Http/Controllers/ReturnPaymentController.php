<?php

namespace Laraditz\TngEwallet\Http\Controllers;

use Illuminate\Http\Request;

class ReturnPaymentController
{
    public function __invoke(Request $request)
    {
        return response('', 200);
    }
}
