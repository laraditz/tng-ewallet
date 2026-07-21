<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\PaymentStatus;
use Laraditz\TngEwallet\Enums\ResultStatus;

class Payment extends Model
{
    protected $table = 'tng_ewallet_payments';

    protected $fillable = [
        'payment_id', 'payment_request_id',
        'status', 'result_status', 'result_code', 'payment_fail_reason',
        'currency', 'amount', 'action_form_type', 'redirection_url',
        'payment_time', 'auth_expiry_time', 'notified_at',
        'raw_pay_response', 'raw_notify_payload', 'customer_return_url',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'result_status' => ResultStatus::class,
        'payment_time' => 'datetime',
        'auth_expiry_time' => 'datetime',
        'notified_at' => 'datetime',
        'raw_pay_response' => 'array',
        'raw_notify_payload' => 'array',
    ];
}
