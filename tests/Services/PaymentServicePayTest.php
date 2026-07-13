<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Responses\PayResponse;
use Laraditz\TngEwallet\Services\PaymentService;

test('pay() posts to /v1/payments/pay and returns a PayResponse for Cashier Payment', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-cashier-1',
        'actionForm' => ['actionFormType' => 'REDIRECTION', 'redirectionUrl' => 'https://m-sd.tngdigital.com.my/s/cashier/1'],
    ]), 200)]);

    $service = new PaymentService(new TngClient());
    $response = $service->pay(['paymentRequestId' => 'pr-1', 'paymentFactor' => ['isCashierPayment' => true]]);

    expect($response)->toBeInstanceOf(PayResponse::class)
        ->and($response->paymentId)->toBe('pay-cashier-1')
        ->and($response->actionForm->redirectionUrl)->toBe('https://m-sd.tngdigital.com.my/s/cashier/1');

    Http::assertSent(fn ($request) => $request->url() === 'https://example.test/acl/api/v1/payments/pay'
        && $request['paymentRequestId'] === 'pr-1');
});

test('pay() posts to /v1/payments/pay and returns a PayResponse for Agreement Payment', function () {
    generateAndConfigureRsaKeypairFixture();
    config(['tng-ewallet.verify_response_signature' => false]);

    Http::fake(['https://example.test/*' => Http::response(json_encode([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-agreement-1',
        'paymentTime' => '2021-07-27T21:12:12+08:00',
    ]), 200)]);

    $service = new PaymentService(new TngClient());
    $response = $service->pay(['paymentRequestId' => 'pr-2', 'paymentFactor' => ['isAgreementPay' => true], 'paymentAuthCode' => 'tok_abc']);

    expect($response)->toBeInstanceOf(PayResponse::class)
        ->and($response->isSuccessful())->toBeTrue();
});
