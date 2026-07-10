<?php

use Laraditz\TngEwallet\Responses\ApplyTokenResponse;

test('exposes accessToken, expiry times, refreshToken, and customerId', function () {
    $response = new ApplyTokenResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'accessToken' => 'tok_abc',
        'accessTokenExpiryTime' => '2021-08-01T13:04:59+08:00',
        'refreshToken' => 'refresh_abc',
        'refreshTokenExpiryTime' => '2021-08-01T13:04:59+08:00',
        'customerId' => 'cust-1',
    ]);

    expect($response->accessToken)->toBe('tok_abc')
        ->and($response->accessTokenExpiryTime)->toBe('2021-08-01T13:04:59+08:00')
        ->and($response->refreshToken)->toBe('refresh_abc')
        ->and($response->refreshTokenExpiryTime)->toBe('2021-08-01T13:04:59+08:00')
        ->and($response->customerId)->toBe('cust-1');
});
