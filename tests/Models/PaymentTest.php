<?php

use Laraditz\TngEwallet\Enums\PaymentStatus;
use Laraditz\TngEwallet\Enums\ResultStatus;
use Laraditz\TngEwallet\Models\Payment;

test('a payment row can be created and casts status/result_status to enums', function () {
    $payment = Payment::create([
        'payment_request_id' => 'pr-1',
        'status' => PaymentStatus::Accepted->value,
        'result_status' => ResultStatus::Accepted->value,
    ]);

    expect($payment->fresh())
        ->status->toBe(PaymentStatus::Accepted)
        ->result_status->toBe(ResultStatus::Accepted);
});

test('raw_pay_response and raw_notify_payload are cast to arrays', function () {
    $payment = Payment::create([
        'payment_request_id' => 'pr-2',
        'status' => PaymentStatus::Created->value,
        'raw_pay_response' => ['paymentId' => '123'],
        'raw_notify_payload' => ['paymentResult' => ['resultStatus' => 'S']],
    ]);

    expect($payment->fresh()->raw_pay_response)->toBe(['paymentId' => '123'])
        ->and($payment->fresh()->raw_notify_payload)->toBe(['paymentResult' => ['resultStatus' => 'S']]);
});
