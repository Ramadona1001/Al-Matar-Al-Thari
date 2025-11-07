<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->string('qr_code')->unique();
            $table->enum('type', ['silver', 'gold', 'platinum'])->default('silver');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->date('expiry_date');
            $table->enum('status', ['active', 'expired', 'blocked'])->default('active');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('card_number');
            $table->index('qr_code');
            $table->index('user_id');
            $table->index('status');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_cards');
    }
};