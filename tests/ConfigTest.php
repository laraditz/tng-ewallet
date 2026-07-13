<?php

test('the package config is merged with the documented sandbox url', function () {
    expect(config('tng-ewallet.sandbox_url'))->toBe('https://api-sd.tngdigital.com.my');
});

test('the package config has the documented acl/api path prefix', function () {
    expect(config('tng-ewallet.api_path'))->toBe('/acl/api');
});
