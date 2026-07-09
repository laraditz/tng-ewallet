<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Payment;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() creates a Payment row keyed on paymentRequestId with paymentId, status, and raw response', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-persist-1',
        'actionForm' => ['actionFormType' => 'REDIRECTION', 'redirectionUrl' => 'https://m-sd.tngdigital.com.my/s/cashier/1'],
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-persist-1']);

    expect(Payment::count())->toBe(1);

    $payment = Payment::first();
    expect($payment->payment_request_id)->toBe('pr-persist-1')
        ->and($payment->payment_id)->toBe('pay-persist-1')
        ->and($payment->status->value)->toBe('accepted')
        ->and($payment->raw_pay_response)->not->toBeNull();
});
