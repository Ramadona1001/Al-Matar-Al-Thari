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
        Schema::table('pages', function (Blueprint $table) {
            // SEO fields
            $table->string('meta_title')->nullable()->after('is_published');
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Additional content
            $table->string('featured_image')->nullable();
            $table->text('excerpt')->nullable();
            
            // Template and layout
            $table->string('template')->nullable()->default('default'); // Template name
            $table->json('sections')->nullable(); // Associated sections
            
            // Visibility and ordering
            $table->integer('order')->default(0);
            $table->boolean('show_in_menu')->default(false);
            $table->string('menu_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'featured_image',
                'excerpt',
                'template',
                'sections',
                'order',
                'show_in_menu',
                'menu_label',
            ]);
        });
    }
};
