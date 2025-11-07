<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->string('usage_code')->unique();
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('original_amount', 10, 2);
            $table->decimal('final_amount', 10, 2);
            $table->enum('status', ['used', 'expired', 'invalid'])->default('used');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->dateTime('used_at');
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index('usage_code');
            $table->index('coupon_id');
            $table->index('user_id');
            $table->index('company_id');
            $table->index('used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usage');
    }
};