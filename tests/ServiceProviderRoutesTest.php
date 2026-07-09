<?php

use Illuminate\Support\Facades\Route;

test('the notify route is registered once the provider boots, without the consuming app defining it', function () {
    $route = collect(Route::getRoutes())->first(
        fn ($route) => $route->uri() === 'tng-ewallet/notify' && in_array('POST', $route->methods())
    );

    expect($route)->not->toBeNull();
});
