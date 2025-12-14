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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Set NULL values to 0 before making it non-nullable
            DB::statement("UPDATE products SET stock_quantity = 0 WHERE stock_quantity IS NULL");
            $table->integer('stock_quantity')->default(0)->nullable(false)->change();
        });
    }
};
