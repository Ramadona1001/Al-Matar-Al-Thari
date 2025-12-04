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
        if (!Schema::hasTable('footer_menu_group_translations')) {
            Schema::create('footer_menu_group_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('footer_menu_group_id');
                $table->string('locale')->index();
                $table->string('name');
                
                $table->unique(['footer_menu_group_id', 'locale'], 'footer_menu_group_trans_unique');
                $table->foreign('footer_menu_group_id')->references('id')->on('footer_menu_groups')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_menu_group_translations');
    }
};
