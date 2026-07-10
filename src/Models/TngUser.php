<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TngUser extends Model
{
    protected $table = 'tng_ewallet_users';

    protected $fillable = [
        'user_id', 'access_token_id', 'user_info', 'last_fetched_at',
    ];

    protected $casts = [
        'user_info' => 'array',
        'last_fetched_at' => 'datetime',
    ];

    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(AccessToken::class, 'access_token_id');
    }
}
