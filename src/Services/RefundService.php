<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Models\Refund;
use Laraditz\TngEwallet\Responses\InquiryRefundResponse;
use Laraditz\TngEwallet\Responses\RefundResponse;
use Laraditz\TngEwallet\Services\Concerns\DefaultsPartnerId;

class RefundService
{
    use DefaultsPartnerId;

    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(array $data): RefundResponse
    {
        $response = new RefundResponse($this->client->post('/v1/payments/refund', $this->withPartnerId($data)));

        Refund::create([
            'refund_id' => $response->refundId,
            'refund_request_id' => $data['refundRequestId'],
            'payment_id' => $data['paymentId'] ?? null,
            'payment_request_id' => $data['paymentRequestId'] ?? null,
            'refund_status' => 'PROCESSING',
            'result_status' => $response->resultStatus,
            'result_code' => $response->resultCode,
            'refund_amount_currency' => $data['refundAmount']['currency'] ?? null,
            'refund_amount_value' => $data['refundAmount']['value'] ?? null,
            'refund_reason' => $data['refundReason'] ?? null,
            'refund_time' => $response->refundTime,
        ]);

        return $response;
    }

    public function inquiry(array $data): InquiryRefundResponse
    {
        $response = new InquiryRefundResponse($this->client->post('/v1/payments/inquiryRefund', $this->withPartnerId($data)));

        $refundRequestId = $data['refundRequestId'] ?? $response->refundRequestId;
        $refund = $refundRequestId !== null ? Refund::where('refund_request_id', $refundRequestId)->first() : null;

        $refund?->update([
            'refund_id' => $response->refundId ?? $refund->refund_id,
            'refund_status' => $response->refundStatus,
            'result_status' => $response->resultStatus,
            'result_code' => $response->resultCode,
            'refund_time' => $response->refundTime,
            'refund_fail_reason' => $response->refundFailReason,
        ]);

        return $response;
    }
}
