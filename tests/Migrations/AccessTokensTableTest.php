<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('tng_ewallet_access_tokens table exists with the documented columns', function () {
    expect(Schema::hasTable('tng_ewallet_access_tokens'))->toBeTrue();

    expect(Schema::hasColumns('tng_ewallet_access_tokens', [
        'id', 'customer_id', 'reference_client_id',
        'access_token', 'access_token_expiry_time',
        'refresh_token', 'refresh_token_expiry_time',
        'grant_type', 'status', 'cancelled_at',
        'result_status', 'result_code',
        'created_at', 'updated_at',
    ]))->toBeTrue();
});

test('access_token column is indexed for exact-value lookup', function () {
    $indexes = collect(DB::select("PRAGMA index_list('tng_ewallet_access_tokens')"));

    $hasAccessTokenIndex = $indexes->contains(function ($index) {
        $columns = collect(DB::select("PRAGMA index_info('{$index->name}')"));

        return $columns->contains('name', 'access_token');
    });

    expect($hasAccessTokenIndex)->toBeTrue();
});
