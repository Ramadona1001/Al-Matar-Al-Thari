<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('company_id')->constrained('clubs')->nullOnDelete();
            $table->boolean('is_verified')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn('is_verified');
        });
    }
};

