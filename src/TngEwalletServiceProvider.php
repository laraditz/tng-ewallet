<?php

namespace Laraditz\TngEwallet;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Laraditz\TngEwallet\Client\TngClient;
use Laraditz\TngEwallet\Console\Commands\GenerateEncryptionKeyCommand;

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
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tng-ewallet.php' => config_path('tng-ewallet.php'),
            ], 'tng-ewallet-config');

            $this->publishes($this->buildMigrationPublishMap(), 'tng-ewallet-migrations');

            $this->commands([
                GenerateEncryptionKeyCommand::class,
            ]);
        }
    }

    /**
     * Map each package migration to a fresh, app-side timestamp — skipping
     * any migration whose descriptive suffix (filename minus the date/time
     * prefix) already exists somewhere in the app's own migrations
     * directory, so re-publishing never creates duplicates.
     */
    public function buildMigrationPublishMap(): array
    {
        $packagePath = __DIR__.'/../database/migrations/';
        $appPath = database_path('migrations/');

        $files = array_diff(scandir($packagePath), ['.', '..']);

        $date = date('Y_m_d');
        $time = date('His');

        $map = collect($files)->mapWithKeys(function (string $file) use ($packagePath, $appPath, $date, &$time) {
            $suffix = Str::replace(Str::substr($file, 0, 17), '', $file);

            $alreadyPublished = count(glob($appPath.'*'.$suffix)) > 0;
            $time = date('His', strtotime($time) + 1); // keep relative migration order

            return $alreadyPublished ? [] : [
                $packagePath.$file => $appPath.$date.'_'.$time.$suffix,
            ];
        });

        return $map->toArray();
    }
}
