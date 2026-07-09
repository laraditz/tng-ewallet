<?php

use Illuminate\Support\Facades\DB;
use Laraditz\TngEwallet\Models\AccessToken;

test('refresh_token is encrypted at rest, unlike access_token which must stay plaintext for lookups', function () {
    $token = AccessToken::create([
        'access_token' => 'tok_lookup_key',
        'refresh_token' => 'refresh_secret_value',
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
    ]);

    $rawRow = DB::table('tng_ewallet_access_tokens')->find($token->id);

    expect($rawRow->access_token)->toBe('tok_lookup_key')
        ->and($rawRow->refresh_token)->not->toBe('refresh_secret_value');

    expect($token->fresh()->refresh_token)->toBe('refresh_secret_value');
});
