<?php

namespace Laraditz\TngEwallet\Tests;

use Laraditz\TngEwallet\TngEwalletServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TngEwalletServiceProvider::class,
        ];
    }
}
