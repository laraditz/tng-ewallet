<?php

use Illuminate\Support\Facades\Schema;

test('tng_ewallet_notifications table exists with the documented columns', function () {
    expect(Schema::hasTable('tng_ewallet_notifications'))->toBeTrue();

    expect(Schema::hasColumns('tng_ewallet_notifications', [
        'id', 'payment_id', 'payment_request_id', 'customer_id',
        'result_status', 'result_code', 'result_message',
        'payment_amount_currency', 'payment_amount_value', 'payment_time',
        'payment_fail_reason', 'extend_info',
        'signature_verified', 'raw_payload', 'ack_sent_at',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});
