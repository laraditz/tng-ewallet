<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tng_ewallet_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();

            $table->foreignId('access_token_id')
                ->nullable()
                ->constrained('tng_ewallet_access_tokens')
                ->nullOnDelete();

            $table->json('user_info');
            $table->dateTime('last_fetched_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tng_ewallet_users');
    }
};
