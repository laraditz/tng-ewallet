<?php

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Http\Middleware\VerifyTngNotifySignature;

test('an invalid notify signature returns HTTP 401 and never reaches the next handler', function () {
    generateNotifyKeypairAndConfigure();

    $body = json_encode(['paymentId' => 'pay-1']);

    $request = Request::create('/tng-ewallet/notify', 'POST', [], [], [], [], $body);
    $request->headers->set('Client-Id', 'TEST_CLIENT');
    $request->headers->set('Request-Time', now()->format('Y-m-d\TH:i:s.vP'));
    $request->headers->set('Signature', 'algorithm=RSA256, keyVersion=1, signature=not-a-real-signature');

    $nextCalled = false;
    $middleware = new VerifyTngNotifySignature();
    $response = $middleware->handle($request, function ($req) use (&$nextCalled) {
        $nextCalled = true;

        return response('should-not-reach-here');
    });

    expect($response->getStatusCode())->toBe(401)
        ->and($nextCalled)->toBeFalse();
});
