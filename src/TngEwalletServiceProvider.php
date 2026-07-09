<?php

namespace Laraditz\TngEwallet;

use Illuminate\Support\ServiceProvider;
use Laraditz\TngEwallet\Client\TngClient;

class TngEwalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tng-ewallet.php', 'tng-ewallet');

        $this->app->singleton(TngClient::class);

        $this->app->singleton('tng-ewallet', fn ($app) => new TngEwallet($app->make(TngClient::class)));
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
