<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() with no envInfo sends the default terminalType', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-env-1']);

    Http::assertSent(fn ($request) => $request['envInfo'] === ['terminalType' => 'MINI_APP']);
});

test('pay() with an overriding terminalType sends only the caller value, no default leftover', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay([
        'paymentRequestId' => 'pr-env-2',
        'envInfo' => ['terminalType' => 'APP'],
    ]);

    Http::assertSent(fn ($request) => $request['envInfo'] === ['terminalType' => 'APP']);
});

test('pay() with an extra envInfo key merges it alongside the default terminalType', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);

    (new PaymentService(new TngClient()))->pay([
        'paymentRequestId' => 'pr-env-3',
        'envInfo' => ['osType' => 'iOS'],
    ]);

    Http::assertSent(fn ($request) => $request['envInfo'] === ['terminalType' => 'MINI_APP', 'osType' => 'iOS']);
});
