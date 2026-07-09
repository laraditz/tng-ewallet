<?php

use Laraditz\TngEwallet\Responses\ValueObjects\ActionForm;

test('exposes actionFormType, orderCode, redirectionUrl', function () {
    $actionForm = new ActionForm([
        'actionFormType' => 'REDIRECTION',
        'orderCode' => 'ORDER-1',
        'redirectionUrl' => 'https://m-sd.tngdigital.com.my/s/cashier/index.html?bizNo=1',
    ]);

    expect($actionForm->actionFormType)->toBe('REDIRECTION')
        ->and($actionForm->orderCode)->toBe('ORDER-1')
        ->and($actionForm->redirectionUrl)->toBe('https://m-sd.tngdigital.com.my/s/cashier/index.html?bizNo=1');
});
