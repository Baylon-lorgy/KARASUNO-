<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class WateringHistory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'watering_history';

    protected $fillable = [
        'action',
        'source',
        'duration',
        'reason',
        'timestamp',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'duration' => 'integer',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($history) {
            // Set timestamp if not provided
            if (!isset($history->timestamp)) {
                $history->timestamp = now();
            }
        });
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('timestamp', 'desc')->limit($limit);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
} 