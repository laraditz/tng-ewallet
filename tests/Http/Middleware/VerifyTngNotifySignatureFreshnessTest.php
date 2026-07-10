<?php

use Illuminate\Http\Request;
use Laraditz\TngEwallet\Http\Middleware\VerifyTngNotifySignature;

function buildSignedNotifyRequest(string $privateKeyPem, string $uri, string $clientId, string $requestTime, string $body): Request
{
    $content = "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    openssl_sign($content, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    $request = Request::create($uri, 'POST', [], [], [], [], $body);
    $request->headers->set('Client-Id', $clientId);
    $request->headers->set('Request-Time', $requestTime);
    $request->headers->set('Signature', "algorithm=RSA256, keyVersion=1, signature={$signature}");

    return $request;
}

test('a validly signed request with a stale Request-Time (older than the tolerance window) is rejected with 401', function () {
    [$privateKeyPem] = generateNotifyKeypairAndConfigure();

    $staleTime = now()->subMinutes(30)->format('Y-m-d\TH:i:s.vP');
    $body = json_encode(['paymentId' => 'pay-1']);
    $request = buildSignedNotifyRequest($privateKeyPem, '/tng-ewallet/notify', 'TEST_CLIENT', $staleTime, $body);

    $middleware = new VerifyTngNotifySignature();
    $response = $middleware->handle($request, fn ($req) => response('should-not-reach-here'));

    expect($response->getStatusCode())->toBe(401);
});

test('a validly signed request within the tolerance window passes through', function () {
    [$privateKeyPem] = generateNotifyKeypairAndConfigure();

    $freshTime = now()->format('Y-m-d\TH:i:s.vP');
    $body = json_encode(['paymentId' => 'pay-1']);
    $request = buildSignedNotifyRequest($privateKeyPem, '/tng-ewallet/notify', 'TEST_CLIENT', $freshTime, $body);

    $middleware = new VerifyTngNotifySignature();
    $response = $middleware->handle($request, fn ($req) => response('passed-through'));

    expect($response->getContent())->toBe('passed-through');
});
