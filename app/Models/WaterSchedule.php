<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class WaterSchedule extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'water_schedules';

    protected $fillable = [
        'device_id',
        'schedule_time',
        'duration',
        'status',
        'is_active'
    ];

    protected $casts = [
        'schedule_time' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the device that owns the schedule.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
} 