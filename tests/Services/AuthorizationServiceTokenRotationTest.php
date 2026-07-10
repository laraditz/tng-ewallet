<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Services\AuthorizationService;

test('an AUTHORIZATION_CODE call followed by a REFRESH_TOKEN call produces two rows, not an update', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $service = new AuthorizationService(new TngClient());

    Http::fake(['https://example.test/*' => Http::sequence()
        ->push(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'accessToken' => 'tok_first',
            'refreshToken' => 'refresh_first',
            'customerId' => 'cust-1',
        ]), 200)
        ->push(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'accessToken' => 'tok_second',
            'refreshToken' => 'refresh_second',
            'customerId' => 'cust-1',
        ]), 200),
    ]);

    $service->applyToken(['grantType' => 'AUTHORIZATION_CODE', 'authCode' => 'code-1']);
    $service->applyToken(['grantType' => 'REFRESH_TOKEN', 'refreshToken' => 'refresh_first']);

    expect(AccessToken::count())->toBe(2);
    expect(AccessToken::pluck('access_token')->all())->toBe(['tok_first', 'tok_second']);
});
