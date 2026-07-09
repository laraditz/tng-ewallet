<?php

namespace Laraditz\TngEwallet\Responses\ValueObjects;

class ActionForm
{
    public readonly ?string $actionFormType;

    public readonly ?string $orderCode;

    public readonly ?string $redirectionUrl;

    public function __construct(array $data)
    {
        $this->actionFormType = $data['actionFormType'] ?? null;
        $this->orderCode = $data['orderCode'] ?? null;
        $this->redirectionUrl = $data['redirectionUrl'] ?? null;
    }
}
