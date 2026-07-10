<?php

use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;
use Laraditz\TngEwallet\Models\Notification;
use Laraditz\TngEwallet\Models\Payment;

test('handle() with no matching Payment row still persists the notification without throwing', function () {
    expect(Payment::count())->toBe(0);

    $payload = [
        'paymentResult' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-unknown',
        'paymentRequestId' => 'pr-unknown',
    ];

    (new ProcessPaymentNotification($payload))->handle();

    expect(Notification::count())->toBe(1)
        ->and(Payment::count())->toBe(0);
});
