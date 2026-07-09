<?php

use Laraditz\TngEwallet\Exceptions\ApiException;
use Laraditz\TngEwallet\Exceptions\TngException;

test('api exception extends tng exception and carries raw response and status', function () {
    $exception = new ApiException('transport failure', response: ['error' => 'bad gateway'], statusCode: 502);

    expect($exception)->toBeInstanceOf(TngException::class)
        ->and($exception->getResponse())->toBe(['error' => 'bad gateway'])
        ->and($exception->getApiStatusCode())->toBe(502);
});

test('api exception allows a null response and status for connection-level failures', function () {
    $exception = new ApiException('connection timed out');

    expect($exception->getResponse())->toBeNull()
        ->and($exception->getApiStatusCode())->toBeNull();
});
