<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing about sections to have 3 columns per row
        DB::table('sections')
            ->where('page', 'about')
            ->whereIn('name', ['our_mission', 'our_vision', 'our_message'])
            ->update(['columns_per_row' => 3]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to default (1 column per row)
        DB::table('sections')
            ->where('page', 'about')
            ->whereIn('name', ['our_mission', 'our_vision', 'our_message'])
            ->update(['columns_per_row' => 1]);
    }
};
