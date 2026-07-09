<?php

namespace Laraditz\TngEwallet\Client\Concerns;

trait HandlesSigning
{
    protected function buildContentToBeSigned(string $uri, string $clientId, string $requestTime, string $body): string
    {
        return "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    }

    protected function sign(string $content, string $privateKeyPath): string
    {
        $privateKey = file_get_contents($privateKeyPath);

        openssl_sign($content, $rawSignature, $privateKey, OPENSSL_ALGO_SHA256);

        return $this->base64UrlEncode($rawSignature);
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    protected function buildSigningHeaders(string $clientId, string $requestTime, int $keyVersion, string $signature): array
    {
        return [
            'Client-Id' => $clientId,
            'Request-Time' => $requestTime,
            'Signature' => "algorithm=RSA256, keyVersion={$keyVersion}, signature={$signature}",
        ];
    }

    protected function generateRequestTime(): string
    {
        return now()->format('Y-m-d\TH:i:s.vP');
    }
}
