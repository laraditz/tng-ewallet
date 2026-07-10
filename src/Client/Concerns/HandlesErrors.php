<?php

namespace Laraditz\TngEwallet\Client\Concerns;

use Illuminate\Http\Client\Response;
use Laraditz\TngEwallet\Exceptions\ApiException;

trait HandlesErrors
{
    protected function assertSuccessfulResponse(Response $response): void
    {
        if (! $response->successful()) {
            throw new ApiException(
                "TNG API request failed with HTTP status {$response->status()}.",
                response: $response->json(),
                statusCode: $response->status(),
            );
        }

        if ($response->json() === null) {
            throw new ApiException(
                'TNG API response body could not be parsed as JSON.',
                response: null,
                statusCode: $response->status(),
            );
        }
    }
}
