<?php

use Laraditz\TngEwallet\Enums\ResultStatus;

test('result status enum has the correct backing values', function () {
    expect(ResultStatus::Success->value)->toBe('S')
        ->and(ResultStatus::Failed->value)->toBe('F')
        ->and(ResultStatus::Unknown->value)->toBe('U')
        ->and(ResultStatus::Accepted->value)->toBe('A');
});

test('result status can be constructed from a raw string', function () {
    expect(ResultStatus::from('S'))->toBe(ResultStatus::Success);
});
