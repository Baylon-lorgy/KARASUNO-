<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->decimal('soil_moisture', 5, 2); // Percentage
            $table->decimal('temperature', 5, 2);   // Celsius
            $table->decimal('humidity', 5, 2);      // Percentage
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sensor_readings');
    }
}; 