<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Models\TngUser;
use Laraditz\TngEwallet\Responses\UserInfoResponse;

class UserService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function inquiryByAccessToken(array $data): UserInfoResponse
    {
        $response = new UserInfoResponse($this->client->post('/v1/customers/user/inquiryUserInfoByAccessToken', $data));

        $userId = $response->userInfo['userId'] ?? null;

        if ($userId !== null) {
            TngUser::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_info' => $response->userInfo,
                    'last_fetched_at' => now(),
                ],
            );
        }

        return $response;
    }
}
