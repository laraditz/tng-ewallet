<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Models\ApiLog;

test('accessToken, refreshToken, and authCode are redacted before being persisted', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $responseBody = json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'accessToken' => 'tok_secret_value',
        'refreshToken' => 'refresh_secret_value',
    ]);
    Http::fake(['https://example.test/*' => Http::response($responseBody, 200)]);

    (new TngClient())->post('/v1/authorizations/applyToken', [
        'grantType' => 'AUTHORIZATION_CODE',
        'authCode' => 'auth_code_secret',
    ]);

    $log = ApiLog::first();

    expect($log->request_payload['authCode'])
        ->toBe('[redacted:'.AccessToken::hashToken('auth_code_secret').']');

    expect($log->response_payload['accessToken'])
        ->toBe('[redacted:'.AccessToken::hashToken('tok_secret_value').']');

    expect($log->response_payload['refreshToken'])
        ->toBe('[redacted:'.AccessToken::hashToken('refresh_secret_value').']');
});

test('a call with no sensitive fields is persisted completely unredacted', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $responseBody = json_encode(['result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success']]);
    Http::fake(['https://example.test/*' => Http::response($responseBody, 200)]);

    (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-redaction-check']);

    $log = ApiLog::first();

    expect($log->request_payload)->toBe(['paymentRequestId' => 'pr-redaction-check']);
});
