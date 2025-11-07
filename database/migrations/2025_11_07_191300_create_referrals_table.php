<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code')->unique();
            $table->string('referral_link')->unique();
            $table->integer('points_earned')->default(0);
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referee_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('referral_code');
            $table->index('referrer_id');
            $table->index('referee_id');
            $table->index('is_used');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};