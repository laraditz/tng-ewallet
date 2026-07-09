<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
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
}
