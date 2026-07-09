<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TngUser extends Model
{
    protected $table = 'tng_ewallet_users';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_info' => 'array',
            'last_fetched_at' => 'datetime',
        ];
    }

    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(AccessToken::class, 'access_token_id');
    }
}
