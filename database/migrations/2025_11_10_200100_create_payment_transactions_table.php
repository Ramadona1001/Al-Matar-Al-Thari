<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->unsignedBigInteger('payment_gateway_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->string('type')->default('payment'); // payment, refund, payout, subscription
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled, refunded, disputed
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('payment_method')->nullable();
            $table->json('payer_info')->nullable();
            $table->json('recipient_info')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();

            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('set null');

            $table->index('transaction_id');
            $table->index('type');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['affiliate_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
};