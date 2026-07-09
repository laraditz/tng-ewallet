<?php

test('the package config is merged with the documented sandbox url', function () {
    expect(config('tng-ewallet.sandbox_url'))->toBe('https://api-sd.tngdigital.com.my');
});
