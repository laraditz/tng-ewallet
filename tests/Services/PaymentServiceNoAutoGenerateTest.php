<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Payment;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() passes the caller-supplied paymentRequestId through untouched, never generating its own', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-1',
    ]), 200)]);

    $callerSuppliedId = 'caller-chosen-id-12345';
    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => $callerSuppliedId]);

    Http::assertSent(fn ($request) => $request['paymentRequestId'] === $callerSuppliedId);

    expect(Payment::first()->payment_request_id)->toBe($callerSuppliedId);
});
