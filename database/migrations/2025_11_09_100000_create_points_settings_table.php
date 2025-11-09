<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('earn_rate', 8, 2)->default(10); // currency amount required to earn 1 point
            $table->decimal('redeem_rate', 8, 2)->default(0.1); // currency value per point
            $table->integer('referral_bonus_points')->default(50);
            $table->boolean('auto_approve_redemptions')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_settings');
    }
};
