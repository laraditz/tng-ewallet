<?php

namespace Laraditz\TngEwallet\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Laraditz\TngEwallet\Client\Concerns\VerifiesResponseSignature;
use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;

class VerifyTngNotifySignature
{
    use VerifiesResponseSignature;

    protected const FRESHNESS_TOLERANCE_MINUTES = 5;

    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasHeader('Client-Id') || ! $request->hasHeader('Request-Time') || ! $request->hasHeader('Signature')) {
            return response()->json(['message' => 'Missing required signature headers.'], 401);
        }

        if (! $this->isRequestTimeFresh($request->header('Request-Time'))) {
            return response()->json(['message' => 'Request-Time is outside the allowed tolerance window.'], 401);
        }

        try {
            $this->assertValidSignature(
                '/'.ltrim($request->path(), '/'),
                $request->header('Client-Id'),
                $request->header('Request-Time'),
                $request->getContent(),
                $this->extractSignatureValue($request->header('Signature')),
                $this->readPublicKey(),
            );
        } catch (SignatureVerificationException) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        return $next($request);
    }

    protected function isRequestTimeFresh(string $requestTime): bool
    {
        try {
            $parsed = Carbon::parse($requestTime);
        } catch (\Throwable) {
            return false;
        }

        return abs($parsed->diffInSeconds(now())) <= self::FRESHNESS_TOLERANCE_MINUTES * 60;
    }
}
