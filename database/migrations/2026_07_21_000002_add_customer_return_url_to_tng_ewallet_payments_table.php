<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tng_ewallet_payments', function (Blueprint $table) {
            $table->text('customer_return_url')->nullable()->after('redirection_url');
        });
    }

    public function down(): void
    {
        Schema::table('tng_ewallet_payments', function (Blueprint $table) {
            $table->dropColumn('customer_return_url');
        });
    }
};
