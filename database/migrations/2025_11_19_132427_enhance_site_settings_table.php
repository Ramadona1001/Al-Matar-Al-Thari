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
        Schema::table('site_settings', function (Blueprint $table) {
            // SEO fields
            $table->string('meta_title')->nullable()->after('hero_subtitle');
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Social media links
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();
            
            // Footer content
            $table->text('footer_text')->nullable();
            $table->text('footer_copyright')->nullable();
            
            // Additional settings
            $table->json('social_links')->nullable(); // Flexible social links
            $table->json('footer_links')->nullable(); // Footer menu links
            $table->json('additional_settings')->nullable(); // Any other flexible settings
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'facebook_url',
                'twitter_url',
                'instagram_url',
                'linkedin_url',
                'youtube_url',
                'tiktok_url',
                'footer_text',
                'footer_copyright',
                'social_links',
                'footer_links',
                'additional_settings',
            ]);
        });
    }
};
