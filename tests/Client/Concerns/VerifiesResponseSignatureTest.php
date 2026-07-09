<?php

use Laraditz\TngEwallet\Client\Concerns\VerifiesResponseSignature;

class VerifiesResponseSignatureTestSubject
{
    use VerifiesResponseSignature;

    public function callVerify(string $uri, string $clientId, string $responseTime, string $body, string $signature, string $publicKeyPem): bool
    {
        return $this->verifySignature($uri, $clientId, $responseTime, $body, $signature, $publicKeyPem);
    }
}

function generateRsaKeypairFixtureForVerification(): array
{
    $resource = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);

    openssl_pkey_export($resource, $privateKeyPem);
    $publicKeyPem = openssl_pkey_get_details($resource)['key'];

    return [$privateKeyPem, $publicKeyPem];
}

test('verifySignature returns true for a validly signed response', function () {
    [$privateKeyPem, $publicKeyPem] = generateRsaKeypairFixtureForVerification();

    $uri = '/v1/payments/pay';
    $clientId = 'TEST_5X00000000000000';
    $responseTime = '2019-05-28T12:12:14.000+08:00';
    $body = '{"result":{"resultStatus":"S"}}';

    $content = "POST {$uri}\n{$clientId}.{$responseTime}.{$body}";
    openssl_sign($content, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    $result = (new VerifiesResponseSignatureTestSubject())->callVerify($uri, $clientId, $responseTime, $body, $signature, $publicKeyPem);

    expect($result)->toBeTrue();
});
