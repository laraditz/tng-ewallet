<?php

use Laraditz\TngEwallet\Models\AccessToken;
use Laraditz\TngEwallet\Models\TngUser;

test('a tng user row can be created with a json user_info cast', function () {
    $user = TngUser::create([
        'user_id' => 'user-1',
        'user_info' => ['userId' => 'user-1'],
        'last_fetched_at' => now(),
    ]);

    expect($user->fresh()->user_info)->toBe(['userId' => 'user-1']);
});

test('a tng user resolves its linked access token relation', function () {
    $token = AccessToken::create([
        'access_token' => 'tok_xyz',
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
    ]);

    $user = TngUser::create([
        'user_id' => 'user-2',
        'access_token_id' => $token->id,
        'user_info' => ['userId' => 'user-2'],
        'last_fetched_at' => now(),
    ]);

    expect($user->accessToken)->not->toBeNull()
        ->and($user->accessToken->is($token))->toBeTrue();
});
