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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Unique identifier for the section
            $table->string('type')->default('content'); // content, hero, features, testimonials, etc.
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable();
            $table->json('images')->nullable(); // Multiple images
            $table->json('data')->nullable(); // Flexible JSON data for section-specific content
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->string('locale', 10)->default('en');
            $table->string('page')->nullable(); // Which page this section belongs to (home, about, etc.)
            $table->timestamps();
            
            $table->unique(['name', 'locale', 'page']);
            $table->index(['is_visible', 'order']);
            $table->index(['page', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
