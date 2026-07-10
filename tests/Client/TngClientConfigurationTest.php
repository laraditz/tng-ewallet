<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Exceptions\ConfigurationException;

dataset('missing config keys', ['client_id', 'partner_id', 'private_key_path']);

test('missing required config throws ConfigurationException before any http call', function (string $missingKey) {
    Http::fake();

    config([
        'tng-ewallet.client_id' => 'TEST_CLIENT',
        'tng-ewallet.partner_id' => 'PARTNER1',
        'tng-ewallet.private_key_path' => '/tmp/does-not-matter.pem',
        "tng-ewallet.{$missingKey}" => null,
    ]);

    expect(fn () => (new TngClient())->post('/v1/payments/pay', ['a' => 1]))
        ->toThrow(ConfigurationException::class);

    Http::assertNothingSent();
})->with('missing config keys');
