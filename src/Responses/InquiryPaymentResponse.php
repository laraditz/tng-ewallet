<?php

namespace Laraditz\TngEwallet\Responses;

class InquiryPaymentResponse extends Response
{
    public readonly ?string $paymentId;

    public readonly ?string $paymentRequestId;

    public readonly ?array $paymentAmount;

    public readonly ?string $paymentTime;

    public readonly ?string $paymentStatus;

    public readonly ?string $paymentFailReason;

    public readonly ?string $authExpiryTime;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->paymentId = $data['paymentId'] ?? null;
        $this->paymentRequestId = $data['paymentRequestId'] ?? null;
        $this->paymentAmount = $data['paymentAmount'] ?? null;
        $this->paymentTime = $data['paymentTime'] ?? null;
        $this->paymentStatus = $data['paymentStatus'] ?? null;
        $this->paymentFailReason = $data['paymentFailReason'] ?? null;
        $this->authExpiryTime = $data['authExpiryTime'] ?? null;
    }
}
