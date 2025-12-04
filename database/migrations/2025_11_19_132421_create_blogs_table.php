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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('author_name')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->date('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('locale', 10)->default('en');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('views')->default(0);
            $table->json('tags')->nullable();
            $table->json('categories')->nullable();
            $table->timestamps();
            
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['is_published', 'published_at']);
            $table->index(['is_featured', 'is_published']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
