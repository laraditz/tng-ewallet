<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Models\Refund;
use Laraditz\TngEwallet\Responses\RefundResponse;
use Laraditz\TngEwallet\Services\RefundService;

test('create() posts to /v1/payments/refund, returns a RefundResponse, and creates a Refund row', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'refundId' => 'refund-create-1',
        'refundTime' => '2021-07-27T13:34:40+08:00',
    ]), 200)]);

    $service = new RefundService(new TngClient());
    $response = $service->create(['refundRequestId' => 'rr-1', 'paymentId' => 'pay-1', 'refundAmount' => ['currency' => 'MYR', 'value' => '100']]);

    expect($response)->toBeInstanceOf(RefundResponse::class)
        ->and($response->refundId)->toBe('refund-create-1');

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/v1/payments/refund'
        && $request['refundRequestId'] === 'rr-1');

    expect(Refund::count())->toBe(1);
    $refund = Refund::first();
    expect($refund->refund_request_id)->toBe('rr-1')
        ->and($refund->refund_id)->toBe('refund-create-1')
        ->and($refund->payment_id)->toBe('pay-1');
});
