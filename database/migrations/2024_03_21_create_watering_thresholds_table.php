<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('watering_thresholds', function (Blueprint $table) {
            $table->id();
            $table->decimal('soil_moisture', 5, 2)->default(30); // Below this percentage, watering is needed
            $table->decimal('temperature', 5, 2)->default(30);   // Above this temperature, watering is needed
            $table->decimal('humidity', 5, 2)->default(40);      // Below this humidity, watering is needed
            $table->timestamps();
        });

        // Insert default thresholds
        DB::table('watering_thresholds')->insert([
            'soil_moisture' => 30,
            'temperature' => 30,
            'humidity' => 40,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('watering_thresholds');
    }
}; 