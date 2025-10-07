<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WateringThreshold extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'watering_thresholds';

    protected $fillable = [
        'soil_moisture',
        'temperature',
        'humidity'
    ];

    protected $casts = [
        'soil_moisture' => 'float',
        'temperature' => 'float',
        'humidity' => 'float'
    ];
} 