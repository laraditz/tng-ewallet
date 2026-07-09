<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // request_payload/response_payload now hold encrypted ciphertext (base64
        // text), not raw JSON. A json column type rejects that on MySQL/Postgres,
        // which enforce valid JSON content — so these are recreated as longText.
        // Uses drop+add (not ->change()) to avoid a doctrine/dbal dependency,
        // since this package supports Laravel 10 without requiring it.
        Schema::table('tng_ewallet_api_logs', function (Blueprint $table) {
            $table->dropColumn(['request_payload', 'response_payload']);
        });

        Schema::table('tng_ewallet_api_logs', function (Blueprint $table) {
            // Nullable so SQLite/MySQL/Postgres all allow adding the column
            // back on an existing (possibly non-empty) table without a
            // default value — request_payload is always populated by
            // TngClient in practice.
            $table->longText('request_payload')->nullable()->after('reference_id');
            $table->longText('response_payload')->nullable()->after('request_payload');
        });
    }

    public function down(): void
    {
        Schema::table('tng_ewallet_api_logs', function (Blueprint $table) {
            $table->dropColumn(['request_payload', 'response_payload']);
        });

        Schema::table('tng_ewallet_api_logs', function (Blueprint $table) {
            $table->json('request_payload')->nullable()->after('reference_id');
            $table->json('response_payload')->nullable()->after('request_payload');
        });
    }
};
