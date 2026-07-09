<?php

namespace Laraditz\TngEwallet\Responses;

class ApplyTokenResponse extends Response
{
    public readonly ?string $accessToken;

    public readonly ?string $accessTokenExpiryTime;

    public readonly ?string $refreshToken;

    public readonly ?string $refreshTokenExpiryTime;

    public readonly ?string $customerId;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->accessToken = $data['accessToken'] ?? null;
        $this->accessTokenExpiryTime = $data['accessTokenExpiryTime'] ?? null;
        $this->refreshToken = $data['refreshToken'] ?? null;
        $this->refreshTokenExpiryTime = $data['refreshTokenExpiryTime'] ?? null;
        $this->customerId = $data['customerId'] ?? null;
    }
}
