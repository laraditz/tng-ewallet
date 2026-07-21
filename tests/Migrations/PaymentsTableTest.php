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
        'customer_return_url',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});

test('redirection_url is a text column, wide enough for TNG cashier URLs', function () {
    expect(Schema::getColumnType('tng_ewallet_payments', 'redirection_url'))->toBe('text');
});

test('customer_return_url is a text column', function () {
    expect(Schema::getColumnType('tng_ewallet_payments', 'customer_return_url'))->toBe('text');
});
