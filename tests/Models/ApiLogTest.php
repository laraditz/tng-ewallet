<?php

use Laraditz\TngEwallet\Enums\ResultStatus;
use Laraditz\TngEwallet\Models\ApiLog;

test('an api log row can be created with json and enum casts', function () {
    $log = ApiLog::create([
        'endpoint' => '/v1/payments/pay',
        'request_payload' => ['paymentRequestId' => 'pr-1'],
        'response_payload' => ['result' => ['resultStatus' => 'A']],
        'result_status' => ResultStatus::Accepted->value,
    ]);

    expect($log->fresh())
        ->request_payload->toBe(['paymentRequestId' => 'pr-1'])
        ->response_payload->toBe(['result' => ['resultStatus' => 'A']])
        ->result_status->toBe(ResultStatus::Accepted);
});
