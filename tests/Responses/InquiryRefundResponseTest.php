<?php

use Laraditz\TngEwallet\Responses\InquiryRefundResponse;

test('exposes refundId, refundRequestId, refundAmount, refundReason, refundTime, refundStatus, refundFailReason', function () {
    $response = new InquiryRefundResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'refundId' => 'refund-1',
        'refundRequestId' => 'rr-1',
        'refundAmount' => ['currency' => 'MYR', 'value' => '100'],
        'refundReason' => 'refund reason',
        'refundTime' => '2020-01-02T12:01:01+08:00',
        'refundStatus' => 'SUCCESS',
        'refundFailReason' => null,
    ]);

    expect($response->refundId)->toBe('refund-1')
        ->and($response->refundRequestId)->toBe('rr-1')
        ->and($response->refundAmount)->toBe(['currency' => 'MYR', 'value' => '100'])
        ->and($response->refundReason)->toBe('refund reason')
        ->and($response->refundTime)->toBe('2020-01-02T12:01:01+08:00')
        ->and($response->refundStatus)->toBe('SUCCESS')
        ->and($response->refundFailReason)->toBeNull();
});
