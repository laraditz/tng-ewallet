<?php

use Laraditz\TngEwallet\Exceptions\TngException;
use Laraditz\TngEwallet\Exceptions\WebhookException;

test('webhook exception extends tng exception', function () {
    expect(new WebhookException('bad webhook payload'))->toBeInstanceOf(TngException::class);
});
