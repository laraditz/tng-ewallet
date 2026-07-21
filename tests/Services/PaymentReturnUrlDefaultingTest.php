<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() defaults paymentReturnUrl to the package\'s own return route, with paymentRequestId appended', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-1',
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-1']);

    Http::assertSent(fn ($request) => $request['paymentReturnUrl'] === route('tng-ewallet.return', ['payment_request_id' => 'pr-1']));
});

test('an explicitly supplied paymentReturnUrl always wins over the package default', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-1',
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay([
        'paymentRequestId' => 'pr-1',
        'paymentReturnUrl' => 'https://tng-sandbox.test/their-own-return',
    ]);

    Http::assertSent(fn ($request) => $request['paymentReturnUrl'] === 'https://tng-sandbox.test/their-own-return');
});
