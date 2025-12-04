<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('card_id');
            $table->enum('type', ['earn', 'redeem', 'transfer', 'revert']);
            $table->integer('points');
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['customer_id']);
            $table->index(['card_id']);
            $table->index(['type']);

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('loyalty_cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};

