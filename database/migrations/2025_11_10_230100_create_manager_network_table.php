<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manager_network', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('network_id')->constrained('networks')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['manager_user_id', 'network_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_network');
    }
};

