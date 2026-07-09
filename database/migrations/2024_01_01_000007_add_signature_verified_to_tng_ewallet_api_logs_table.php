<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tng_ewallet_api_logs', function (Blueprint $table) {
            $table->boolean('signature_verified')->nullable()->after('http_status');
        });
    }

    public function down(): void
    {
        Schema::table('tng_ewallet_api_logs', function (Blueprint $table) {
            $table->dropColumn('signature_verified');
        });
    }
};
