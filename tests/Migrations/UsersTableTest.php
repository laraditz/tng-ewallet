<?php

use Illuminate\Support\Facades\Schema;

test('tng_ewallet_users table exists with the documented columns', function () {
    expect(Schema::hasTable('tng_ewallet_users'))->toBeTrue();

    expect(Schema::hasColumns('tng_ewallet_users', [
        'id', 'user_id', 'access_token_id', 'user_info', 'last_fetched_at',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});

test('access_token_id is a nullable foreign key to tng_ewallet_access_tokens', function () {
    $tokenId = \Illuminate\Support\Facades\DB::table('tng_ewallet_access_tokens')->insertGetId([
        'access_token' => 'tok_fixture',
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $userId = \Illuminate\Support\Facades\DB::table('tng_ewallet_users')->insertGetId([
        'user_id' => 'user_fixture',
        'access_token_id' => $tokenId,
        'user_info' => json_encode(['userId' => 'user_fixture']),
        'last_fetched_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(\Illuminate\Support\Facades\DB::table('tng_ewallet_users')->find($userId)->access_token_id)
        ->toBe($tokenId);

    // Nullable — a row with no resolvable access token must also succeed.
    $nullLinkedUserId = \Illuminate\Support\Facades\DB::table('tng_ewallet_users')->insertGetId([
        'user_id' => 'user_fixture_2',
        'access_token_id' => null,
        'user_info' => json_encode(['userId' => 'user_fixture_2']),
        'last_fetched_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(\Illuminate\Support\Facades\DB::table('tng_ewallet_users')->find($nullLinkedUserId)->access_token_id)
        ->toBeNull();
});
