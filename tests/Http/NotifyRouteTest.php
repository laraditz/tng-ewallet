<?php

use Illuminate\Support\Facades\Route;

test('the notify route is registered at the configured path once the provider boots', function () {
    config(['tng-ewallet.notify_path' => '/tng-ewallet/notify']);

    $route = collect(Route::getRoutes())->first(
        fn ($route) => $route->uri() === 'tng-ewallet/notify' && in_array('POST', $route->methods())
    );

    expect($route)->not->toBeNull();
});
