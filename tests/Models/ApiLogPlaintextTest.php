<?php

use Illuminate\Support\Facades\DB;
use Laraditz\TngEwallet\Models\ApiLog;

test('request and response payloads are stored as plain JSON, not encrypted', function () {
    $log = ApiLog::create([
        'endpoint' => '/v1/payments/pay',
        'request_payload' => ['paymentRequestId' => 'pr-1'],
        'response_payload' => ['resultStatus' => 'S'],
    ]);

    $rawRow = DB::table('tng_ewallet_api_logs')->find($log->id);

    expect($rawRow->request_payload)->toContain('pr-1')
        ->and($rawRow->response_payload)->toContain('S');

    expect($log->fresh()->request_payload)->toBe(['paymentRequestId' => 'pr-1'])
        ->and($log->fresh()->response_payload)->toBe(['resultStatus' => 'S']);
});
