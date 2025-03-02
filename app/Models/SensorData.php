<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SensorData extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sensor_data';

    protected $fillable = [
        'device_id',
        'type',
        'value',
        'unit',
        'timestamp',
        'metadata'
    ];

    protected $casts = [
        'value' => 'float',
        'timestamp' => 'datetime',
        'metadata' => 'array'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
