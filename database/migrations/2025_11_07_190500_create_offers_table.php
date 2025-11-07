<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Multi-language support
            $table->json('description'); // Multi-language support
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('type')->enum(['discount', 'coupon', 'loyalty', 'affiliate']);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->integer('max_usage_per_user')->default(1);
            $table->integer('total_usage_limit')->nullable();
            $table->integer('current_usage_count')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['draft', 'active', 'expired', 'paused'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index('company_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};