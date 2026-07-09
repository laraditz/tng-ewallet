<?php

namespace Laraditz\TngEwallet\Responses;

class UserInfoResponse extends Response
{
    public readonly ?array $userInfo;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->userInfo = $data['userInfo'] ?? null;
    }
}
