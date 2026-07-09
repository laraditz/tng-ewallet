<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Exceptions\ApiException;
use Laraditz\TngEwallet\Models\ApiLog;

test('a successful call is persisted to tng_ewallet_api_logs', function () {
    [$privateKeyPem] = generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $responseBody = json_encode(['result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success']]);
    Http::fake(['https://example.test/*' => Http::response($responseBody, 200)]);

    (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-log-1']);

    expect(ApiLog::count())->toBe(1);

    $log = ApiLog::first();
    expect($log->endpoint)->toBe('/v1/payments/pay')
        ->and($log->http_status)->toBe(200)
        ->and($log->result_status->value)->toBe('S')
        ->and($log->request_payload)->toBe(['paymentRequestId' => 'pr-log-1']);
});

test('a failed call is also persisted to tng_ewallet_api_logs', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(['error' => 'bad gateway'], 502)]);

    try {
        (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-log-2']);
    } catch (ApiException) {
        // expected
    }

    expect(ApiLog::count())->toBe(1);

    $log = ApiLog::first();
    expect($log->http_status)->toBe(502);
});
