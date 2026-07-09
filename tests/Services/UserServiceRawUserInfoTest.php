<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\TngUser;
use Laraditz\TngEwallet\Services\UserService;

test('user_info matches the raw response payload byte-for-byte after decode, including undocumented fields', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $rawUserInfo = [
        'userId' => 'user-raw-1',
        'nickName' => 'Someone',
        'avatarUrl' => 'https://example.com/avatar.png',
    ];

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'userInfo' => $rawUserInfo,
    ]), 200)]);

    (new UserService(new TngClient()))->inquiryByAccessToken(['accessToken' => 'tok_raw']);

    expect(TngUser::first()->user_info)->toBe($rawUserInfo);
});
