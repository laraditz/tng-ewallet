<?php

namespace Laraditz\TngEwallet\Client\Concerns;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait MakesHttpRequests
{
    protected function newRequest(): PendingRequest
    {
        return Http::baseUrl($this->resolveBaseUrl())
            ->timeout((int) config('tng-ewallet.timeout'));
    }

    protected function resolveBaseUrl(): string
    {
        if ($baseUrl = config('tng-ewallet.base_url')) {
            return $baseUrl;
        }

        return config('tng-ewallet.sandbox')
            ? config('tng-ewallet.sandbox_url')
            : config('tng-ewallet.production_url');
    }
}
