<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\AccessTokenStatus;

class AccessToken extends Model
{
    protected $table = 'tng_ewallet_access_tokens';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'access_token_expiry_time' => 'datetime',
            'refresh_token_expiry_time' => 'datetime',
            'status' => AccessTokenStatus::class,
            'cancelled_at' => 'datetime',
        ];
    }
}
