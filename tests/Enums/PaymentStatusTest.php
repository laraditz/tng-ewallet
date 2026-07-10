<?php

use Laraditz\TngEwallet\Enums\PaymentStatus;

test('payment status enum has the correct backing values', function () {
    expect(PaymentStatus::Created->value)->toBe('created')
        ->and(PaymentStatus::Accepted->value)->toBe('accepted')
        ->and(PaymentStatus::Success->value)->toBe('success')
        ->and(PaymentStatus::Failed->value)->toBe('failed')
        ->and(PaymentStatus::Unknown->value)->toBe('unknown');
});
