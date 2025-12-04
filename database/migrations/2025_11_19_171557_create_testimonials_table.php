<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('company')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('rating')->default(5); // 1-5 stars
            $table->text('testimonial');
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('locale', 10)->default('en');
            $table->timestamps();
            
            $table->index(['is_active', 'is_featured', 'order']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
