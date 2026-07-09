<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\RefundService;

test('inquiryRefund with REFUND_NOT_EXIST surfaces as a normal F DTO, not an exception', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'F', 'resultCode' => 'REFUND_NOT_EXIST', 'resultMessage' => 'Refund is not exist.'],
    ]), 200)]);

    $response = (new RefundService(new TngClient()))->inquiry(['refundRequestId' => 'rr-missing']);

    expect($response->isFailed())->toBeTrue()
        ->and($response->resultCode)->toBe('REFUND_NOT_EXIST');
});
