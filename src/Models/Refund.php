<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\RefundStatus;
use Laraditz\TngEwallet\Enums\ResultStatus;

class Refund extends Model
{
    protected $table = 'tng_ewallet_refunds';

    protected $fillable = [
        'refund_id', 'refund_request_id', 'payment_id', 'payment_request_id',
        'refund_status', 'result_status', 'result_code',
        'refund_amount_currency', 'refund_amount_value',
        'refund_reason', 'refund_fail_reason', 'refund_time',
    ];

    protected $casts = [
        'refund_status' => RefundStatus::class,
        'result_status' => ResultStatus::class,
        'refund_time' => 'datetime',
    ];
}
