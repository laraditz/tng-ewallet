<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Payment;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() persists currency and amount from the request paymentAmount', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-amount-1',
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay([
        'paymentRequestId' => 'pr-amount-1',
        'paymentAmount' => ['currency' => 'MYR', 'value' => '12345'],
    ]);

    $payment = Payment::first();
    expect($payment->currency)->toBe('MYR')
        ->and($payment->amount)->toBe('12345');
});
