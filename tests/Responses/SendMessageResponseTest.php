<?php

use Laraditz\TngEwallet\Responses\SendMessageResponse;

dataset('result statuses', ['S', 'F', 'U']);

test('exposes only base status helpers', function (string $resultStatus) {
    $response = new SendMessageResponse([
        'result' => ['resultStatus' => $resultStatus, 'resultCode' => 'X', 'resultMessage' => 'x'],
    ]);

    expect($response->resultStatus)->toBe($resultStatus);
})->with('result statuses');
