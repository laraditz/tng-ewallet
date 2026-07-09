<?php

namespace Laraditz\TngEwallet\Exceptions;

class ApiException extends TngException
{
    public function __construct(
        string $message,
        protected ?array $response = null,
        protected ?int $statusCode = null,
    ) {
        parent::__construct($message);
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function getApiStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
