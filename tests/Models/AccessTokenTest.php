<?php

use Laraditz\TngEwallet\Enums\AccessTokenStatus;
use Laraditz\TngEwallet\Models\AccessToken;

test('an access token row can be created and casts status to an enum', function () {
    $token = AccessToken::create([
        'access_token' => 'tok_abc',
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => AccessTokenStatus::Active->value,
    ]);

    expect($token->fresh())
        ->access_token->toBe('tok_abc')
        ->status->toBe(AccessTokenStatus::Active);
});
