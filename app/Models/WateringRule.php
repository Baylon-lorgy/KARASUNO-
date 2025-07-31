<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class WateringRule extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'watering_rules';

    protected $fillable = [
        'name',
        'sensor_type',      // 'soil_moisture', 'humidity', 'temperature'
        'condition',        // 'less_than', 'greater_than', 'between'
        'value',           // Single value for less_than/greater_than, or array for between
        'duration',        // Watering duration in minutes
        'is_active',
        'cooldown_minutes' // Minimum time between waterings
    ];

    protected $casts = [
        'value' => 'array',
        'is_active' => 'boolean',
        'duration' => 'integer',
        'cooldown_minutes' => 'integer',
        'last_triggered' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($rule) {
            // Validate duration
            if ($rule->duration < 1 || $rule->duration > 5) {
                throw new \InvalidArgumentException('Duration must be between 1 and 5 minutes');
            }

            // Validate sensor type
            if (!in_array($rule->sensor_type, ['soil_moisture', 'humidity', 'temperature'])) {
                throw new \InvalidArgumentException('Invalid sensor type');
            }

            // Validate condition
            if (!in_array($rule->condition, ['less_than', 'greater_than', 'between'])) {
                throw new \InvalidArgumentException('Invalid condition');
            }

            // Validate value based on condition
            if ($rule->condition === 'between') {
                if (!is_array($rule->value) || count($rule->value) !== 2) {
                    throw new \InvalidArgumentException('Between condition requires an array of two values');
                }
            }

            // Set default cooldown if not provided
            if (!isset($rule->cooldown_minutes)) {
                $rule->cooldown_minutes = 30; // Default 30 minutes cooldown
            }
        });
    }

    public function shouldTrigger($sensorValue)
    {
        // Check cooldown period
        if ($this->last_triggered && now()->diffInMinutes($this->last_triggered) < $this->cooldown_minutes) {
            return false;
        }

        switch ($this->condition) {
            case 'less_than':
                return $sensorValue < $this->value;
            case 'greater_than':
                return $sensorValue > $this->value;
            case 'between':
                return $sensorValue >= $this->value[0] && $sensorValue <= $this->value[1];
            default:
                return false;
        }
    }
}
