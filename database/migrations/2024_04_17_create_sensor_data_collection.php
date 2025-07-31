<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sensor_data', function (Blueprint $collection) {
            // Add indexes for frequently queried fields
            $collection->index('created_at');
            $collection->index('sensor_id');
            $collection->index('status');
            
            // Compound index for date-based queries
            $collection->index(['created_at' => 1, 'sensor_id' => 1]);

            // Add schema validation
            $collection->validator([
                '$jsonSchema' => [
                    'bsonType' => 'object',
                    'required' => [
                        'sensor_id',
                        'water_level',
                        'soil_moisture',
                        'temperature',
                        'humidity',
                        'status'
                    ],
                    'properties' => [
                        'sensor_id' => ['bsonType' => 'string'],
                        'water_level' => ['bsonType' => 'double'],
                        'soil_moisture' => ['bsonType' => 'double'],
                        'temperature' => ['bsonType' => 'double'],
                        'humidity' => ['bsonType' => 'double'],
                        'status' => ['bsonType' => 'string'],
                        'created_at' => ['bsonType' => 'date'],
                        'updated_at' => ['bsonType' => 'date']
                    ]
                ]
            ]);
        });
    }

    public function down()
    {
        Schema::drop('sensor_data');
    }
}; 