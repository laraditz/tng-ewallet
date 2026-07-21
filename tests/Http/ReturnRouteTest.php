<?php

use Illuminate\Support\Facades\Route;

test('the return route is registered as GET at the configured path, with no middleware', function () {
    config(['tng-ewallet.return_path' => '/tng-ewallet/return']);

    $route = collect(Route::getRoutes())->first(
        fn ($route) => $route->uri() === 'tng-ewallet/return' && in_array('GET', $route->methods())
    );

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toBe([]);
});
