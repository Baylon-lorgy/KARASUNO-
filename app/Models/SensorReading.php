<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SensorReading extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sensor_readings';
    
    protected $fillable = [
        'soil_moisture',
        'temperature',
        'humidity'
    ];

    protected $casts = [
        'soil_moisture' => 'decimal:2',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'http_status' => 'integer',
        'timestamp' => 'datetime',
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

    public function scopeSuccessful($query)
    {
        return $query->where('http_status', 200);
    }
} 