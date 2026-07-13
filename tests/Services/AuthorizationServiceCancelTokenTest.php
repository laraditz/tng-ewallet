<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\CancelTokenResponse;
use Laraditz\TngEwallet\Services\AuthorizationService;

test('cancelToken() posts to /v1/authorizations/cancelToken and returns a CancelTokenResponse', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    $service = new AuthorizationService(new TngClient());
    $response = $service->cancelToken(['accessToken' => 'tok_to_cancel']);

    expect($response)->toBeInstanceOf(CancelTokenResponse::class)
        ->and($response->isSuccessful())->toBeTrue();

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/acl/api/v1/authorizations/cancelToken'
        && $request['accessToken'] === 'tok_to_cancel');
});
