<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Payment;
use Laraditz\TngEwallet\Services\PaymentService;

dataset('result status to payment status mapping', [
    ['A', 'accepted'],
    ['S', 'success'],
    ['F', 'failed'],
    ['U', 'unknown'],
]);

test('pay() maps each resultStatus to the correct Payment.status enum', function (string $resultStatus, string $expectedStatus) {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => $resultStatus, 'resultCode' => 'X', 'resultMessage' => 'x'],
        'paymentId' => 'pay-status-'.$resultStatus,
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-status-'.$resultStatus]);

    expect(Payment::where('payment_request_id', 'pr-status-'.$resultStatus)->first()->status->value)
        ->toBe($expectedStatus);
})->with('result status to payment status mapping');
