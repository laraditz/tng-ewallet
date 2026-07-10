<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Exceptions\SigningException;

function configureClientWithPrivateKeyPath(?string $privateKeyPath): void
{
    config([
        'tng-ewallet.base_url' => 'https://example.test',
        'tng-ewallet.client_id' => 'TEST_CLIENT',
        'tng-ewallet.partner_id' => 'PARTNER1',
        'tng-ewallet.private_key_path' => $privateKeyPath,
        'tng-ewallet.key_version' => 1,
    ]);
}

test('missing private key file throws SigningException before any http call', function () {
    Http::fake();
    configureClientWithPrivateKeyPath('/tmp/does-not-exist-'.uniqid().'.pem');

    expect(fn () => (new TngClient())->post('/v1/payments/pay', ['a' => 1]))
        ->toThrow(SigningException::class);

    Http::assertNothingSent();
});

test('invalid PEM content throws SigningException before any http call', function () {
    Http::fake();

    $invalidKeyPath = tempnam(sys_get_temp_dir(), 'tng_invalid_');
    file_put_contents($invalidKeyPath, 'not a real pem key');
    configureClientWithPrivateKeyPath($invalidKeyPath);

    expect(fn () => (new TngClient())->post('/v1/payments/pay', ['a' => 1]))
        ->toThrow(SigningException::class);

    Http::assertNothingSent();

    unlink($invalidKeyPath);
});
