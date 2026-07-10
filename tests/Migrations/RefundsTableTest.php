<?php

use Illuminate\Support\Facades\Schema;

test('tng_ewallet_refunds table exists with the documented columns', function () {
    expect(Schema::hasTable('tng_ewallet_refunds'))->toBeTrue();

    expect(Schema::hasColumns('tng_ewallet_refunds', [
        'id', 'refund_id', 'refund_request_id', 'payment_id', 'payment_request_id',
        'refund_status', 'result_status', 'result_code',
        'refund_amount_currency', 'refund_amount_value',
        'refund_reason', 'refund_fail_reason', 'refund_time',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});
