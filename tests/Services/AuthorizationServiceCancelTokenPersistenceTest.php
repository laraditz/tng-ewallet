<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Enums\AccessTokenStatus;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Services\AuthorizationService;

test('cancelToken() marks the matching AccessToken row as cancelled', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $token = AccessToken::create([
        'access_token' => 'tok_to_cancel',
        'access_token_hash' => AccessToken::hashToken('tok_to_cancel'),
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => AccessTokenStatus::Active->value,
    ]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    (new AuthorizationService(new TngClient()))->cancelToken(['accessToken' => 'tok_to_cancel']);

    expect($token->fresh())
        ->status->toBe(AccessTokenStatus::Cancelled)
        ->cancelled_at->not->toBeNull();
});
