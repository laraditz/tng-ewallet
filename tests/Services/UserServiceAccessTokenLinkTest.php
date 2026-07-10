<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Models\TngUser;
use Laraditz\TngEwallet\Services\UserService;

test('inquiryByAccessToken() links access_token_id to the matching AccessToken row when resolvable', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $token = AccessToken::create([
        'access_token' => 'tok_known',
        'access_token_hash' => AccessToken::hashToken('tok_known'),
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
    ]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'userInfo' => ['userId' => 'user-linked'],
    ]), 200)]);

    (new UserService(new TngClient()))->inquiryByAccessToken(['accessToken' => 'tok_known']);

    expect(TngUser::first()->access_token_id)->toBe($token->id);
});

test('inquiryByAccessToken() with an unresolvable accessToken still succeeds with access_token_id null', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'userInfo' => ['userId' => 'user-unlinked'],
    ]), 200)]);

    (new UserService(new TngClient()))->inquiryByAccessToken(['accessToken' => 'tok_unknown']);

    expect(TngUser::first()->access_token_id)->toBeNull();
});
