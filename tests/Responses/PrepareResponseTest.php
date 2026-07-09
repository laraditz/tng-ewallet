<?php

use Laraditz\TngEwallet\Responses\PrepareResponse;

test('exposes authId, authURL, authClientId', function () {
    $response = new PrepareResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'authId' => 'auth-123',
        'authURL' => 'https://m-sd.tngdigital.com.my/s/auth/index.html?bizNo=1',
        'authClientId' => 'CLIENT-1',
    ]);

    expect($response->authId)->toBe('auth-123')
        ->and($response->authURL)->toBe('https://m-sd.tngdigital.com.my/s/auth/index.html?bizNo=1')
        ->and($response->authClientId)->toBe('CLIENT-1')
        ->and($response->isSuccessful())->toBeTrue();
});
