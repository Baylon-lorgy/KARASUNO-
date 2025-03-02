<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Device extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'devices';

    protected $fillable = [
        'name',
        'type',
        'status',
        'location',
        'last_active_at'
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'location' => 'array'
    ];

    public function waterLevels()
    {
        return $this->hasMany(WaterLevel::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
