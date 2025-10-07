<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Client;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get MongoDB connection
        $connection = config('database.connections.mongodb.dsn');
        $database = config('database.connections.mongodb.database');
        
        // Create MongoDB client
        $client = new Client($connection);
        $db = $client->selectDatabase($database);
        
        // Create sensor_per_day collection with validation
        $db->createCollection('sensor_per_day', [
            'validator' => [
                '$jsonSchema' => [
                    'bsonType' => 'object',
                    'required' => [
                        'date',
                        'average_water_level',
                        'average_soil_moisture',
                        'readings_count'
                    ],
                    'properties' => [
                        'date' => ['bsonType' => 'string'],
                        'average_water_level' => ['bsonType' => 'double'],
                        'average_soil_moisture' => ['bsonType' => 'double'],
                        'average_temperature' => ['bsonType' => 'double'],
                        'average_humidity' => ['bsonType' => 'double'],
                        'average_light_level' => ['bsonType' => 'double'],
                        'min_water_level' => ['bsonType' => 'double'],
                        'max_water_level' => ['bsonType' => 'double'],
                        'min_soil_moisture' => ['bsonType' => 'double'],
                        'max_soil_moisture' => ['bsonType' => 'double'],
                        'min_temperature' => ['bsonType' => 'double'],
                        'max_temperature' => ['bsonType' => 'double'],
                        'min_humidity' => ['bsonType' => 'double'],
                        'max_humidity' => ['bsonType' => 'double'],
                        'min_light_level' => ['bsonType' => 'double'],
                        'max_light_level' => ['bsonType' => 'double'],
                        'readings_count' => ['bsonType' => 'int'],
                        'success_count' => ['bsonType' => 'int'],
                        'failed_count' => ['bsonType' => 'int'],
                        'created_at' => ['bsonType' => 'date'],
                        'updated_at' => ['bsonType' => 'date']
                    ]
                ]
            ]
        ]);

        // Create indexes
        $collection = $db->selectCollection('sensor_per_day');
        
        // Create a unique index on date field
        $collection->createIndex(
            ['date' => 1],
            ['unique' => true]
        );

        // Create indexes for common queries
        $collection->createIndex(['created_at' => 1]);
        $collection->createIndex(['readings_count' => 1]);
        $collection->createIndex(['average_water_level' => 1]);
        $collection->createIndex(['average_soil_moisture' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get MongoDB connection
        $connection = config('database.connections.mongodb.dsn');
        $database = config('database.connections.mongodb.database');
        
        // Create MongoDB client
        $client = new Client($connection);
        $db = $client->selectDatabase($database);
        
        // Drop the collection
        $db->dropCollection('sensor_per_day');
    }
}; 