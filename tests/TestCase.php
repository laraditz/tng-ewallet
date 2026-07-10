<?php

namespace Laraditz\TngEwallet\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laraditz\TngEwallet\TngEwalletServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            TngEwalletServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        // Test-only: the real provider now publishes migrations (matching
        // laraditz/xendit's convention) rather than auto-loading them via
        // loadMigrationsFrom(), so the test suite needs its own path to the
        // package's migrations — same pattern xendit's own TestCase uses.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
