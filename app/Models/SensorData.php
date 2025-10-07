<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SensorData extends Model
{
    protected $collection = 'sensor_data';
    
    protected $fillable = [
        'sensor_id',
        'water_level',
        'soil_moisture',
        'temperature',
        'humidity',
        'status'
    ];

    protected $casts = [
        'water_level' => 'float',
        'soil_moisture' => 'float',
        'temperature' => 'float',
        'humidity' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
