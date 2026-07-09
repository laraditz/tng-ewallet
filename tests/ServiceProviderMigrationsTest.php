<?php

use Illuminate\Support\Facades\Schema;

test('all 6 package tables exist after the provider boots, with no vendor:publish step run', function () {
    expect(Schema::hasTable('tng_ewallet_payments'))->toBeTrue()
        ->and(Schema::hasTable('tng_ewallet_api_logs'))->toBeTrue()
        ->and(Schema::hasTable('tng_ewallet_access_tokens'))->toBeTrue()
        ->and(Schema::hasTable('tng_ewallet_notifications'))->toBeTrue()
        ->and(Schema::hasTable('tng_ewallet_refunds'))->toBeTrue()
        ->and(Schema::hasTable('tng_ewallet_users'))->toBeTrue();
});
