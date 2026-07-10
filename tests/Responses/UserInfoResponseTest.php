<?php

use Laraditz\TngEwallet\Responses\UserInfoResponse;

test('exposes userInfo', function () {
    $response = new UserInfoResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'userInfo' => ['userId' => 'user-1'],
    ]);

    expect($response->userInfo)->toBe(['userId' => 'user-1'])
        ->and($response->isSuccessful())->toBeTrue();
});
