<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tng_ewallet_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable();
            $table->string('payment_request_id')->unique();

            $table->string('status');
            $table->string('result_status')->nullable();
            $table->string('result_code')->nullable();
            $table->string('payment_fail_reason')->nullable();

            $table->string('currency')->nullable();
            $table->string('amount')->nullable();
            $table->string('action_form_type')->nullable();
            $table->string('redirection_url')->nullable();

            $table->dateTime('payment_time')->nullable();
            $table->dateTime('auth_expiry_time')->nullable();
            $table->dateTime('notified_at')->nullable();

            $table->json('raw_pay_response')->nullable();
            $table->json('raw_notify_payload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tng_ewallet_payments');
    }
};
