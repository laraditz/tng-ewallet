<?php

use Laraditz\TngEwallet\Jobs\ProcessPaymentNotification;
use Laraditz\TngEwallet\Models\Notification;

test('handle() persists the delivery as a Notification row', function () {
    // Verbatim shape from vendor doc "11. API - notifyPayment.md" sample A.
    $payload = [
        'paymentResult' => [
            'resultStatus' => 'S',
            'resultCode' => 'SUCCESS',
            'resultMessage' => 'success',
        ],
        'paymentId' => '20210726111212800110171163001220213',
        'paymentRequestId' => '6-20210714041658535w',
        'customerId' => '1000000839990000',
        'paymentTime' => '2021-07-26T12:13:50+08:00',
        'paymentAmount' => ['currency' => 'MYR', 'value' => '10000'],
    ];

    (new ProcessPaymentNotification($payload))->handle();

    expect(Notification::count())->toBe(1);

    $notification = Notification::first();
    expect($notification->payment_id)->toBe('20210726111212800110171163001220213')
        ->and($notification->payment_request_id)->toBe('6-20210714041658535w')
        ->and($notification->customer_id)->toBe('1000000839990000')
        ->and($notification->result_status->value)->toBe('S')
        ->and($notification->payment_amount_currency)->toBe('MYR')
        ->and($notification->payment_amount_value)->toBe('10000')
        ->and($notification->raw_payload)->toBe($payload);
});
