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
        Schema::create('company_partner_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_partner_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            
            $table->unique(['company_partner_id', 'locale'], 'cp_partner_id_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_partner_translations');
    }
};
