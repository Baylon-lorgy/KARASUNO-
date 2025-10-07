<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sensors';

    protected $fillable = [
        'water_level',
        'water_available',
        'last_reading'
    ];

    protected $casts = [
        'water_level' => 'float',
        'water_available' => 'boolean',
        'last_reading' => 'datetime'
    ];
} 