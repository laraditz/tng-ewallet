<?php

namespace Laraditz\TngEwallet\Client\Concerns;

use Laraditz\TngEwallet\Exceptions\SigningException;

trait HandlesSigning
{
    protected function buildContentToBeSigned(string $uri, string $clientId, string $requestTime, string $body): string
    {
        return "POST {$uri}\n{$clientId}.{$requestTime}.{$body}";
    }

    protected function sign(string $content, string $privateKeyPath): string
    {
        if (! is_readable($privateKeyPath)) {
            throw new SigningException("The private key file at \"{$privateKeyPath}\" does not exist or is not readable.");
        }

        $privateKey = file_get_contents($privateKeyPath);

        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if ($privateKeyResource === false) {
            throw new SigningException("The private key at \"{$privateKeyPath}\" is not a valid PEM-encoded RSA key.");
        }

        if (! openssl_sign($content, $rawSignature, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
            throw new SigningException('Failed to sign the request content with the configured private key.');
        }

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
