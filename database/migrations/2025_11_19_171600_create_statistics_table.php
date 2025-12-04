<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // e.g., "Clients worldwide"
            $table->string('value'); // e.g., "360+"
            $table->string('icon')->nullable();
            $table->string('suffix')->nullable(); // e.g., "MLN", "€", "+"
            $table->string('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('locale', 10)->default('en');
            $table->timestamps();
            
            $table->index(['is_active', 'order']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
