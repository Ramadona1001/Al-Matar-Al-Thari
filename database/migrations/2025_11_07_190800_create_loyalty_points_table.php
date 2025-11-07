<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->integer('points');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'bonus']);
            $table->string('source_type')->nullable(); // 'purchase', 'referral', 'affiliate', etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->dateTime('redeemed_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('company_id');
            $table->index('type');
            $table->index('expiry_date');
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};