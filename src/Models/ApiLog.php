<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\ResultStatus;

class ApiLog extends Model
{
    protected $table = 'tng_ewallet_api_logs';

    protected $fillable = [
        'endpoint', 'reference_id',
        'request_payload', 'response_payload', 'http_status', 'signature_verified',
        'result_status', 'result_code', 'result_message',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'encrypted:array',
            'response_payload' => 'encrypted:array',
            'result_status' => ResultStatus::class,
            'signature_verified' => 'boolean',
        ];
    }
}
