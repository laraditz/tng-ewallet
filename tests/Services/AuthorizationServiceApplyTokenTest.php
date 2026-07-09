<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\ApplyTokenResponse;
use Laraditz\TngEwallet\Services\AuthorizationService;

function fakeApplyTokenResponse(): void
{
    Http::fake([
        'https://example.test/*' => Http::response(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'accessToken' => 'tok_abc',
            'accessTokenExpiryTime' => '2021-08-01T13:04:59+08:00',
            'refreshToken' => 'refresh_abc',
            'refreshTokenExpiryTime' => '2021-08-01T13:04:59+08:00',
            'customerId' => 'cust-1',
        ]), 200),
    ]);
}

test('applyToken() with AUTHORIZATION_CODE grant posts to /v1/authorizations/applyToken', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakeApplyTokenResponse();

    $service = new AuthorizationService(new TngClient());
    $response = $service->applyToken(['grantType' => 'AUTHORIZATION_CODE', 'authCode' => 'code-1']);

    expect($response)->toBeInstanceOf(ApplyTokenResponse::class)
        ->and($response->accessToken)->toBe('tok_abc');

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/v1/authorizations/applyToken'
        && $request['grantType'] === 'AUTHORIZATION_CODE');
});

test('applyToken() with REFRESH_TOKEN grant posts to /v1/authorizations/applyToken', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakeApplyTokenResponse();

    $service = new AuthorizationService(new TngClient());
    $response = $service->applyToken(['grantType' => 'REFRESH_TOKEN', 'refreshToken' => 'refresh-old']);

    expect($response)->toBeInstanceOf(ApplyTokenResponse::class);

    Http::assertSent(fn ($request) => $request['grantType'] === 'REFRESH_TOKEN'
        && $request['refreshToken'] === 'refresh-old');
});
