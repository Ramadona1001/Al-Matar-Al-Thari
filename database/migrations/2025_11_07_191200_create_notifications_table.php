<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Multi-language support
            $table->json('message'); // Multi-language support
            $table->string('type'); // 'info', 'warning', 'success', 'error'
            $table->string('category'); // 'offer', 'coupon', 'loyalty', 'system'
            $table->string('icon')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('company_id');
            $table->index('type');
            $table->index('category');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};