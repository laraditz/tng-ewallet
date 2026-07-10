<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // access_token is now encrypted (ciphertext isn't exact-match
        // queryable), so the plaintext index and the plaintext-sized string
        // column are both replaced: access_token widens to text, and a new
        // deterministic access_token_hash column takes over the lookup role
        // cancelToken()/user() need.
        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->dropIndex('tng_ewallet_access_tokens_access_token_index');
            $table->dropColumn('access_token');
        });

        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->text('access_token')->after('reference_client_id');
            $table->string('access_token_hash')->nullable()->index()->after('access_token');
        });
    }

    public function down(): void
    {
        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['access_token', 'access_token_hash']);
        });

        Schema::table('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->string('access_token')->after('reference_client_id')->index();
        });
    }
};
