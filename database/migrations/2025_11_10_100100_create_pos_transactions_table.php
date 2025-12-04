<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->unsignedBigInteger('pos_device_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('digital_card_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->string('payment_method', 50);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('pos_transaction_id')->unique();
            $table->json('items')->nullable();
            $table->json('receipt_data')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->foreign('pos_device_id')->references('id')->on('pos_devices')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->foreign('digital_card_id')->references('id')->on('digital_cards')->onDelete('set null');
            $table->index(['company_id', 'status']);
            $table->index(['pos_device_id', 'created_at']);
            $table->index('transaction_id');
            $table->index('pos_transaction_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_transactions');
    }
};