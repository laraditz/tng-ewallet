<?php

namespace Laraditz\TngEwallet\Responses;

class RefundResponse extends Response
{
    public readonly ?string $refundId;

    public readonly ?string $refundTime;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->refundId = $data['refundId'] ?? null;
        $this->refundTime = $data['refundTime'] ?? null;
    }
}
