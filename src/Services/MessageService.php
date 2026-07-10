<?php

namespace Laraditz\TngEwallet\Services;

use Laraditz\TngEwallet\Client\Contracts\ClientInterface;
use Laraditz\TngEwallet\Responses\SendMessageResponse;

class MessageService
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function sendByAccessToken(array $data): SendMessageResponse
    {
        return new SendMessageResponse($this->client->post('/v2/customers/message/sendByAccessToken', $data));
    }
}
