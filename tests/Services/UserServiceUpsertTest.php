<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\TngUser;
use Laraditz\TngEwallet\Services\UserService;

test('inquiryByAccessToken() upserts a TngUser row keyed on userId, not appends on repeat lookups', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $service = new UserService(new TngClient());

    Http::fake(['https://example.test/*' => Http::sequence()
        ->push(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'userInfo' => ['userId' => 'user-upsert-1'],
        ]), 200)
        ->push(json_encode([
            'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
            'userInfo' => ['userId' => 'user-upsert-1', 'extra' => 'field'],
        ]), 200),
    ]);

    $service->inquiryByAccessToken(['accessToken' => 'tok_abc']);

    expect(TngUser::count())->toBe(1);
    $firstFetchedAt = TngUser::first()->last_fetched_at;

    $service->inquiryByAccessToken(['accessToken' => 'tok_abc']);

    expect(TngUser::count())->toBe(1);
    expect(TngUser::first()->user_info)->toBe(['userId' => 'user-upsert-1', 'extra' => 'field']);
});
