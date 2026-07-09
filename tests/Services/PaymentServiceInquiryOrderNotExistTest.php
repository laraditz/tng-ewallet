<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\PaymentService;

test('inquiryPayment with ORDER_NOT_EXIST surfaces as a normal F DTO, not an exception', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'F', 'resultCode' => 'ORDER_NOT_EXIST', 'resultMessage' => 'Order does not exist.'],
    ]), 200)]);

    $response = (new PaymentService(new TngClient()))->inquiry(['paymentRequestId' => 'pr-missing']);

    expect($response->isFailed())->toBeTrue()
        ->and($response->resultCode)->toBe('ORDER_NOT_EXIST');
});
