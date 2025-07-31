<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class WaterLevel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'water_levels';
    
    protected $fillable = [
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

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d\TH:i:s.u\Z');
    }

    public function scopeForDate($query, $date)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();
        
        return $query->where('created_at', '>=', $startOfDay)
                    ->where('created_at', '<=', $endOfDay);
    }
} 