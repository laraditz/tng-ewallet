<?php

namespace Laraditz\TngEwallet\Models;

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Enums\ResultStatus;

class ApiLog extends Model
{
    protected $table = 'tng_ewallet_api_logs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'result_status' => ResultStatus::class,
        ];
    }
}
