<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Responses\ApplyTokenResponse;
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
        return new ApplyTokenResponse($this->client->post('/v1/authorizations/applyToken', $data));
    }
}
