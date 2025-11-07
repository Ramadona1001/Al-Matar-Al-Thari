<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('qr_code')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->enum('type', ['percentage', 'fixed', 'free_shipping', 'buy_x_get_y']);
            $table->decimal('value', 10, 2);
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->integer('max_usage_per_user')->default(1);
            $table->integer('total_usage_limit')->nullable();
            $table->integer('current_usage_count')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['active', 'used', 'expired', 'disabled'])->default('active');
            $table->boolean('is_public')->default(false);
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('code');
            $table->index('qr_code');
            $table->index('offer_id');
            $table->index('company_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};