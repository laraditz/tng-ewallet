<?php

use Illuminate\Support\Facades\Event;
use Laraditz\TngEwallet\Events\PaymentNotified;
use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;

test('handle() fires PaymentNotified with the parsed payload', function () {
    Event::fake();

    $payload = [
        'paymentResult' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-event-1',
        'paymentRequestId' => 'pr-event-1',
    ];

    (new ProcessPaymentNotification($payload))->handle();

    Event::assertDispatched(PaymentNotified::class, fn ($event) => $event->payload === $payload);
});
