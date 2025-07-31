<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::create('water_levels', function (Blueprint $collection) {
            // Add indexes for frequently queried fields
            $collection->index('created_at');
            $collection->index('sensor_id');
            $collection->index('status');
            
            // Compound index for date-based queries
            $collection->index(['created_at' => 1, 'sensor_id' => 1]);
        });
    }

    public function down()
    {
        Schema::drop('water_levels');
    }
}; 