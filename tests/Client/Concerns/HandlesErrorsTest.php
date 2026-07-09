<?php

use Illuminate\Support\Facades\Http;
use Laraditz\TngEwallet\Client\Concerns\HandlesErrors;
use Laraditz\TngEwallet\Exceptions\ApiException;

class HandlesErrorsTestSubject
{
    use HandlesErrors;

    public function callAssert(\Illuminate\Http\Client\Response $response): void
    {
        $this->assertSuccessfulResponse($response);
    }
}

test('non-2xx http response throws ApiException carrying raw response and status', function () {
    Http::fake(['https://example.test/*' => Http::response(['error' => 'bad gateway'], 502)]);
    $response = Http::get('https://example.test/fail');

    try {
        (new HandlesErrorsTestSubject())->callAssert($response);
        $this->fail('Expected ApiException was not thrown.');
    } catch (ApiException $exception) {
        expect($exception->getApiStatusCode())->toBe(502)
            ->and($exception->getResponse())->toBe(['error' => 'bad gateway']);
    }
});

test('malformed json body throws ApiException', function () {
    Http::fake(['https://example.test/*' => Http::response('not json{{{', 200)]);
    $response = Http::get('https://example.test/malformed');

    expect(fn () => (new HandlesErrorsTestSubject())->callAssert($response))
        ->toThrow(ApiException::class);
});
