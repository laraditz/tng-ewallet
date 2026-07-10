<?php

namespace Laraditz\TngEwallet\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Laraditz\TngEwallet\Exceptions\ConfigurationException;

class TngEncrypted implements CastsAttributes
{
    protected const CIPHER = 'aes-256-cbc';

    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return $this->encrypter()->decryptString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return $this->encrypter()->encryptString($value);
    }

    protected function encrypter(): Encrypter
    {
        $key = config('tng-ewallet.encryption_key');

        if (empty($key)) {
            throw new ConfigurationException('The "tng-ewallet.encryption_key" config value is required but missing.');
        }

        $rawKey = str_starts_with($key, 'base64:') ? base64_decode(substr($key, 7)) : $key;

        return new Encrypter($rawKey, self::CIPHER);
    }
}
