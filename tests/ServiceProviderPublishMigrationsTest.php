<?php

use Laraditz\TngEwallet\TngEwalletServiceProvider;

function makeServiceProviderInstance(): TngEwalletServiceProvider
{
    return new TngEwalletServiceProvider(app());
}

test('every package migration is mapped for publishing when none exist yet in the app', function () {
    $map = makeServiceProviderInstance()->buildMigrationPublishMap();

    $packageMigrationCount = count(glob(__DIR__.'/../database/migrations/*.php'));

    expect($map)->toHaveCount($packageMigrationCount);

    $targetBasenames = array_map(fn ($target) => preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', basename($target)), array_values($map));
    expect($targetBasenames)->toContain('create_tng_ewallet_payments_table.php');
});

test('a migration whose descriptive suffix already exists in the app is not mapped again', function () {
    $migrationsDir = database_path('migrations');
    if (! is_dir($migrationsDir)) {
        mkdir($migrationsDir, 0777, true);
    }

    $existingFile = $migrationsDir.'/2020_01_01_000000_create_tng_ewallet_payments_table.php';
    file_put_contents($existingFile, '<?php // pre-existing, already published');

    try {
        $map = makeServiceProviderInstance()->buildMigrationPublishMap();

        $targetBasenames = array_map(fn ($target) => preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', basename($target)), array_values($map));
        expect($targetBasenames)->not->toContain('create_tng_ewallet_payments_table.php');

        $packageMigrationCount = count(glob(__DIR__.'/../database/migrations/*.php'));
        expect($map)->toHaveCount($packageMigrationCount - 1);
    } finally {
        unlink($existingFile);
    }
});

test('mapped target filenames use a fresh, incrementing timestamp prefix to preserve migration order', function () {
    $map = makeServiceProviderInstance()->buildMigrationPublishMap();

    $prefixes = array_map(fn ($target) => substr(basename($target), 0, 17), array_values($map));

    expect($prefixes)->toBe(collect($prefixes)->unique()->values()->all())
        ->and($prefixes)->toBe(collect($prefixes)->sort()->values()->all());
});
