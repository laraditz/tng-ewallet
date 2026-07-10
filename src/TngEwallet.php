<?php

namespace Laraditz\TngEwallet;

use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Services\AuthorizationService;
use Laraditz\TngEwallet\Services\MessageService;
use Laraditz\TngEwallet\Services\PaymentService;
use Laraditz\TngEwallet\Services\RefundService;
use Laraditz\TngEwallet\Services\UserService;

class TngEwallet
{
    public function __construct(protected TngClient $client)
    {
    }

    public function authorization(): AuthorizationService
    {
        return new AuthorizationService($this->client);
    }

    public function user(): UserService
    {
        return new UserService($this->client);
    }

    public function payment(): PaymentService
    {
        return new PaymentService($this->client);
    }

    public function refund(): RefundService
    {
        return new RefundService($this->client);
    }

    public function message(): MessageService
    {
        return new MessageService($this->client);
    }

    public function client(): TngClient
    {
        return $this->client;
    }
}
