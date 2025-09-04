<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertThreshold extends Model
{
    protected $fillable = [
        'sensor_type',
        'min',
        'max'
    ];

    protected $casts = [
        'min' => 'integer',
        'max' => 'integer'
    ];
} 