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

            // access_token is encrypted at rest, so it can't be looked up with an
            // exact-match WHERE clause (Laravel's encrypted cast is
            // non-deterministic). access_token_hash — a deterministic
            // HMAC-SHA256 keyed on APP_KEY — is what cancelToken()/user()
            // actually query against.
            $table->text('access_token');
            $table->string('access_token_hash')->nullable()->index();
            $table->dateTime('access_token_expiry_time')->nullable();

            $table->text('refresh_token')->nullable();
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
