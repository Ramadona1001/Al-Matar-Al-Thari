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
        if (!Schema::hasTable('how_it_works_step_translations')) {
            Schema::create('how_it_works_step_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('how_it_works_step_id')->constrained()->onDelete('cascade');
                $table->string('locale')->index();
                $table->string('title');
                $table->text('description')->nullable();
                
                $table->unique(['how_it_works_step_id', 'locale'], 'hiws_step_id_locale_unique');
            });
        } else {
            // Table exists, check if unique constraint needs to be fixed
            $indexes = \DB::select("SHOW INDEX FROM how_it_works_step_translations WHERE Key_name LIKE '%unique%'");
            
            // Drop all existing unique constraints on these columns
            foreach ($indexes as $index) {
                if (in_array('how_it_works_step_id', [$index->Column_name]) || in_array('locale', [$index->Column_name])) {
                    try {
                        \DB::statement("ALTER TABLE how_it_works_step_translations DROP INDEX `{$index->Key_name}`");
                    } catch (\Exception $e) {
                        // Ignore if doesn't exist
                    }
                }
            }
            
            // Add new unique constraint with shorter name
            Schema::table('how_it_works_step_translations', function (Blueprint $table) {
                if (!\Schema::hasColumn('how_it_works_step_translations', 'id')) {
                    return; // Table structure is wrong
                }
                try {
                    $table->unique(['how_it_works_step_id', 'locale'], 'hiws_step_id_locale_unique');
                } catch (\Exception $e) {
                    // Might already exist
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('how_it_works_step_translations');
    }
};
