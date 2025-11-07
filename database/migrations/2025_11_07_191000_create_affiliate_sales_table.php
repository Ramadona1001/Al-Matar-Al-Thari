<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_sales', function (Blueprint $table) {
            $table->id();
            $table->decimal('sale_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index('affiliate_id');
            $table->index('user_id');
            $table->index('company_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_sales');
    }
};