<?php

namespace Laraditz\TngEwallet\Client\Concerns;

trait HandlesSigning
{
    protected function buildContentToBeSigned(string $uri, string $clientId, string $requestTime, string $body): string
    {
        return "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    }
}
