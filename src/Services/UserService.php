<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Responses\UserInfoResponse;

class UserService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function inquiryByAccessToken(array $data): UserInfoResponse
    {
        return new UserInfoResponse($this->client->post('/v1/customers/user/inquiryUserInfoByAccessToken', $data));
    }
}
