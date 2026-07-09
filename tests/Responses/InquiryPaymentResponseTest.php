<?php

use Laraditz\TngEwallet\Responses\InquiryPaymentResponse;

test('exposes paymentId, paymentRequestId, paymentAmount, paymentTime, paymentStatus, paymentFailReason, authExpiryTime', function () {
    $response = new InquiryPaymentResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-1',
        'paymentRequestId' => 'pr-1',
        'paymentAmount' => ['currency' => 'MYR', 'value' => '100'],
        'paymentTime' => '2021-07-27T21:12:12+08:00',
        'paymentStatus' => 'SUCCESS',
        'paymentFailReason' => null,
        'authExpiryTime' => '2021-07-28T21:12:12+08:00',
    ]);

    expect($response->paymentId)->toBe('pay-1')
        ->and($response->paymentRequestId)->toBe('pr-1')
        ->and($response->paymentAmount)->toBe(['currency' => 'MYR', 'value' => '100'])
        ->and($response->paymentTime)->toBe('2021-07-27T21:12:12+08:00')
        ->and($response->paymentStatus)->toBe('SUCCESS')
        ->and($response->paymentFailReason)->toBeNull()
        ->and($response->authExpiryTime)->toBe('2021-07-28T21:12:12+08:00');
});
