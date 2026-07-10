<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\Concerns\MakesHttpRequests;

class MakesHttpRequestsTestSubject
{
    use MakesHttpRequests;

    public function callPost(string $uri, array $data): array
    {
        return $this->newRequest()->post($uri, $data)->json();
    }
}

test('the trait sends requests against the configured base url', function () {
    config(['tng-ewallet.base_url' => 'https://example.test']);
    Http::fake(['https://example.test/*' => Http::response(['ok' => true])]);

    (new MakesHttpRequestsTestSubject())->callPost('/v1/payments/pay', ['a' => 1]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://example.test/v1/payments/pay';
    });
});
