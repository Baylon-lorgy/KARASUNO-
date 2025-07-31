<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SensorPerDay extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sensor_per_days';
    
    protected $fillable = [
        'date',
        'readings_count',
        'success_count',
        'failed_count',
        'water_level',
        'soil_moisture',
        'temperature',
        'humidity',
        'water_float_status',
        'average_water_level',
        'average_soil_moisture',
        'average_temperature',
        'average_humidity',
        'min_water_level',
        'max_water_level',
        'min_soil_moisture',
        'max_soil_moisture',
        'min_temperature',
        'max_temperature',
        'min_humidity',
        'max_humidity',
        'readings'
    ];

    protected $casts = [
        'readings_count' => 'integer',
        'success_count' => 'integer',
        'failed_count' => 'integer',
        'water_level' => 'float',
        'soil_moisture' => 'float',
        'temperature' => 'float',
        'humidity' => 'float',
        'average_water_level' => 'float',
        'average_soil_moisture' => 'float',
        'average_temperature' => 'float',
        'average_humidity' => 'float',
        'min_water_level' => 'float',
        'max_water_level' => 'float',
        'min_soil_moisture' => 'float',
        'max_soil_moisture' => 'float',
        'min_temperature' => 'float',
        'max_temperature' => 'float',
        'min_humidity' => 'float',
        'max_humidity' => 'float',
        'readings' => 'array'
    ];

    // Format the readings for frontend
    public function formatForFrontend()
    {
        return [
            'device_id' => $this->device_id,
            'timestamp' => $this->timestamp->format('Y-m-d\TH:i:s\Z'),
            'readings' => [
                'temperature_celsius' => $this->readings['temperature_celsius'] ?? 0,
                'humidity_percent' => $this->readings['humidity_percent'] ?? 0,
                'soil_moisture_percent' => $this->readings['soil_moisture_percent'] ?? 0,
                'water_level_cm' => $this->readings['water_level_cm'] ?? 0,
                'water_detected' => $this->readings['water_detected'] ?? false
            ],
            'storage' => [
                'zone' => $this->storage['zone'] ?? 'Main'
            ]
        ];
    }
} 