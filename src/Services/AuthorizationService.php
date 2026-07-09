<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Enums\AccessTokenStatus;
use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Responses\ApplyTokenResponse;
use Laraditz\TngEwallet\Responses\CancelTokenResponse;
use Laraditz\TngEwallet\Responses\PrepareResponse;

class AuthorizationService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function prepare(array $data): PrepareResponse
    {
        return new PrepareResponse($this->client->post('/v1/authorizations/prepare', $data));
    }

    public function applyToken(array $data): ApplyTokenResponse
    {
        $response = new ApplyTokenResponse($this->client->post('/v1/authorizations/applyToken', $data));

        AccessToken::create([
            'customer_id' => $response->customerId,
            'reference_client_id' => $data['referenceClientId'] ?? null,
            'access_token' => $response->accessToken,
            'access_token_expiry_time' => $response->accessTokenExpiryTime,
            'refresh_token' => $response->refreshToken,
            'refresh_token_expiry_time' => $response->refreshTokenExpiryTime,
            'grant_type' => $data['grantType'],
            'status' => AccessTokenStatus::Active->value,
            'result_status' => $response->resultStatus,
            'result_code' => $response->resultCode,
        ]);

        return $response;
    }

    public function cancelToken(array $data): CancelTokenResponse
    {
        return new CancelTokenResponse($this->client->post('/v1/authorizations/cancelToken', $data));
    }
}
