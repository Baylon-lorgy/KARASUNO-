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
            $table->float('soil_moisture')->default(30);
            $table->float('temperature')->default(30);
            $table->float('humidity')->default(40);
            $table->timestamps();
        });

        // Insert default threshold values
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