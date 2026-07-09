<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tng_ewallet_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->nullable()->index();
            $table->string('reference_client_id')->nullable();

            // Stored as plaintext (not encrypted) so cancelToken()/user() can
            // look up a row by exact token value — Laravel's encrypted cast
            // is non-deterministic and cannot support this lookup.
            $table->string('access_token')->index();
            $table->dateTime('access_token_expiry_time')->nullable();

            $table->string('refresh_token')->nullable();
            $table->dateTime('refresh_token_expiry_time')->nullable();

            $table->string('grant_type');
            $table->string('status');
            $table->dateTime('cancelled_at')->nullable();

            $table->string('result_status')->nullable();
            $table->string('result_code')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tng_ewallet_access_tokens');
    }
};
