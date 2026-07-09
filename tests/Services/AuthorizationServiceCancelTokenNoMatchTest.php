<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Models\ApiLog;
use Laraditz\TngEwallet\Responses\CancelTokenResponse;
use Laraditz\TngEwallet\Services\AuthorizationService;

test('cancelToken() with no matching local AccessToken row still completes and logs the API call', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    expect(AccessToken::count())->toBe(0);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    $response = (new AuthorizationService(new TngClient()))->cancelToken(['accessToken' => 'tok_unknown']);

    expect($response)->toBeInstanceOf(CancelTokenResponse::class)
        ->and($response->isSuccessful())->toBeTrue()
        ->and(AccessToken::count())->toBe(0)
        ->and(ApiLog::count())->toBe(1);
});
