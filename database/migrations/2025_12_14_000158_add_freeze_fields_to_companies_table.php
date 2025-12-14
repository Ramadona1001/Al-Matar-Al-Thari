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
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_frozen')->default(false)->after('status');
            $table->text('frozen_reason')->nullable()->after('is_frozen');
            $table->foreignId('frozen_by')->nullable()->constrained('users')->onDelete('set null')->after('frozen_reason');
            $table->timestamp('frozen_at')->nullable()->after('frozen_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['frozen_by']);
            $table->dropColumn(['is_frozen', 'frozen_reason', 'frozen_by', 'frozen_at']);
        });
    }
};
