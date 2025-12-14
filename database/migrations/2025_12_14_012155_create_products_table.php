<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Multi-language support
            $table->json('description')->nullable(); // Multi-language support
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // Original price for comparison
            $table->integer('stock_quantity')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('in_stock')->default(true);
            $table->string('image')->nullable(); // Main product image
            $table->json('images')->nullable(); // Additional product images
            $table->enum('status', ['draft', 'active', 'inactive', 'out_of_stock'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index('company_id');
            $table->index('category_id');
            $table->index('branch_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('sort_order');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
