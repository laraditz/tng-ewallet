<?php

use Laraditz\TngEwallet\Client\Concerns\HandlesSigning;

class HandlesSigningTestSubject
{
    use HandlesSigning;

    public function callBuildContentToBeSigned(string $uri, string $clientId, string $requestTime, string $body): string
    {
        return $this->buildContentToBeSigned($uri, $clientId, $requestTime, $body);
    }
}

test('content to be signed matches the vendor doc syntax exactly', function () {
    $subject = new HandlesSigningTestSubject();

    $result = $subject->callBuildContentToBeSigned(
        '/api/v1/payments/pay',
        'TEST_5X00000000000000',
        '2019-05-28T12:12:12+08:00',
        '{"paymentRequestId":"pr-1"}',
    );

    expect($result)->toBe("POST /api/v1/payments/pay\nTEST_5X00000000000000.2019-05-28T12:12:12+08:00.{\"paymentRequestId\":\"pr-1\"}");
});
