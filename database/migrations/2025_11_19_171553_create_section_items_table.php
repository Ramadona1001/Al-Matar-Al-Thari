<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link')->nullable();
            $table->string('link_text')->nullable();
            $table->json('metadata')->nullable(); // Flexible data (colors, badges, etc.)
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('locale', 10)->default('en');
            $table->timestamps();
            
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->index(['section_id', 'is_active', 'order']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_items');
    }
};
