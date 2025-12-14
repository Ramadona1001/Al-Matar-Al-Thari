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
        Schema::table('points_settings', function (Blueprint $table) {
            $table->integer('affiliate_settlement_days')->default(30)->after('referral_bonus_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('points_settings', function (Blueprint $table) {
            $table->dropColumn('affiliate_settlement_days');
        });
    }
};
