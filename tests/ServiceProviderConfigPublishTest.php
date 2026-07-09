<?php

use Laraditz\TngEwallet\TngEwalletServiceProvider;

test('the config file is registered under the tng-ewallet-config publish tag', function () {
    $paths = TngEwalletServiceProvider::pathsToPublish(TngEwalletServiceProvider::class, 'tng-ewallet-config');

    expect($paths)->not->toBeEmpty();

    $sourceBasenames = array_map('basename', array_keys($paths));
    expect($sourceBasenames)->toContain('tng-ewallet.php');
});
