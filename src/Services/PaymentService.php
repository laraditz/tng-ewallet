<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Responses\PayResponse;

class PaymentService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function pay(array $data): PayResponse
    {
        return new PayResponse($this->client->post('/v1/payments/pay', $data));
    }
}
