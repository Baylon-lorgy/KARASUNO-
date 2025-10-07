<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        try {
            Schema::create('sensor_per_day', function (Blueprint $collection) {
                // Add indexes for frequently queried fields
                $collection->index('date');
                $collection->index('sensor_id');
                
                // Compound index for date-based queries
                $collection->index(['date' => 1, 'sensor_id' => 1]);

                // Add schema validation
                $collection->validator([
                    '$jsonSchema' => [
                        'bsonType' => 'object',
                        'required' => [
                            'date',
                            'sensor_id',
                            'average_water_level',
                            'average_soil_moisture',
                            'average_temperature',
                            'average_humidity',
                            'readings_count'
                        ],
                        'properties' => [
                            'date' => ['bsonType' => 'string'],
                            'sensor_id' => ['bsonType' => 'string'],
                            'average_water_level' => ['bsonType' => 'double'],
                            'average_soil_moisture' => ['bsonType' => 'double'],
                            'average_temperature' => ['bsonType' => 'double'],
                            'average_humidity' => ['bsonType' => 'double'],
                            'min_water_level' => ['bsonType' => 'double'],
                            'max_water_level' => ['bsonType' => 'double'],
                            'min_soil_moisture' => ['bsonType' => 'double'],
                            'max_soil_moisture' => ['bsonType' => 'double'],
                            'min_temperature' => ['bsonType' => 'double'],
                            'max_temperature' => ['bsonType' => 'double'],
                            'min_humidity' => ['bsonType' => 'double'],
                            'max_humidity' => ['bsonType' => 'double'],
                            'readings_count' => ['bsonType' => 'int'],
                            'success_count' => ['bsonType' => 'int'],
                            'failed_count' => ['bsonType' => 'int']
                        ]
                    ]
                ]);
            });
        } catch (\Exception $e) {
            // Collection might already exist, that's okay
            // Just create indexes if they don't exist
            try {
                $collection = \DB::connection('mongodb')->getCollection('sensor_per_day');
                $collection->createIndex(['date' => 1]);
                $collection->createIndex(['sensor_id' => 1]);
                $collection->createIndex(['date' => 1, 'sensor_id' => 1]);
            } catch (\Exception $indexException) {
                // Indexes might already exist, continue
            }
        }
    }

    public function down()
    {
        Schema::drop('sensor_per_day');
    }
}; 