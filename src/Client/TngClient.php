<?php

namespace Laraditz\TngEwallet\Client;

use Laraditz\TngEwallet\Client\Concerns\HandlesErrors;
use Laraditz\TngEwallet\Client\Concerns\HandlesSigning;
use Laraditz\TngEwallet\Client\Concerns\MakesHttpRequests;
use Laraditz\TngEwallet\Client\Concerns\VerifiesResponseSignature;
use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Illuminate\Http\Client\ConnectionException;
use Laraditz\TngEwallet\Exceptions\ApiException;
use Laraditz\TngEwallet\Exceptions\ConfigurationException;
use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Models\ApiLog;

class TngClient implements ClientInterface
{
    use HandlesErrors;
    use HandlesSigning;
    use MakesHttpRequests;
    use VerifiesResponseSignature;

    protected const REQUIRED_CONFIG_KEYS = ['client_id', 'partner_id', 'private_key_path'];

    protected const REFERENCE_ID_KEYS = ['paymentRequestId', 'refundRequestId', 'customerId'];

    protected const REDACTED_KEYS = ['accessToken', 'refreshToken', 'authCode'];

    public function post(string $uri, array $data): array
    {
        $this->assertConfigured();

        $fullUri = config('tng-ewallet.api_path').$uri;

        $clientId = config('tng-ewallet.client_id');
        $requestTime = $this->generateRequestTime();
        $body = json_encode($data);

        $content = $this->buildContentToBeSigned($fullUri, $clientId, $requestTime, $body);
        $signature = $this->sign($content, config('tng-ewallet.private_key_path'));
        $headers = $this->buildSigningHeaders($clientId, $requestTime, (int) config('tng-ewallet.key_version'), $signature);

        $response = null;
        $signatureVerified = null;
        $startedAt = microtime(true);

        try {
            $response = $this->newRequest()
                ->withHeaders($headers)
                ->withBody($body, 'application/json; charset=UTF-8')
                ->post($fullUri);

            $this->assertSuccessfulResponse($response);

            if (config('tng-ewallet.verify_response_signature')) {
                $this->assertValidSignature(
                    $fullUri,
                    $response->header('Client-Id'),
                    $response->header('Response-Time'),
                    $response->body(),
                    $this->extractSignatureValue($response->header('Signature')),
                    $this->readPublicKey(),
                );
                $signatureVerified = true;
            }

            return $response->json();
        } catch (SignatureVerificationException $exception) {
            $signatureVerified = false;

            throw $exception;
        } catch (ConnectionException $exception) {
            throw new ApiException(
                "TNG API request failed: {$exception->getMessage()}",
                response: null,
                statusCode: null,
            );
        } finally {
            $this->logApiCall($fullUri, $data, $response, $signatureVerified, $startedAt);
        }
    }

    protected function logApiCall(string $uri, array $data, ?\Illuminate\Http\Client\Response $response, ?bool $signatureVerified, float $startedAt): void
    {
        // Only trust parsed result fields and the raw body from a response that
        // was either verified successfully or never required verification in
        // the first place — never persist fields from a response whose
        // signature verification failed, since they may be forged.
        $trustworthy = $signatureVerified !== false;
        $result = $trustworthy ? ($response?->json('result') ?? []) : [];

        $responsePayload = $trustworthy ? $response?->json() : null;

        ApiLog::create([
            'endpoint' => $uri,
            'reference_id' => $this->extractReferenceId($data),
            'request_payload' => $this->redactSensitiveFields($data),
            'response_payload' => is_null($responsePayload) ? null : $this->redactSensitiveFields($responsePayload),
            'http_status' => $response?->status(),
            'signature_verified' => $signatureVerified,
            'result_status' => $result['resultStatus'] ?? null,
            'result_code' => $result['resultCode'] ?? null,
            'result_message' => $result['resultMessage'] ?? null,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);
    }

    protected function redactSensitiveFields(array $data): array
    {
        foreach (self::REDACTED_KEYS as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[redacted:'.AccessToken::hashToken($data[$key]).']';
            }
        }

        return $data;
    }

    protected function extractReferenceId(array $data): ?string
    {
        foreach (self::REFERENCE_ID_KEYS as $key) {
            if (! empty($data[$key])) {
                return $data[$key];
            }
        }

        return null;
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
