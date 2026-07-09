<?php

use Laraditz\TngEwallet\Enums\PaymentStatus;
use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;
use Laraditz\TngEwallet\Models\Payment;

test('handle() updates the matching Payment row status and notified_at', function () {
    $payment = Payment::create([
        'payment_id' => 'pay-notify-1',
        'payment_request_id' => 'pr-notify-1',
        'status' => PaymentStatus::Accepted->value,
    ]);

    $payload = [
        'paymentResult' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-notify-1',
        'paymentRequestId' => 'pr-notify-1',
    ];

    (new ProcessPaymentNotification($payload))->handle();

    expect($payment->fresh())
        ->status->toBe(PaymentStatus::Success)
        ->notified_at->not->toBeNull();
});
