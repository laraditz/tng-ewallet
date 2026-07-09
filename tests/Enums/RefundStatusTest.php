<?php

use Laraditz\TngEwallet\Enums\RefundStatus;

test('refund status enum matches TNG vendor doc values verbatim', function () {
    expect(RefundStatus::Processing->value)->toBe('PROCESSING')
        ->and(RefundStatus::Success->value)->toBe('SUCCESS')
        ->and(RefundStatus::Fail->value)->toBe('FAIL');
});
