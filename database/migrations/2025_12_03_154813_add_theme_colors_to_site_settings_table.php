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
            // Theme Colors - مستوحاة من الشعار
            // Primary: Dark Forest Green (اللون الأخضر الداكن)
            $table->string('theme_primary_color', 20)->default('#1B4332')->after('secondary_color');
            // Secondary: Golden/Brown (اللون الذهبي/البني)
            $table->string('theme_secondary_color', 20)->default('#D4AF37')->after('theme_primary_color');
            // Accent Color (لون التمييز)
            $table->string('theme_accent_color', 20)->nullable()->after('theme_secondary_color');
            
            // Gradient Colors (ألوان التدرج)
            $table->string('gradient_start_color', 20)->default('#1B4332')->after('theme_accent_color');
            $table->string('gradient_end_color', 20)->default('#2D5016')->after('gradient_start_color');
            
            // Text Colors (ألوان النصوص)
            $table->string('text_primary_color', 20)->default('#1B4332')->after('gradient_end_color');
            $table->string('text_secondary_color', 20)->default('#6C757D')->after('text_primary_color');
            $table->string('text_on_primary_color', 20)->default('#FFFFFF')->after('text_secondary_color');
            
            // Background Colors (ألوان الخلفيات)
            $table->string('bg_primary_color', 20)->default('#FFFFFF')->after('text_on_primary_color');
            $table->string('bg_secondary_color', 20)->default('#F8F9FA')->after('bg_primary_color');
            $table->string('bg_dark_color', 20)->default('#1B4332')->after('bg_secondary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'theme_primary_color',
                'theme_secondary_color',
                'theme_accent_color',
                'gradient_start_color',
                'gradient_end_color',
                'text_primary_color',
                'text_secondary_color',
                'text_on_primary_color',
                'bg_primary_color',
                'bg_secondary_color',
                'bg_dark_color',
            ]);
        });
    }
};
