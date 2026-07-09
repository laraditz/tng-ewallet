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
}
