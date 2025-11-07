<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code')->unique();
            $table->string('referral_link')->unique();
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->enum('status', ['active', 'suspended', 'disabled'])->default('active');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('referral_code');
            $table->index('user_id');
            $table->index('company_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};