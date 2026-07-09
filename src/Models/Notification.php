<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\ResultStatus;

class Notification extends Model
{
    protected $table = 'tng_ewallet_notifications';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'result_status' => ResultStatus::class,
            'payment_time' => 'datetime',
            'signature_verified' => 'boolean',
            'raw_payload' => 'array',
            'ack_sent_at' => 'datetime',
        ];
    }
}
