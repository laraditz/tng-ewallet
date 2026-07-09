<?php

namespace Laraditz\TngEwallet\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPaymentNotification
{
    use Dispatchable;

    public function __construct(public readonly array $payload)
    {
    }

    public function handle(): void
    {
        //
    }
}
