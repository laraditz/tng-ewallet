<?php

test('the full request/response cycle returns the exact documented ack JSON shape', function () {
    [$privateKeyPem] = generateNotifyKeypairAndConfigure();

    $uri = '/tng-ewallet/notify';
    $clientId = 'TEST_CLIENT';
    $requestTime = now()->format('Y-m-d\TH:i:s.vP');
    $body = json_encode(['paymentId' => 'pay-1', 'paymentRequestId' => 'pr-1']);

    $content = "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    openssl_sign($content, $rawSignature, $privateKeyPem, OPENSSL_ALGO_SHA256);
    $signature = rtrim(strtr(base64_encode($rawSignature), '+/', '-_'), '=');

    $response = $this->call('POST', $uri, [], [], [], [
        'HTTP_Client-Id' => $clientId,
        'HTTP_Request-Time' => $requestTime,
        'HTTP_Signature' => "algorithm=RSA256, keyVersion=1, signature={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $body);

    expect(json_decode($response->getContent(), true))->toBe([
        'result' => [
            'resultCode' => 'SUCCESS',
            'resultStatus' => 'S',
            'resultMessage' => 'success',
        ],
    ]);
});
