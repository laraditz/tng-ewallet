<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Services\AuthorizationService;

test('applyToken() creates a new AccessToken row with the response data', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake([
        'https://example.test/*' => Http::response(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'accessToken' => 'tok_persist_1',
            'accessTokenExpiryTime' => '2021-08-01T13:04:59+08:00',
            'refreshToken' => 'refresh_persist_1',
            'refreshTokenExpiryTime' => '2021-08-01T13:04:59+08:00',
            'customerId' => 'cust-1',
        ]), 200),
    ]);

    $service = new AuthorizationService(new TngClient());
    $service->applyToken(['grantType' => 'AUTHORIZATION_CODE', 'authCode' => 'code-1']);

    expect(AccessToken::count())->toBe(1);

    $token = AccessToken::first();
    expect($token->access_token)->toBe('tok_persist_1')
        ->and($token->refresh_token)->toBe('refresh_persist_1')
        ->and($token->customer_id)->toBe('cust-1')
        ->and($token->grant_type)->toBe('AUTHORIZATION_CODE')
        ->and($token->status->value)->toBe('active');
});
