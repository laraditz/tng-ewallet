<?php

use Laraditz\TngEwallet\Exceptions\TngException;

test('tng exception extends the base exception class', function () {
    expect(new TngException('boom'))->toBeInstanceOf(\Exception::class);
});
