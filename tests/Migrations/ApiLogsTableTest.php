<?php

use Illuminate\Support\Facades\Schema;

test('tng_ewallet_api_logs table exists with the documented columns', function () {
    expect(Schema::hasTable('tng_ewallet_api_logs'))->toBeTrue();

    expect(Schema::hasColumns('tng_ewallet_api_logs', [
        'id', 'endpoint', 'reference_id',
        'request_payload', 'response_payload', 'http_status',
        'result_status', 'result_code', 'result_message',
        'duration_ms', 'created_at', 'updated_at',
    ]))->toBeTrue();
});
