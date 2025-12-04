<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pos_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('api_key')->unique();
            $table->enum('status', ['active', 'inactive', 'online', 'offline'])->default('active');
            $table->timestamp('last_active_at')->nullable();
            $table->string('last_ip_address')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->index(['company_id', 'status']);
            $table->index('device_id');
            $table->index('api_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_devices');
    }
};