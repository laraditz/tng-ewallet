<?php

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Http\Middleware\VerifyTngNotifySignature;

test('a missing public key file returns 401, not an uncaught 500', function () {
    config(['tng-ewallet.public_key_path' => '/tmp/does-not-exist-'.uniqid().'.pem']);

    $body = json_encode(['paymentId' => 'pay-1']);
    $request = Request::create('/tng-ewallet/notify', 'POST', [], [], [], [], $body);
    $request->headers->set('Client-Id', 'TEST_CLIENT');
    $request->headers->set('Request-Time', now()->format('Y-m-d\TH:i:s.vP'));
    $request->headers->set('Signature', 'algorithm=RSA256, keyVersion=1, signature=abc');

    $middleware = new VerifyTngNotifySignature();
    $response = $middleware->handle($request, fn ($req) => response('should-not-reach-here'));

    expect($response->getStatusCode())->toBe(401);
});
