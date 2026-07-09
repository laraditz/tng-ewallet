<?php

use Laraditz\TngEwallet\Events\PaymentNotified;

test('exposes the payload passed to its constructor', function () {
    $payload = ['paymentId' => 'pay-1', 'paymentRequestId' => 'pr-1'];

    $event = new PaymentNotified($payload);

    expect($event->payload)->toBe($payload);
});
