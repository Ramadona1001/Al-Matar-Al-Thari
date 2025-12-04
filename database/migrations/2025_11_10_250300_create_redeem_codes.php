<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('redeem_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->unique();
            $table->unsignedBigInteger('card_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->integer('points');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('used_by')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['card_id']);
            $table->index(['customer_id']);

            $table->foreign('card_id')->references('id')->on('loyalty_cards')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('used_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redeem_codes');
    }
};

