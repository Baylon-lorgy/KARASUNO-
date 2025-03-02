<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('type');
            $table->float('value');
            $table->string('unit');
            $table->timestamp('timestamp');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('sensor_data');
    }
};
