<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\AccessTokenStatus;

class AccessToken extends Model
{
    protected $table = 'tng_ewallet_access_tokens';

    protected $fillable = [
        'customer_id', 'reference_client_id',
        'access_token', 'access_token_expiry_time',
        'refresh_token', 'refresh_token_expiry_time',
        'grant_type', 'status', 'cancelled_at',
        'result_status', 'result_code',
    ];

    protected $casts = [
        'access_token_expiry_time' => 'datetime',
        'refresh_token' => 'encrypted',
        'refresh_token_expiry_time' => 'datetime',
        'status' => AccessTokenStatus::class,
        'cancelled_at' => 'datetime',
    ];
}
