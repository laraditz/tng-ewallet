<?php

use Laraditz\TngEwallet\TngEwallet;

test('app(tng-ewallet) resolves to a TngEwallet instance', function () {
    expect(app('tng-ewallet'))->toBeInstanceOf(TngEwallet::class);
});
