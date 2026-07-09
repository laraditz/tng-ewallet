<?php

use Laraditz\TngEwallet\Responses\PayResponse;
use Laraditz\TngEwallet\Responses\ValueObjects\ActionForm;

test('exposes paymentId, paymentTime, actionForm, authExpiryTime', function () {
    $response = new PayResponse([
        'result' => ['resultStatus' => 'A', 'resultCode' => 'ACCEPT', 'resultMessage' => 'accept'],
        'paymentId' => 'pay-1',
        'paymentTime' => '2021-07-27T21:12:12+08:00',
        'authExpiryTime' => '2021-07-28T21:12:12+08:00',
        'actionForm' => [
            'actionFormType' => 'REDIRECTION',
            'redirectionUrl' => 'https://m-sd.tngdigital.com.my/s/cashier/index.html?bizNo=1',
        ],
    ]);

    expect($response->paymentId)->toBe('pay-1')
        ->and($response->paymentTime)->toBe('2021-07-27T21:12:12+08:00')
        ->and($response->authExpiryTime)->toBe('2021-07-28T21:12:12+08:00')
        ->and($response->actionForm)->toBeInstanceOf(ActionForm::class)
        ->and($response->actionForm->redirectionUrl)->toBe('https://m-sd.tngdigital.com.my/s/cashier/index.html?bizNo=1')
        ->and($response->isAccepted())->toBeTrue();
});

test('actionForm is null when absent from the response', function () {
    $response = new PayResponse([
        'result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success'],
        'paymentId' => 'pay-2',
    ]);

    expect($response->actionForm)->toBeNull();
});
