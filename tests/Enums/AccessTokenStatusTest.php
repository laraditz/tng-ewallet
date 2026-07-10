<?php

use Laraditz\TngEwallet\Enums\AccessTokenStatus;

test('access token status enum has the correct backing values', function () {
    expect(AccessTokenStatus::Active->value)->toBe('active')
        ->and(AccessTokenStatus::Cancelled->value)->toBe('cancelled')
        ->and(AccessTokenStatus::Expired->value)->toBe('expired');
});
