<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class WaterLevel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'water_levels';
    protected $fillable = ['level', 'status', 'timestamp'];

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime'
    ];
}