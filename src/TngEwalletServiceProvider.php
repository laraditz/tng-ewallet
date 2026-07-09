<?php

namespace Laraditz\TngEwallet;

use Illuminate\Support\ServiceProvider;

class TngEwalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tng-ewallet.php', 'tng-ewallet');
    }

    public function boot(): void
    {
        //
    }
}
