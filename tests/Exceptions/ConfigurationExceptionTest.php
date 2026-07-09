<?php

use Laraditz\TngEwallet\Exceptions\ConfigurationException;
use Laraditz\TngEwallet\Exceptions\TngException;

test('configuration exception extends tng exception', function () {
    expect(new ConfigurationException('missing client_id'))->toBeInstanceOf(TngException::class);
});
