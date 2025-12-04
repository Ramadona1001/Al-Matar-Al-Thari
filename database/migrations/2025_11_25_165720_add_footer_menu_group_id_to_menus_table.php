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
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('footer_menu_group_id')->nullable()->after('parent_id');
            $table->foreign('footer_menu_group_id')->references('id')->on('footer_menu_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['footer_menu_group_id']);
            $table->dropColumn('footer_menu_group_id');
        });
    }
};
