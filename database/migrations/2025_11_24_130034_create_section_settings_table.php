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
        Schema::create('section_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->boolean('is_active')->default(true);
            $table->json('options')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('section_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_setting_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            
            $table->unique(['section_setting_id', 'locale'], 'ss_section_setting_id_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_setting_translations');
        Schema::dropIfExists('section_settings');
    }
};
