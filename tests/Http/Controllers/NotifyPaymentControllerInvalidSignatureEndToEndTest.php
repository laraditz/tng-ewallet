<?php

use Illuminate\Support\Facades\Bus;
use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;
use Laraditz\TngEwallet\Models\Notification;

test('an invalid signature through the real route/middleware/controller chain never dispatches the job or persists a notification', function () {
    Bus::fake();
    generateNotifyKeypairAndConfigure();

    $body = json_encode(['paymentId' => 'pay-1', 'paymentRequestId' => 'pr-1']);

    $response = $this->call('POST', '/tng-ewallet/notify', [], [], [], [
        'HTTP_Client-Id' => 'TEST_CLIENT',
        'HTTP_Request-Time' => now()->format('Y-m-d\TH:i:s.vP'),
        'HTTP_Signature' => 'algorithm=RSA256, keyVersion=1, signature=not-a-real-signature',
        'CONTENT_TYPE' => 'application/json',
    ], $body);

    $response->assertStatus(401);
    Bus::assertNotDispatched(ProcessPaymentNotification::class);
    expect(Notification::count())->toBe(0);
});
