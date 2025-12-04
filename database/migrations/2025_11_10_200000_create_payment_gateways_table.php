<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // stripe, paypal, razorpay, etc.
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('public_key')->nullable();
            $table->string('private_key')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->boolean('test_mode')->default(true);
            $table->boolean('status')->default(false);
            $table->json('settings')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->json('supported_countries')->nullable();
            $table->json('processing_fees')->nullable();
            $table->decimal('minimum_amount', 10, 2)->default(0);
            $table->decimal('maximum_amount', 10, 2)->default(999999.99);
            $table->string('processing_time')->default('1-3 business days');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('test_mode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
};