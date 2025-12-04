<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Menu identifier (header, footer, sidebar, etc.)
            $table->string('label'); // Display label
            $table->string('url')->nullable();
            $table->string('route')->nullable(); // Laravel route name
            $table->string('icon')->nullable(); // Icon class or name
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->string('locale', 10)->default('en');
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
            $table->index(['name', 'locale', 'is_active']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
