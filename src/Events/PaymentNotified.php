<?php

namespace Laraditz\TngEwallet\Events;

class PaymentNotified
{
    public function __construct(public readonly array $payload)
    {
    }
}
