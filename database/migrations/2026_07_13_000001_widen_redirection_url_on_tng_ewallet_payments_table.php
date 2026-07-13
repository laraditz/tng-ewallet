<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Raw SQL for mysql/pgsql instead of Blueprint::change() — that method
     * requires doctrine/dbal on Laravel 10, which this package doesn't
     * depend on. The Blueprint fallback below only ever runs on sqlite in
     * this package's own test suite.
     */
    public function up(): void
    {
        match (Schema::getConnection()->getDriverName()) {
            'mysql' => DB::statement('ALTER TABLE tng_ewallet_payments MODIFY redirection_url TEXT NULL'),
            'pgsql' => DB::statement('ALTER TABLE tng_ewallet_payments ALTER COLUMN redirection_url TYPE TEXT'),
            default => Schema::table('tng_ewallet_payments', fn (Blueprint $table) => $table->text('redirection_url')->nullable()->change()),
        };
    }

    public function down(): void
    {
        match (Schema::getConnection()->getDriverName()) {
            'mysql' => DB::statement('ALTER TABLE tng_ewallet_payments MODIFY redirection_url VARCHAR(255) NULL'),
            'pgsql' => DB::statement('ALTER TABLE tng_ewallet_payments ALTER COLUMN redirection_url TYPE VARCHAR(255)'),
            default => Schema::table('tng_ewallet_payments', fn (Blueprint $table) => $table->string('redirection_url')->nullable()->change()),
        };
    }
};
