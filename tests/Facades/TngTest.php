<?php

use Laraditz\TngEwallet\Facades\Tng;
use Laraditz\TngEwallet\Services\PaymentService;

test('Tng::payment() resolves through the facade to the same PaymentService type as the manager', function () {
    expect(Tng::payment())->toBeInstanceOf(PaymentService::class);
});

test('the facade resolves to the same underlying manager singleton as app(tng-ewallet)', function () {
    expect(Tng::getFacadeRoot())->toBe(app('tng-ewallet'));
});
