<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\AuthorizationService;
use Laraditz\TngEwallet\Services\PaymentService;
use Laraditz\TngEwallet\Services\RefundService;

function fakeGenericSuccessResponse(): void
{
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
    ]), 200)]);
}

beforeEach(function () {
    generateAndConfigureRsaKeypairFixture();
    config([
        'tng-ewallet.verify_response_signature' => false,
        'tng-ewallet.partner_id' => 'PARTNER-FROM-CONFIG',
    ]);
});

test('prepare() defaults partnerId from config when not supplied', function () {
    fakeGenericSuccessResponse();

    (new AuthorizationService(new TngClient()))->prepare(['referenceClientId' => 'ref-1']);

    Http::assertSent(fn ($request) => $request['partnerId'] === 'PARTNER-FROM-CONFIG');
});

test('pay() defaults partnerId from config when not supplied', function () {
    fakeGenericSuccessResponse();

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-1']);

    Http::assertSent(fn ($request) => $request['partnerId'] === 'PARTNER-FROM-CONFIG');
});

test('payment inquiry() defaults partnerId from config when not supplied', function () {
    fakeGenericSuccessResponse();

    (new PaymentService(new TngClient()))->inquiry(['paymentRequestId' => 'pr-1']);

    Http::assertSent(fn ($request) => $request['partnerId'] === 'PARTNER-FROM-CONFIG');
});

test('refund create() defaults partnerId from config when not supplied', function () {
    fakeGenericSuccessResponse();

    (new RefundService(new TngClient()))->create(['refundRequestId' => 'rr-1']);

    Http::assertSent(fn ($request) => $request['partnerId'] === 'PARTNER-FROM-CONFIG');
});

test('refund inquiry() defaults partnerId from config when not supplied', function () {
    fakeGenericSuccessResponse();

    (new RefundService(new TngClient()))->inquiry(['refundRequestId' => 'rr-1']);

    Http::assertSent(fn ($request) => $request['partnerId'] === 'PARTNER-FROM-CONFIG');
});

test('an explicitly supplied partnerId always wins over config', function () {
    fakeGenericSuccessResponse();

    (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-1', 'partnerId' => 'CALLER-SUPPLIED']);

    Http::assertSent(fn ($request) => $request['partnerId'] === 'CALLER-SUPPLIED');
});
