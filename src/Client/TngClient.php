<?php

namespace Laraditz\TngEwallet\Client;

use Laraditz\TngEwallet\Client\Concerns\HandlesErrors;
use Laraditz\TngEwallet\Client\Concerns\HandlesSigning;
use Laraditz\TngEwallet\Client\Concerns\MakesHttpRequests;
use Laraditz\TngEwallet\Client\Concerns\VerifiesResponseSignature;
use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Exceptions\ConfigurationException;

class TngClient implements ClientInterface
{
    use HandlesErrors;
    use HandlesSigning;
    use MakesHttpRequests;
    use VerifiesResponseSignature;

    protected const REQUIRED_CONFIG_KEYS = ['client_id', 'partner_id', 'private_key_path'];

    public function post(string $uri, array $data): array
    {
        $this->assertConfigured();

        $clientId = config('tng-ewallet.client_id');
        $requestTime = $this->generateRequestTime();
        $body = json_encode($data);

        $content = $this->buildContentToBeSigned($uri, $clientId, $requestTime, $body);
        $signature = $this->sign($content, config('tng-ewallet.private_key_path'));
        $headers = $this->buildSigningHeaders($clientId, $requestTime, (int) config('tng-ewallet.key_version'), $signature);

        $response = $this->newRequest()
            ->withHeaders($headers)
            ->withBody($body, 'application/json; charset=UTF-8')
            ->post($uri);

        $this->assertSuccessfulResponse($response);

        if (config('tng-ewallet.verify_response_signature')) {
            $this->assertValidSignature(
                $uri,
                $response->header('Client-Id'),
                $response->header('Response-Time'),
                $response->body(),
                $this->extractSignatureValue($response->header('Signature')),
                file_get_contents(config('tng-ewallet.public_key_path')),
            );
        }

        return $response->json();
    }

    protected function extractSignatureValue(string $signatureHeader): string
    {
        preg_match('/signature=(.+)$/', $signatureHeader, $matches);

        return $matches[1] ?? '';
    }

    protected function assertConfigured(): void
    {
        foreach (self::REQUIRED_CONFIG_KEYS as $key) {
            if (empty(config("tng-ewallet.{$key}"))) {
                throw new ConfigurationException("The \"tng-ewallet.{$key}\" config value is required but missing.");
            }
        }
    }
}
