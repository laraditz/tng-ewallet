<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\InquiryPaymentResponse;
use Laraditz\TngEwallet\Services\PaymentService;

function fakeInquiryPaymentResponse(): void
{
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-1',
        'paymentRequestId' => 'pr-1',
        'paymentStatus' => 'SUCCESS',
    ]), 200)]);
}

test('inquiry() with only paymentId sends the correct payload', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakeInquiryPaymentResponse();

    $response = (new PaymentService(new TngClient()))->inquiry(['paymentId' => 'pay-1']);

    expect($response)->toBeInstanceOf(InquiryPaymentResponse::class);

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/acl/api/v1/payments/inquiryPayment'
        && $request['paymentId'] === 'pay-1');
});

test('inquiry() with only paymentRequestId sends the correct payload', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakeInquiryPaymentResponse();

    (new PaymentService(new TngClient()))->inquiry(['paymentRequestId' => 'pr-1']);

    Http::assertSent(fn ($request) => $request['paymentRequestId'] === 'pr-1');
});
