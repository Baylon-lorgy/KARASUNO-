<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterSchedule extends Model
{
    protected $fillable = [
        'day',
        'time',
        'duration',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
} 