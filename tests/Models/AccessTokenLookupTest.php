<?php

use Laraditz\TngEwallet\Models\AccessToken;

test('a stored access_token can be looked up by exact value', function () {
    $token = AccessToken::create([
        'access_token' => 'tok_lookup_roundtrip',
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
    ]);

    $found = AccessToken::where('access_token', 'tok_lookup_roundtrip')->first();

    expect($found)->not->toBeNull()
        ->and($found->is($token))->toBeTrue();
});
