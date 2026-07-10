<?php

use Laraditz\TngEwallet\Exceptions\ConfigurationException;
use Laraditz\TngEwallet\Models\AccessToken;

test('hashToken() throws ConfigurationException when encryption_key is unset', function () {
    config(['tng-ewallet.encryption_key' => null]);

    expect(fn () => AccessToken::hashToken('some-token'))
        ->toThrow(ConfigurationException::class);
});
