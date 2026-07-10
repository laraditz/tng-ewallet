<?php

use Laraditz\TngEwallet\Client\Concerns\MakesHttpRequests;

class BaseUrlResolutionTestSubject
{
    use MakesHttpRequests;

    public function callResolveBaseUrl(): string
    {
        return $this->resolveBaseUrl();
    }
}

test('sandbox is used when sandbox is true and no explicit base_url is set', function () {
    config([
        'tng-ewallet.base_url' => null,
        'tng-ewallet.sandbox' => true,
        'tng-ewallet.sandbox_url' => 'https://api-sd.tngdigital.com.my',
        'tng-ewallet.production_url' => 'https://api.tngdigital.com.my',
    ]);

    expect((new BaseUrlResolutionTestSubject())->callResolveBaseUrl())->toBe('https://api-sd.tngdigital.com.my');
});

test('production is used when sandbox is false and no explicit base_url is set', function () {
    config([
        'tng-ewallet.base_url' => null,
        'tng-ewallet.sandbox' => false,
        'tng-ewallet.sandbox_url' => 'https://api-sd.tngdigital.com.my',
        'tng-ewallet.production_url' => 'https://api.tngdigital.com.my',
    ]);

    expect((new BaseUrlResolutionTestSubject())->callResolveBaseUrl())->toBe('https://api.tngdigital.com.my');
});

test('explicit base_url always wins even when sandbox is also set', function () {
    config([
        'tng-ewallet.base_url' => 'https://custom.example.com',
        'tng-ewallet.sandbox' => true,
        'tng-ewallet.sandbox_url' => 'https://api-sd.tngdigital.com.my',
        'tng-ewallet.production_url' => 'https://api.tngdigital.com.my',
    ]);

    expect((new BaseUrlResolutionTestSubject())->callResolveBaseUrl())->toBe('https://custom.example.com');
});
