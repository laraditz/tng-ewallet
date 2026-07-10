<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laraditz\TngEwallet\Events\PaymentNotified;
use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;
use Laraditz\TngEwallet\Models\Notification;

test('a failure while updating the matching Payment row does not prevent the event from firing or the notification from being persisted', function () {
    Event::fake();

    // Force the Payment lookup/update step to blow up, simulating a transient
    // DB error, without touching the already-inserted Notification row.
    DB::listen(function ($query) {
        if (str_contains($query->sql, 'update "tng_ewallet_payments"')) {
            throw new \RuntimeException('simulated DB failure during payment update');
        }
    });

    $payload = [
        'paymentResult' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-resilience-1',
        'paymentRequestId' => 'pr-resilience-1',
    ];

    \Laraditz\TngEwallet\Models\Payment::create([
        'payment_id' => 'pay-resilience-1',
        'payment_request_id' => 'pr-resilience-1',
        'status' => 'accepted',
    ]);

    (new ProcessPaymentNotification($payload))->handle();

    expect(Notification::count())->toBe(1);
    Event::assertDispatched(PaymentNotified::class, fn ($event) => $event->payload === $payload);
});
