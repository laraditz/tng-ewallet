<?php

use Laraditz\TngEwallet\Exceptions\SigningException;
use Laraditz\TngEwallet\Exceptions\TngException;

test('signing exception extends tng exception', function () {
    expect(new SigningException('invalid PEM'))->toBeInstanceOf(TngException::class);
});
