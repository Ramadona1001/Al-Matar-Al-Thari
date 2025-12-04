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
        // Add og_image to blogs
        if (Schema::hasTable('blogs') && !Schema::hasColumn('blogs', 'og_image')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->string('og_image')->nullable()->after('featured_image');
            });
        }

        // Add og_image to pages
        if (Schema::hasTable('pages') && !Schema::hasColumn('pages', 'og_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('og_image')->nullable()->after('featured_image');
            });
        }

        // Add og_image to services
        if (Schema::hasTable('services') && !Schema::hasColumn('services', 'og_image')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('og_image')->nullable()->after('image_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('blogs') && Schema::hasColumn('blogs', 'og_image')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn('og_image');
            });
        }

        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'og_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('og_image');
            });
        }

        if (Schema::hasTable('services') && Schema::hasColumn('services', 'og_image')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('og_image');
            });
        }
    }
};
