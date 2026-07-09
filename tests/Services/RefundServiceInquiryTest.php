<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\InquiryRefundResponse;
use Laraditz\TngEwallet\Services\RefundService;

function fakeInquiryRefundResponse(): void
{
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'refundId' => 'refund-1',
        'refundRequestId' => 'rr-1',
        'refundStatus' => 'SUCCESS',
    ]), 200)]);
}

test('inquiry() with only refundId sends the correct payload', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakeInquiryRefundResponse();

    $response = (new RefundService(new TngClient()))->inquiry(['refundId' => 'refund-1']);

    expect($response)->toBeInstanceOf(InquiryRefundResponse::class);

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/v1/payments/inquiryRefund'
        && $request['refundId'] === 'refund-1');
});

test('inquiry() with only refundRequestId sends the correct payload', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);
    fakeInquiryRefundResponse();

    (new RefundService(new TngClient()))->inquiry(['refundRequestId' => 'rr-1']);

    Http::assertSent(fn ($request) => $request['refundRequestId'] === 'rr-1');
});
