<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loyalty_cards', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
            $table->unsignedInteger('points')->default(0)->after('image_path');
            $table->decimal('balance', 10, 2)->default(0)->after('points');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_cards', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'points', 'balance']);
        });
    }
};

