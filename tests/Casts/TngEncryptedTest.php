<?php

use Illuminate\Database\Eloquent\Model;
use Laraditz\TngEwallet\Casts\TngEncrypted;
use Laraditz\TngEwallet\Exceptions\ConfigurationException;

test('set() then get() round-trips the original value', function () {
    $cast = new TngEncrypted();
    $model = new class extends Model {};

    $encrypted = $cast->set($model, 'value', 'plain-secret', []);
    expect($encrypted)->not->toBe('plain-secret');

    $decrypted = $cast->get($model, 'value', $encrypted, []);
    expect($decrypted)->toBe('plain-secret');
});

test('null passes through unchanged for both get() and set()', function () {
    $cast = new TngEncrypted();
    $model = new class extends Model {};

    expect($cast->set($model, 'value', null, []))->toBeNull();
    expect($cast->get($model, 'value', null, []))->toBeNull();
});

test('throws ConfigurationException when encryption_key is empty', function () {
    config(['tng-ewallet.encryption_key' => null]);

    $cast = new TngEncrypted();
    $model = new class extends Model {};

    expect(fn () => $cast->set($model, 'value', 'plain-secret', []))
        ->toThrow(ConfigurationException::class);
});
