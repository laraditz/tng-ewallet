<?php

use Laraditz\TngEwallet\Responses\CancelTokenResponse;

test('exposes only base status helpers', function () {
    $response = new CancelTokenResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]);

    expect($response->isSuccessful())->toBeTrue();
});
