<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tng_ewallet_refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_id')->nullable();
            $table->string('refund_request_id')->unique();
            $table->string('payment_id')->nullable()->index();
            $table->string('payment_request_id')->nullable()->index();

            $table->string('refund_status');
            $table->string('result_status')->nullable();
            $table->string('result_code')->nullable();

            $table->string('refund_amount_currency')->nullable();
            $table->string('refund_amount_value')->nullable();
            $table->string('refund_reason')->nullable();
            $table->string('refund_fail_reason')->nullable();
            $table->dateTime('refund_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tng_ewallet_refunds');
    }
};
