<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() defaults paymentNotifyUrl to the package\'s own webhook route when not supplied', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-1',
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-1']);

    Http::assertSent(fn ($request) => $request['paymentNotifyUrl'] === route('tng-ewallet.notify'));
});

test('an explicitly supplied paymentNotifyUrl always wins over the package default', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-1',
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay([
        'paymentRequestId' => 'pr-1',
        'paymentNotifyUrl' => 'https://consumer-app.test/their-own-webhook',
    ]);

    Http::assertSent(fn ($request) => $request['paymentNotifyUrl'] === 'https://consumer-app.test/their-own-webhook');
});
