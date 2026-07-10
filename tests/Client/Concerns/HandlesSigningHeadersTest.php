<?php

use Laraditz\TngEwallet\Client\Concerns\HandlesSigning;

class HandlesSigningHeadersTestSubject
{
    use HandlesSigning;

    public function callBuildHeaders(string $clientId, string $requestTime, int $keyVersion, string $signature): array
    {
        return $this->buildSigningHeaders($clientId, $requestTime, $keyVersion, $signature);
    }

    public function callGenerateRequestTime(): string
    {
        return $this->generateRequestTime();
    }
}

test('builds the Client-Id, Request-Time, and Signature headers in the documented format', function () {
    $headers = (new HandlesSigningHeadersTestSubject())->callBuildHeaders(
        'TEST_5X00000000000000',
        '2019-05-28T12:12:12.123+08:00',
        1,
        'sig123',
    );

    expect($headers)->toBe([
        'Client-Id' => 'TEST_5X00000000000000',
        'Request-Time' => '2019-05-28T12:12:12.123+08:00',
        'Signature' => 'algorithm=RSA256, keyVersion=1, signature=sig123',
    ]);
});

test('generateRequestTime produces an RFC3339 timestamp with millisecond precision', function () {
    $requestTime = (new HandlesSigningHeadersTestSubject())->callGenerateRequestTime();

    expect($requestTime)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}[+-]\d{2}:\d{2}$/');
});
