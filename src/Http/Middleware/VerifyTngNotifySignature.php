<?php

namespace Laraditz\TngEwallet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laraditz\TngEwallet\Client\Concerns\VerifiesResponseSignature;
use Laraditz\TngEwallet\Exceptions\SignatureVerificationException;

class VerifyTngNotifySignature
{
    use VerifiesResponseSignature;

    public function handle(Request $request, Closure $next)
    {
        try {
            $this->assertValidSignature(
                '/'.ltrim($request->path(), '/'),
                $request->header('Client-Id'),
                $request->header('Request-Time'),
                $request->getContent(),
                $this->extractSignatureValue($request->header('Signature')),
                file_get_contents(config('tng-ewallet.public_key_path')),
            );
        } catch (SignatureVerificationException) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        return $next($request);
    }

    protected function extractSignatureValue(string $signatureHeader): string
    {
        preg_match('/signature=(.+)$/', $signatureHeader, $matches);

        return $matches[1] ?? '';
    }
}
