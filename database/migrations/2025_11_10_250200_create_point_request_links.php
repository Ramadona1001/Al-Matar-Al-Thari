<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('point_request_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['customer_id']);
            $table->index(['card_id']);

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('loyalty_cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_request_links');
    }
};

