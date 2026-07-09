<?php

use Laraditz\TngEwallet\Enums\ResultStatus;
use Laraditz\TngEwallet\Models\Notification;

test('a notification row can be created with json and enum casts', function () {
    $notification = Notification::create([
        'payment_id' => 'pay-1',
        'payment_request_id' => 'pr-1',
        'result_status' => ResultStatus::Success->value,
        'signature_verified' => true,
        'raw_payload' => ['paymentResult' => ['resultStatus' => 'S']],
        'ack_sent_at' => now(),
    ]);

    expect($notification->fresh())
        ->result_status->toBe(ResultStatus::Success)
        ->raw_payload->toBe(['paymentResult' => ['resultStatus' => 'S']])
        ->signature_verified->toBeTrue();
});
