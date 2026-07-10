<?php

use Illuminate\Support\Facades\DB;
use Laraditz\TngEwallet\Models\AccessToken;

test('refresh_token is encrypted at rest, same as access_token', function () {
    $token = AccessToken::create([
        'access_token' => 'tok_lookup_key',
        'access_token_hash' => AccessToken::hashToken('tok_lookup_key'),
        'refresh_token' => 'refresh_secret_value',
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
    ]);

    $rawRow = DB::table('tng_ewallet_access_tokens')->find($token->id);

    expect($rawRow->access_token)->not->toBe('tok_lookup_key')
        ->and($rawRow->refresh_token)->not->toBe('refresh_secret_value');

    expect($token->fresh()->access_token)->toBe('tok_lookup_key')
        ->and($token->fresh()->refresh_token)->toBe('refresh_secret_value');
});
