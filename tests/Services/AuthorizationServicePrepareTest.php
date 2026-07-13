<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\PrepareResponse;
use Laraditz\TngEwallet\Services\AuthorizationService;

test('prepare() posts to /v1/authorizations/prepare and returns a PrepareResponse', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake([
        'https://example.test/*' => Http::response(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'authId' => 'auth-123',
            'authURL' => 'https://m-sd.tngdigital.com.my/s/auth/index.html?bizNo=1',
            'authClientId' => 'CLIENT-1',
        ]), 200),
    ]);

    $service = new AuthorizationService(new TngClient());
    $response = $service->prepare(['referenceClientId' => 'ref-1']);

    expect($response)->toBeInstanceOf(PrepareResponse::class)
        ->and($response->authId)->toBe('auth-123');

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/acl/api/v1/authorizations/prepare'
        && $request['referenceClientId'] === 'ref-1');
});
