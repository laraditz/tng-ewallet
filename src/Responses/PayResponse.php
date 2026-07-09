<?php

namespace Laraditz\TngEwallet\Responses;

use Laraditz\TngEwallet\Responses\ValueObjects\ActionForm;

class PayResponse extends Response
{
    public readonly ?string $paymentId;

    public readonly ?string $paymentTime;

    public readonly ?ActionForm $actionForm;

    public readonly ?string $authExpiryTime;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->paymentId = $data['paymentId'] ?? null;
        $this->paymentTime = $data['paymentTime'] ?? null;
        $this->actionForm = isset($data['actionForm']) ? new ActionForm($data['actionForm']) : null;
        $this->authExpiryTime = $data['authExpiryTime'] ?? null;
    }
}
