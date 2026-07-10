<?php

use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;
use Laraditz\TngEwallet\Models\Notification;

test('running the same notification payload twice creates two separate rows, no dedup', function () {
    $payload = [
        'paymentResult' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-dup-1',
        'paymentRequestId' => 'pr-dup-1',
    ];

    (new ProcessPaymentNotification($payload))->handle();
    (new ProcessPaymentNotification($payload))->handle();

    expect(Notification::count())->toBe(2);
});
