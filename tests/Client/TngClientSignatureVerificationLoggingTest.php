<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;
use Laraditz\TngEwallet\Models\ApiLog;

test('a response that fails signature verification is logged as unverified, not as genuine data', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => true]);

    $responseBody = json_encode(['result' => ['resultStatus' => 'S', 'resultCode' => 'FORGED', 'resultMessage' => 'attacker-controlled']]);

    Http::fake([
        'https://example.test/*' => Http::response($responseBody, 200, [
            'Client-Id' => 'TEST_CLIENT',
            'Response-Time' => '2019-05-28T12:12:14.000+08:00',
            'Signature' => 'algorithm=RSA256, keyVersion=1, signature=not-a-real-signature',
        ]),
    ]);

    try {
        (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-forged']);
    } catch (SignatureVerificationException) {
        // expected
    }

    expect(ApiLog::count())->toBe(1);

    $log = ApiLog::first();
    expect($log->signature_verified)->toBeFalse()
        ->and($log->result_status)->toBeNull()
        ->and($log->response_payload)->toBeNull();
});

test('a successfully verified response is logged with signature_verified true', function () {
    [$privateKeyPem] = generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => true]);

    $responseBody = json_encode(['result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success']]);
    $contentToBeValidated = "POST /v1/payments/pay\nTEST_CLIENT.2019-05-28T12:12:14.000+08:00.{$responseBody}";
    openssl_sign($contentToBeValidated, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    Http::fake([
        'https://example.test/*' => Http::response($responseBody, 200, [
            'Client-Id' => 'TEST_CLIENT',
            'Response-Time' => '2019-05-28T12:12:14.000+08:00',
            'Signature' => "algorithm=RSA256, keyVersion=1, signature={$signature}",
        ]),
    ]);

    (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-genuine']);

    $log = ApiLog::first();
    expect($log->signature_verified)->toBeTrue()
        ->and($log->result_status->value)->toBe('S');
});
