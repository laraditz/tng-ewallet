<?php

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Http\Middleware\VerifyTngNotifySignature;

dataset('missing headers', ['Client-Id', 'Request-Time', 'Signature']);

test('a request missing any required header returns 401, not a 500', function (string $missingHeader) {
    generateNotifyKeypairAndConfigure();

    $body = json_encode(['paymentId' => 'pay-1']);
    $request = Request::create('/tng-ewallet/notify', 'POST', [], [], [], [], $body);

    $headers = [
        'Client-Id' => 'TEST_CLIENT',
        'Request-Time' => '2019-05-28T12:12:14.000+08:00',
        'Signature' => 'algorithm=RSA256, keyVersion=1, signature=abc',
    ];
    unset($headers[$missingHeader]);

    foreach ($headers as $name => $value) {
        $request->headers->set($name, $value);
    }

    $middleware = new VerifyTngNotifySignature();
    $response = $middleware->handle($request, fn ($req) => response('should-not-reach-here'));

    expect($response->getStatusCode())->toBe(401);
})->with('missing headers');
