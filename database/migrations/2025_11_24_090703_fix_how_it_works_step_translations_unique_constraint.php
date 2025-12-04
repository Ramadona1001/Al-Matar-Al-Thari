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
        // Check if the constraint with short name already exists
        $indexes = \DB::select("SHOW INDEX FROM how_it_works_step_translations WHERE Key_name = 'hiws_step_id_locale_unique'");
        
        if (empty($indexes)) {
            // Check for old long-named constraint
            $oldIndexes = \DB::select("SHOW INDEX FROM how_it_works_step_translations WHERE Key_name LIKE '%how_it_works_step_id%locale%unique%'");
            
            // Drop old constraint if exists
            foreach ($oldIndexes as $index) {
                try {
                    \DB::statement("ALTER TABLE how_it_works_step_translations DROP INDEX `{$index->Key_name}`");
                } catch (\Exception $e) {
                    // Ignore if doesn't exist
                }
            }
            
            // Add new constraint with shorter name
            Schema::table('how_it_works_step_translations', function (Blueprint $table) {
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
        Schema::table('how_it_works_step_translations', function (Blueprint $table) {
            $table->dropUnique('hiws_step_id_locale_unique');
        });
        
        Schema::table('how_it_works_step_translations', function (Blueprint $table) {
            $table->unique(['how_it_works_step_id', 'locale']);
        });
    }
};
