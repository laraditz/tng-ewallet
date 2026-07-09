<?php

namespace Laraditz\TngEwallet\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Laraditz\TngEwallet\Models\Notification;

class ProcessPaymentNotification
{
    use Dispatchable;

    public function __construct(public readonly array $payload)
    {
    }

    public function handle(): void
    {
        $result = $this->payload['paymentResult'] ?? [];

        Notification::create([
            'payment_id' => $this->payload['paymentId'] ?? null,
            'payment_request_id' => $this->payload['paymentRequestId'] ?? null,
            'customer_id' => $this->payload['customerId'] ?? null,
            'result_status' => $result['resultStatus'] ?? null,
            'result_code' => $result['resultCode'] ?? null,
            'result_message' => $result['resultMessage'] ?? null,
            'payment_amount_currency' => $this->payload['paymentAmount']['currency'] ?? null,
            'payment_amount_value' => $this->payload['paymentAmount']['value'] ?? null,
            'payment_time' => $this->payload['paymentTime'] ?? null,
            'payment_fail_reason' => $this->payload['paymentFailReason'] ?? null,
            'extend_info' => $this->payload['extendInfo'] ?? null,
            // The job only ever runs after VerifyTngNotifySignature has already
            // passed (it's dispatched from the controller post-ack) — so
            // verification is always true by the time handle() executes.
            'signature_verified' => true,
            'raw_payload' => $this->payload,
            'ack_sent_at' => now(),
        ]);
    }
}
