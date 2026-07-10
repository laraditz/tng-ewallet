<?php

use Illuminate\Support\Facades\DB;
use Laraditz\TngEwallet\Models\AccessToken;

test('access_token is encrypted at rest but can still be looked up via access_token_hash', function () {
    $hash = AccessToken::hashToken('tok_secret_value');

    $token = AccessToken::create([
        'access_token' => 'tok_secret_value',
        'access_token_hash' => $hash,
        'grant_type' => 'AUTHORIZATION_CODE',
        'status' => 'active',
    ]);

    $rawRow = DB::table('tng_ewallet_access_tokens')->find($token->id);
    expect($rawRow->access_token)->not->toBe('tok_secret_value');
    expect($rawRow->access_token_hash)->toBe($hash);

    // Transparent decryption via the model.
    expect($token->fresh()->access_token)->toBe('tok_secret_value');

    // Deterministic hash makes exact-value lookup possible again.
    $found = AccessToken::where('access_token_hash', AccessToken::hashToken('tok_secret_value'))->first();
    expect($found)->not->toBeNull()
        ->and($found->is($token))->toBeTrue();
});

test('hashToken produces the same hash for the same input and different hashes for different input', function () {
    expect(AccessToken::hashToken('same-value'))->toBe(AccessToken::hashToken('same-value'))
        ->and(AccessToken::hashToken('value-a'))->not->toBe(AccessToken::hashToken('value-b'));
});
