<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loyalty_cards', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('company_id')->constrained('clubs')->nullOnDelete();
            $table->unsignedBigInteger('views_count')->default(0)->after('status');
            $table->unsignedBigInteger('points_accumulated')->default(0)->after('views_count');
            $table->unsignedBigInteger('rewards_redeemed_count')->default(0)->after('points_accumulated');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_cards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn(['views_count', 'points_accumulated', 'rewards_redeemed_count']);
        });
    }
};

