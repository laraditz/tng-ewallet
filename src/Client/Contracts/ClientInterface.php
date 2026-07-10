<?php

namespace Laraditz\TngEwallet\Client\Contracts;

interface ClientInterface
{
    public function post(string $uri, array $data): array;
}
