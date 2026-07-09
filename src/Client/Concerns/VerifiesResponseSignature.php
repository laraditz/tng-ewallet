<?php

namespace Laraditz\TngEwallet\Client\Concerns;

use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;

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

    protected function assertValidSignature(string $uri, string $clientId, string $responseTime, string $body, string $signature, string $publicKeyPem): void
    {
        if (! $this->verifySignature($uri, $clientId, $responseTime, $body, $signature, $publicKeyPem)) {
            throw new SignatureVerificationException('The response signature could not be verified against the configured TNG public key.');
        }
    }

    protected function extractSignatureValue(string $signatureHeader): string
    {
        preg_match('/signature=(.+)$/', $signatureHeader, $matches);

        return $matches[1] ?? '';
    }

    protected function readPublicKey(): string
    {
        $publicKeyPath = config('tng-ewallet.public_key_path');

        if (! is_readable($publicKeyPath)) {
            throw new SignatureVerificationException("The public key file at \"{$publicKeyPath}\" does not exist or is not readable.");
        }

        return file_get_contents($publicKeyPath);
    }
}
