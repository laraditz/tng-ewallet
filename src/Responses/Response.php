<?php

namespace Laraditz\TngEwallet\Responses;

use Laraditz\TngEwallet\Enums\ResultStatus;

class Response
{
    public readonly ?string $resultStatus;

    public readonly ?string $resultCode;

    public readonly ?string $resultMessage;

    public function __construct(protected array $data)
    {
        $result = $data['result'] ?? [];

        $this->resultStatus = $result['resultStatus'] ?? null;
        $this->resultCode = $result['resultCode'] ?? null;
        $this->resultMessage = $result['resultMessage'] ?? null;
    }

    public function isSuccessful(): bool
    {
        return $this->resultStatus === ResultStatus::Success->value;
    }

    public function isAccepted(): bool
    {
        return $this->resultStatus === ResultStatus::Accepted->value;
    }

    public function isFailed(): bool
    {
        return $this->resultStatus === ResultStatus::Failed->value;
    }

    public function isUnknown(): bool
    {
        return $this->resultStatus === ResultStatus::Unknown->value;
    }

    public function raw(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
