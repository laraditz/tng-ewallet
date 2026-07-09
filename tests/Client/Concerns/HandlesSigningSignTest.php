<?php

use Laraditz\TngEwallet\Client\Concerns\HandlesSigning;

class HandlesSigningSignTestSubject
{
    use HandlesSigning;

    public function callSign(string $content, string $privateKeyPath): string
    {
        return $this->sign($content, $privateKeyPath);
    }
}

function generateRsaKeypairFixture(): array
{
    $resource = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);

    openssl_pkey_export($resource, $privateKeyPem);
    $publicKeyPem = openssl_pkey_get_details($resource)['key'];

    $privateKeyPath = tempnam(sys_get_temp_dir(), 'tng_priv_');
    file_put_contents($privateKeyPath, $privateKeyPem);

    return [$privateKeyPath, $publicKeyPem];
}

test('sign produces a base64url string that verifies against the matching public key', function () {
    [$privateKeyPath, $publicKeyPem] = generateRsaKeypairFixture();

    $content = "POST /v1/payments/pay\nCLIENT.2019-05-28T12:12:12+08:00.{\"a\":1}";

    $signature = (new HandlesSigningSignTestSubject())->callSign($content, $privateKeyPath);

    // base64url alphabet only, no padding
    expect($signature)->toMatch('/^[A-Za-z0-9_-]+$/');

    $rawSignature = base64_decode(strtr($signature, '-_', '+/'));

    expect(openssl_verify($content, $rawSignature, $publicKeyPem, OPENSSL_ALGO_SHA256))->toBe(1);

    unlink($privateKeyPath);
});
