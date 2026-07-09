<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\PaymentService;

test('the vendor doc Cashier Payment sample response is the isAccepted() golden path', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    // Verbatim from vendor doc "9. API - Pay.md" Response sample.
    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => [
            'resultCode' => 'ACCEPT',
            'resultMessage' => 'accept',
            'resultStatus' => 'A',
        ],
        'paymentId' => '20210727211212800110171824907593592',
        'actionForm' => [
            'actionFormType' => 'REDIRECTION',
            'redirectionUrl' => 'https://m-sd.tngdigital.com.my/s/cashier/index.html?bizNo=33333333&timestamp=1612492156219&merchantId=5555555&sign=somesignature',
        ],
    ]), 200)]);

    $response = (new PaymentService(new TngClient()))->pay(['paymentRequestId' => 'pr-golden']);

    expect($response->isAccepted())->toBeTrue()
        ->and($response->isSuccessful())->toBeFalse()
        ->and($response->actionForm->redirectionUrl)->toContain('m-sd.tngdigital.com.my/s/cashier');
});
