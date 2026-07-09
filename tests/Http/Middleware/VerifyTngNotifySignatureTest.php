<?php

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Http\Middleware\VerifyTngNotifySignature;

function generateNotifyKeypairAndConfigure(): array
{
    $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($resource, $privateKeyPem);
    $publicKeyPem = openssl_pkey_get_details($resource)['key'];

    $publicKeyPath = tempnam(sys_get_temp_dir(), 'tng_notify_pub_');
    file_put_contents($publicKeyPath, $publicKeyPem);

    config(['tng-ewallet.public_key_path' => $publicKeyPath]);

    return [$privateKeyPem, $publicKeyPem];
}

test('a validly signed notify request passes through the middleware', function () {
    [$privateKeyPem] = generateNotifyKeypairAndConfigure();

    $uri = '/tng-ewallet/notify';
    $clientId = 'TEST_CLIENT';
    $requestTime = '2019-05-28T12:12:14.000+08:00';
    $body = json_encode(['paymentId' => 'pay-1', 'paymentRequestId' => 'pr-1']);

    $content = "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    openssl_sign($content, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    $request = Request::create($uri, 'POST', [], [], [], [], $body);
    $request->headers->set('Client-Id', $clientId);
    $request->headers->set('Request-Time', $requestTime);
    $request->headers->set('Signature', "algorithm=RSA256, keyVersion=1, signature={$signature}");

    $middleware = new VerifyTngNotifySignature();
    $response = $middleware->handle($request, fn ($req) => response('passed-through'));

    expect($response->getContent())->toBe('passed-through');
});
