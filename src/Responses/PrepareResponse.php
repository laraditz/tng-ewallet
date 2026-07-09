<?php

namespace Laraditz\TngEwallet\Responses;

class PrepareResponse extends Response
{
    public readonly ?string $authId;

    public readonly ?string $authURL;

    public readonly ?string $authClientId;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->authId = $data['authId'] ?? null;
        $this->authURL = $data['authURL'] ?? null;
        $this->authClientId = $data['authClientId'] ?? null;
    }
}
