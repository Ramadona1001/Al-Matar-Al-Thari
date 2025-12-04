<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_loyalty_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('card_id');
            $table->integer('points_balance')->default(0);
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamps();

            $table->unique(['customer_id', 'card_id']);
            $table->index(['customer_id']);
            $table->index(['card_id']);

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('loyalty_cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty_cards');
    }
};

