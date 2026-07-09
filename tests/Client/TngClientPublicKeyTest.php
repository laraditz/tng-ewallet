<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;

test('missing public key file throws a clear SignatureVerificationException', function () {
    generateAndConfigureRsaKeypairFixture();
    config([
        'tng-ewallet.verify_response_signature' => true,
        'tng-ewallet.public_key_path' => '/tmp/does-not-exist-'.uniqid().'.pem',
    ]);

    Http::fake([
        'https://example.test/*' => Http::response(
            json_encode(['result' => ['resultStatus' => 'S']]),
            200,
            ['Client-Id' => 'TEST_CLIENT', 'Response-Time' => now()->toIso8601String(), 'Signature' => 'algorithm=RSA256, keyVersion=1, signature=abc'],
        ),
    ]);

    expect(fn () => (new TngClient())->post('/v1/payments/pay', ['paymentRequestId' => 'pr-1']))
        ->toThrow(SignatureVerificationException::class, 'does not exist or is not readable');
});
