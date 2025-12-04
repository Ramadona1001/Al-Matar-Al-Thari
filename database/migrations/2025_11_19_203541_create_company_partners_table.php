<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('website_url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('locale')->default('en');
            $table->timestamps();
            
            $table->index(['locale', 'is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_partners');
    }
};
