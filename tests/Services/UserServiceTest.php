<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\UserInfoResponse;
use Laraditz\TngEwallet\Services\UserService;

test('inquiryByAccessToken() posts to /v1/customers/user/inquiryUserInfoByAccessToken and returns a UserInfoResponse', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'userInfo' => ['userId' => 'user-1'],
    ]), 200)]);

    $service = new UserService(new TngClient());
    $response = $service->inquiryByAccessToken(['accessToken' => 'tok_abc']);

    expect($response)->toBeInstanceOf(UserInfoResponse::class)
        ->and($response->userInfo)->toBe(['userId' => 'user-1']);

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/acl/api/v1/customers/user/inquiryUserInfoByAccessToken'
        && $request['accessToken'] === 'tok_abc');
});
