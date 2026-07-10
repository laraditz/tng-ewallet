<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\ResultStatus;

class Notification extends Model
{
    protected $table = 'tng_ewallet_notifications';

    protected $fillable = [
        'payment_id', 'payment_request_id', 'customer_id',
        'result_status', 'result_code', 'result_message',
        'payment_amount_currency', 'payment_amount_value', 'payment_time',
        'payment_fail_reason', 'extend_info',
        'signature_verified', 'raw_payload', 'ack_sent_at',
    ];

    protected $casts = [
        'result_status' => ResultStatus::class,
        'payment_time' => 'datetime',
        'signature_verified' => 'boolean',
        'raw_payload' => 'array',
        'ack_sent_at' => 'datetime',
    ];
}
