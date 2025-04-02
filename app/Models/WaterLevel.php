<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class WaterLevel extends Model
{
    protected $connection = 'mongodb';
    
    protected $fillable = [
        'level',
        'timestamp',
        'sensor_id',
        'location'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'level' => 'float'
    ];
} 