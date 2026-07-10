<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\RefundService;

test('REFUND_AMOUNT_EXCEED on create() surfaces as a normal F DTO, not an exception', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'F', 'resultCode' => 'REFUND_AMOUNT_EXCEED', 'resultMessage' => 'The total refund amount has exceed the payment amount.'],
    ]), 200)]);

    $response = (new RefundService(new TngClient()))->create([
        'refundRequestId' => 'rr-exceed',
        'paymentId' => 'pay-1',
        'refundAmount' => ['currency' => 'MYR', 'value' => '99999'],
    ]);

    expect($response->isFailed())->toBeTrue()
        ->and($response->resultCode)->toBe('REFUND_AMOUNT_EXCEED');
});
