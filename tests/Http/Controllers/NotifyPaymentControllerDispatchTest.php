<?php

use Illuminate\Support\Facades\Bus;
use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;

test('the controller dispatches ProcessPaymentNotification with the parsed payload', function () {
    Bus::fake();

    [$privateKeyPem] = generateNotifyKeypairAndConfigure();

    $uri = '/tng-ewallet/notify';
    $clientId = 'TEST_CLIENT';
    $requestTime = '2019-05-28T12:12:14.000+08:00';
    $payload = ['paymentId' => 'pay-1', 'paymentRequestId' => 'pr-1'];
    $body = json_encode($payload);

    $content = "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    openssl_sign($content, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    $this->call('POST', $uri, [], [], [], [
        'HTTP_Client-Id' => $clientId,
        'HTTP_Request-Time' => $requestTime,
        'HTTP_Signature' => "algorithm=RSA256, keyVersion=1, signature={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $body);

    Bus::assertDispatched(ProcessPaymentNotification::class, function ($job) use ($payload) {
        return $job->payload === $payload;
    });
});
