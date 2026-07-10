<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Exceptions\ApiException;
use Laraditz\TngEwallet\Models\ApiLog;

test('a connection-level failure is wrapped in ApiException, not left as a raw ConnectionException', function () {
    generateAndConfigureRsaKeypairFixture();

    Http::fake(function () {
        throw new ConnectionException('Connection timed out.');
    });

    expect(fn () => (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-conn-1']))
        ->toThrow(ApiException::class);

    // FR-20: the call is still logged even though it never got a response.
    $log = ApiLog::first();
    expect($log->http_status)->toBeNull();
});
