<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loyalty_points', function (Blueprint $table) {
            // Add foreign key constraints for existing loyalty_card_id and club_id columns
            // after loyalty_cards and clubs tables are created
            if (Schema::hasTable('loyalty_cards')) {
                $table->foreign('loyalty_card_id')->references('id')->on('loyalty_cards')->onDelete('set null');
            }
            if (Schema::hasTable('clubs')) {
                $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->dropForeign(['loyalty_card_id']);
            $table->dropForeign(['club_id']);
        });
    }
};

