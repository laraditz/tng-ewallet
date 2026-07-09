<?php

namespace Laraditz\TngEwallet\Client\Concerns;

trait VerifiesResponseSignature
{
    protected function buildContentToBeValidated(string $uri, string $clientId, string $responseTime, string $body): string
    {
        return "POST {$uri}\n{$clientId}.{$responseTime}.{$body}";
    }

    protected function verifySignature(string $uri, string $clientId, string $responseTime, string $body, string $signature, string $publicKeyPem): bool
    {
        $content = $this->buildContentToBeValidated($uri, $clientId, $responseTime, $body);
        $rawSignature = base64_decode(strtr($signature, '-_', '+/'));

        return openssl_verify($content, $rawSignature, $publicKeyPem, OPENSSL_ALGO_SHA256) === 1;
    }
}
