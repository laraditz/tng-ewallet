<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\PaymentStatus;
use Laraditz\TngEwallet\Enums\ResultStatus;

class Payment extends Model
{
    protected $table = 'tng_ewallet_payments';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'result_status' => ResultStatus::class,
            'payment_time' => 'datetime',
            'auth_expiry_time' => 'datetime',
            'notified_at' => 'datetime',
            'raw_pay_response' => 'array',
            'raw_notify_payload' => 'array',
        ];
    }
}
