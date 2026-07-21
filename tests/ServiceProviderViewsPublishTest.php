<?php

use Laraditz\TngEwallet\TngEwalletServiceProvider;

test('the return view is registered under the tng-ewallet-views publish tag', function () {
    $paths = TngEwalletServiceProvider::pathsToPublish(TngEwalletServiceProvider::class, 'tng-ewallet-views');

    expect($paths)->not->toBeEmpty();
});

test('the return view is resolvable under the tng-ewallet namespace', function () {
    expect(view()->exists('tng-ewallet::return'))->toBeTrue();
});
