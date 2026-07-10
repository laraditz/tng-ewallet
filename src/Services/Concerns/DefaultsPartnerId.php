<?php

namespace Laraditz\TngEwallet\Services\Concerns;

trait DefaultsPartnerId
{
    protected function withPartnerId(array $data): array
    {
        return $data + ['partnerId' => config('tng-ewallet.partner_id')];
    }
}
