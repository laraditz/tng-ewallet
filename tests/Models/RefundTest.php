<?php

use Laraditz\TngEwallet\Enums\RefundStatus;
use Laraditz\TngEwallet\Enums\ResultStatus;
use Laraditz\TngEwallet\Models\Refund;

test('a refund row can be created and casts refund_status/result_status to enums', function () {
    $refund = Refund::create([
        'refund_request_id' => 'rr-1',
        'refund_status' => RefundStatus::Processing->value,
        'result_status' => ResultStatus::Success->value,
    ]);

    expect($refund->fresh())
        ->refund_status->toBe(RefundStatus::Processing)
        ->result_status->toBe(ResultStatus::Success);
});
