<?php

use Laraditz\TngEwallet\Client\Concerns\VerifiesResponseSignature;
use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;

class VerifiesResponseSignatureAssertTestSubject
{
    use VerifiesResponseSignature;

    public function callAssertValidSignature(string $uri, string $clientId, string $responseTime, string $body, string $signature, string $publicKeyPem): void
    {
        $this->assertValidSignature($uri, $clientId, $responseTime, $body, $signature, $publicKeyPem);
    }
}

test('assertValidSignature throws SignatureVerificationException for a tampered signature', function () {
    $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    $publicKeyPem = openssl_pkey_get_details($resource)['key'];

    $subject = new VerifiesResponseSignatureAssertTestSubject();

    expect(fn () => $subject->callAssertValidSignature(
        '/v1/payments/pay',
        'TEST_5X00000000000000',
        '2019-05-28T12:12:14.000+08:00',
        '{"result":{"resultStatus":"S"}}',
        'not-a-real-signature',
        $publicKeyPem,
    ))->toThrow(SignatureVerificationException::class);
});
