<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;

function generateAndConfigureRsaKeypairFixture(): array
{
    $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($resource, $privateKeyPem);
    $publicKeyPem = openssl_pkey_get_details($resource)['key'];

    $privateKeyPath = tempnam(sys_get_temp_dir(), 'tng_priv_');
    $publicKeyPath = tempnam(sys_get_temp_dir(), 'tng_pub_');
    file_put_contents($privateKeyPath, $privateKeyPem);
    file_put_contents($publicKeyPath, $publicKeyPem);

    config([
        'tng-ewallet.base_url' => 'https://example.test',
        'tng-ewallet.client_id' => 'TEST_CLIENT',
        'tng-ewallet.partner_id' => 'PARTNER1',
        'tng-ewallet.private_key_path' => $privateKeyPath,
        'tng-ewallet.public_key_path' => $publicKeyPath,
        'tng-ewallet.key_version' => 1,
        'tng-ewallet.verify_response_signature' => true,
    ]);

    return [$privateKeyPem, $publicKeyPem, $privateKeyPath, $publicKeyPath];
}

test('post() signs the request, dispatches it, and verifies the response signature', function () {
    [$privateKeyPem] = generateAndConfigureRsaKeypairFixture();

    $responseBody = json_encode(['result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success']]);
    $responseClientId = 'TEST_CLIENT';
    $responseTime = '2019-05-28T12:12:14.000+08:00';
    $contentToBeValidated = "POST /acl/api/v1/payments/pay\n{$responseClientId}.{$responseTime}.{$responseBody}";
    openssl_sign($contentToBeValidated, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    Http::fake([
        'https://example.test/*' => Http::response($responseBody, 200, [
            'Client-Id' => $responseClientId,
            'Response-Time' => $responseTime,
            'Signature' => "algorithm=RSA256, keyVersion=1, signature={$signature}",
        ]),
    ]);

    $result = (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-1']);

    expect($result)->toBe(['result' => ['resultStatus' => 'S', 'resultCode' => 'SUCCESS', 'resultMessage' => 'success']]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://example.test/acl/api/v1/payments/pay'
            && $request->hasHeader('Client-Id', 'TEST_CLIENT')
            && $request->hasHeader('Request-Time')
            && str_starts_with($request->header('Signature')[0], 'algorithm=RSA256, keyVersion=1, signature=');
    });
});
