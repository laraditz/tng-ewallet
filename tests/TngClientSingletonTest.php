<?php

use Laraditz\TngEwallet\Client\TngClient;

test('the container resolves TngClient as the same singleton instance', function () {
    $first = app(TngClient::class);
    $second = app(TngClient::class);

    expect($first)->toBeInstanceOf(TngClient::class)
        ->and($first)->toBe($second);
});
