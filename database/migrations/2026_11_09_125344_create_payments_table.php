<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('checkout_batch')->unique();
            $table->string('order_id')->unique();
            $table->unsignedBigInteger('gross_amount');
            $table->string('status')->default('pending');
            $table->string('midtrans_transaction_id')->nullable()->index();
            $table->string('midtrans_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('fraud_status')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('redirect_url')->nullable();
            $table->json('last_notification')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
