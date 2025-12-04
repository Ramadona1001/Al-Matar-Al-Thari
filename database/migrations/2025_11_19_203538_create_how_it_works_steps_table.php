<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('how_it_works_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('step_number')->default(1);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('locale')->default('en');
            $table->timestamps();
            
            $table->index(['locale', 'is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('how_it_works_steps');
    }
};
