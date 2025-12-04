<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'network_id')) {
                $table->foreignId('network_id')->nullable()->constrained('networks')->nullOnDelete();
            }
            if (!Schema::hasColumn('companies', 'can_display_cards_on_homepage')) {
                $table->boolean('can_display_cards_on_homepage')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'can_display_cards_on_homepage')) {
                $table->dropColumn('can_display_cards_on_homepage');
            }
            if (Schema::hasColumn('companies', 'network_id')) {
                $table->dropConstrainedForeignId('network_id');
            }
        });
    }
};

