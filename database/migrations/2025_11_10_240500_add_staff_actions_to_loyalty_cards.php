<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loyalty_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_actions_count')->default(0)->after('rewards_redeemed_count');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_cards', function (Blueprint $table) {
            $table->dropColumn('staff_actions_count');
        });
    }
};

