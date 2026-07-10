<?php

use Illuminate\Support\Facades\Artisan;

test('tng-ewallet:generate-key outputs a valid base64 32-byte key', function () {
    Artisan::call('tng-ewallet:generate-key');

    $output = Artisan::output();

    expect($output)->toContain('base64:');

    preg_match('/base64:([A-Za-z0-9+\/=]+)/', $output, $matches);
    $decoded = base64_decode($matches[1]);

    expect(strlen($decoded))->toBe(32);
});
