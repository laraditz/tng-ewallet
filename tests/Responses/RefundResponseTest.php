<?php

use Laraditz\TngEwallet\Responses\RefundResponse;

test('exposes refundId and refundTime', function () {
    $response = new RefundResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'refundId' => 'refund-1',
        'refundTime' => '2021-07-27T13:34:40+08:00',
    ]);

    expect($response->refundId)->toBe('refund-1')
        ->and($response->refundTime)->toBe('2021-07-27T13:34:40+08:00')
        ->and($response->isSuccessful())->toBeTrue();
});
