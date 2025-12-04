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
        // Check if label column exists and remove it
        if (Schema::hasColumn('menus', 'label')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('label');
            });
        }
        
        // Also remove locale column if exists (since we're using translations table)
        if (Schema::hasColumn('menus', 'locale')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('locale');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('label')->after('name');
            $table->string('locale', 10)->default('en')->after('label');
        });
    }
};
