<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Models\Refund;
use Laraditz\TngEwallet\Responses\InquiryRefundResponse;
use Laraditz\TngEwallet\Responses\RefundResponse;

class RefundService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(array $data): RefundResponse
    {
        $response = new RefundResponse($this->client->post('/v1/payments/refund', $data));

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
        return new InquiryRefundResponse($this->client->post('/v1/payments/inquiryRefund', $data));
    }
}
