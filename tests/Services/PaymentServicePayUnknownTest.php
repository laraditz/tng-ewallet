<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\PaymentService;

test('resultStatus U on pay() surfaces via isUnknown() with no follow-up call', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'U', 'resultCode' => 'UNKNOWN_EXCEPTION', 'resultMessage' => 'unknown'],
    ]), 200)]);

    $response = (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-unknown']);

    expect($response->isUnknown())->toBeTrue();

    Http::assertSentCount(1);
});
