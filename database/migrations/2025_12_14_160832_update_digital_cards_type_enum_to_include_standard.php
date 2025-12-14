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
        // MySQL doesn't support modifying ENUM directly, so we need to use raw SQL
        DB::statement("ALTER TABLE digital_cards MODIFY COLUMN type ENUM('silver', 'gold', 'platinum', 'standard') DEFAULT 'standard'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any 'standard' cards to 'silver'
        DB::table('digital_cards')
            ->where('type', 'standard')
            ->update(['type' => 'silver']);
        
        // Then revert the enum back to original
        DB::statement("ALTER TABLE digital_cards MODIFY COLUMN type ENUM('silver', 'gold', 'platinum') DEFAULT 'silver'");
    }
};
