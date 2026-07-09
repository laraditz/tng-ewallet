<?php

use Illuminate\Support\Facades\DB;
use Laraditz\TngEwallet\Models\ApiLog;

test('request and response payloads are encrypted at rest, not stored as plaintext JSON', function () {
    $log = ApiLog::create([
        'endpoint' => '/v1/authorizations/applyToken',
        'request_payload' => ['authCode' => 'code-1'],
        'response_payload' => ['accessToken' => 'tok_secret_value'],
    ]);

    $rawRow = DB::table('tng_ewallet_api_logs')->find($log->id);

    expect($rawRow->request_payload)->not->toContain('code-1')
        ->and($rawRow->response_payload)->not->toContain('tok_secret_value');

    // But the Eloquent-cast accessor still decrypts transparently.
    expect($log->fresh()->request_payload)->toBe(['authCode' => 'code-1'])
        ->and($log->fresh()->response_payload)->toBe(['accessToken' => 'tok_secret_value']);
});
