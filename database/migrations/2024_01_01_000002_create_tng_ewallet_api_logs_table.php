<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tng_ewallet_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('reference_id')->nullable()->index();

            $table->json('request_payload');
            $table->json('response_payload')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();

            $table->string('result_status')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_message')->nullable();

            $table->unsignedInteger('duration_ms')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tng_ewallet_api_logs');
    }
};
