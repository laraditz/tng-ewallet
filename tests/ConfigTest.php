<?php

test('the package config is merged with the documented sandbox url', function () {
    expect(config('tng-ewallet.sandbox_url'))->toBe('https://api-sd.tngdigital.com.my');
});

test('the package config has the documented acl/api path prefix', function () {
    expect(config('tng-ewallet.api_path'))->toBe('/acl/api');
});

test('the package config has the documented return_path default', function () {
    expect(config('tng-ewallet.return_path'))->toBe('/tng-ewallet/return');
});

test('the package config default_return_url falls back to app.url', function () {
    expect(config('tng-ewallet.default_return_url'))->toBe(config('app.url'));
});
