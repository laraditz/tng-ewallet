<?php

test('the controller responds with the mandatory ack body once the signature check passes', function () {
    [$privateKeyPem] = generateNotifyKeypairAndConfigure();

    $uri = '/tng-ewallet/notify';
    $clientId = 'TEST_CLIENT';
    $requestTime = '2019-05-28T12:12:14.000+08:00';
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

    $response->assertStatus(200);
    $response->assertJson([
        'result' => [
            'resultCode' => 'SUCCESS',
            'resultStatus' => 'S',
            'resultMessage' => 'success',
        ],
    ]);
});
