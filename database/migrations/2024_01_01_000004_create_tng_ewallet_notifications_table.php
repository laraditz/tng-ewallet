<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tng_ewallet_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable()->index();
            $table->string('payment_request_id')->nullable()->index();
            $table->string('customer_id')->nullable()->index();

            $table->string('result_status')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_message')->nullable();

            $table->string('payment_amount_currency')->nullable();
            $table->string('payment_amount_value')->nullable();
            $table->dateTime('payment_time')->nullable();
            $table->string('payment_fail_reason')->nullable();
            $table->text('extend_info')->nullable();

            $table->boolean('signature_verified');
            $table->json('raw_payload');
            $table->dateTime('ack_sent_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tng_ewallet_notifications');
    }
};
