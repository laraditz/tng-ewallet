<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Refund;
use Laraditz\TngEwallet\Services\RefundService;

test('inquiry() updates the matching Refund row status, time, and fail reason', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    $refund = Refund::create([
        'refund_request_id' => 'rr-inquiry-1',
        'refund_status' => 'PROCESSING',
    ]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'refundId' => 'refund-1',
        'refundRequestId' => 'rr-inquiry-1',
        'refundStatus' => 'SUCCESS',
        'refundTime' => '2020-01-02T12:01:01+08:00',
        'refundFailReason' => null,
    ]), 200)]);

    (new RefundService(new TngClient()))->inquiry(['refundRequestId' => 'rr-inquiry-1']);

    expect($refund->fresh())
        ->refund_status->toBe(\Laraditz\TngEwallet\Enums\RefundStatus::Success)
        ->refund_time->not->toBeNull()
        ->refund_fail_reason->toBeNull();
});
