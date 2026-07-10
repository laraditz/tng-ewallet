<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Refund;
use Laraditz\TngEwallet\Services\RefundService;

test('create() passes the caller-supplied refundRequestId through untouched, never generating its own', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'refundId' => 'refund-1',
    ]), 200)]);

    $callerSuppliedId = 'caller-chosen-refund-id-12345';
    (new RefundService(new TngClient()))->create(['refundRequestId' => $callerSuppliedId, 'paymentId' => 'pay-1']);

    Http::assertSent(fn ($request) => $request['refundRequestId'] === $callerSuppliedId);

    expect(Refund::first()->refund_request_id)->toBe($callerSuppliedId);
});
