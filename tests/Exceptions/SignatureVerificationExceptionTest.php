<?php

use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;
use Laraditz\TngEwallet\Exceptions\TngException;

test('signature verification exception extends tng exception', function () {
    expect(new SignatureVerificationException('invalid signature'))->toBeInstanceOf(TngException::class);
});
