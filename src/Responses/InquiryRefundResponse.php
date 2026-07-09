<?php

namespace Laraditz\TngEwallet\Responses;

class InquiryRefundResponse extends Response
{
    public readonly ?string $refundId;

    public readonly ?string $refundRequestId;

    public readonly ?array $refundAmount;

    public readonly ?string $refundReason;

    public readonly ?string $refundTime;

    public readonly ?string $refundStatus;

    public readonly ?string $refundFailReason;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->refundId = $data['refundId'] ?? null;
        $this->refundRequestId = $data['refundRequestId'] ?? null;
        $this->refundAmount = $data['refundAmount'] ?? null;
        $this->refundReason = $data['refundReason'] ?? null;
        $this->refundTime = $data['refundTime'] ?? null;
        $this->refundStatus = $data['refundStatus'] ?? null;
        $this->refundFailReason = $data['refundFailReason'] ?? null;
    }
}
