<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // refresh_token now holds encrypted ciphertext (IV + MAC + base64
        // overhead), which exceeds a 255-char string column. Widened to text.
        // Drop+add (not ->change()) to avoid a doctrine/dbal dependency.
        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->dropColumn('refresh_token');
        });

        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->text('refresh_token')->nullable()->after('access_token_expiry_time');
        });
    }

    public function down(): void
    {
        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->dropColumn('refresh_token');
        });

        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->string('refresh_token')->nullable()->after('access_token_expiry_time');
        });
    }
};
