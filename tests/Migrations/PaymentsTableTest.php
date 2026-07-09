<?php

use Illuminate\Support\Facades\Schema;

test('tng_ewallet_payments table exists with the documented columns', function () {
    expect(Schema::hasTable('tng_ewallet_payments'))->toBeTrue();

    expect(Schema::hasColumns('tng_ewallet_payments', [
        'id', 'payment_id', 'payment_request_id',
        'status', 'result_status', 'result_code', 'payment_fail_reason',
        'currency', 'amount', 'action_form_type', 'redirection_url',
        'payment_time', 'auth_expiry_time', 'notified_at',
        'raw_pay_response', 'raw_notify_payload',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});
