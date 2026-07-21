<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Payment;
use Laraditz\TngEwallet\Services\PaymentService;

function fakePayAccepted(): void
{
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-1',
    ]), 200)]);
}

test('customerReturnUrl is persisted on the Payment record and never sent to TNG', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakePayAccepted();

    (new PaymentService(new TngClient()))->pay([
        'paymentRequestId' => 'pr-1',
        'customerReturnUrl' => 'https://host-app.test/checkout/thanks',
    ]);

    Http::assertSent(fn ($request) => ! array_key_exists('customerReturnUrl', $request->data()));

    expect(Payment::first()->customer_return_url)->toBe('https://host-app.test/checkout/thanks');
});

test('customer_return_url is null when customerReturnUrl is not supplied', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakePayAccepted();

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-2']);

    expect(Payment::first()->customer_return_url)->toBeNull();
});
