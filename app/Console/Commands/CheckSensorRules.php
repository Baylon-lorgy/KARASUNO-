<?php

namespace App\Console\Commands;

use App\Models\WateringRule;
use App\Models\SensorReading;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckSensorRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watering:check-sensor-rules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check sensor readings against watering rules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get latest sensor reading
            $latestReading = SensorReading::latest('created_at')->first();
            
            if (!$latestReading) {
                $this->info('No sensor readings found.');
                return;
            }

            $this->info("Checking rules against latest sensor reading from " . $latestReading->created_at);

            // Get all active rules
            $rules = WateringRule::where('is_active', true)->get();
            
            if ($rules->isEmpty()) {
                $this->info('No active rules found.');
                return;
            }

            // Check if watering is already active
            if (Cache::get('watering_active', false)) {
                $this->info('Watering is already active. Skipping rule checks.');
                return;
            }

            foreach ($rules as $rule) {
                $this->info("Checking rule: {$rule->name}");

                // Get the sensor value based on rule type
                $sensorValue = match ($rule->sensor_type) {
                    'soil_moisture' => $latestReading->soil_moisture,
                    'humidity' => $latestReading->humidity,
                    'temperature' => $latestReading->temperature,
                    default => null
                };

                if ($sensorValue === null) {
                    $this->error("Invalid sensor type: {$rule->sensor_type}");
                    continue;
                }

                $this->info("Current {$rule->sensor_type}: {$sensorValue}");

                if ($rule->shouldTrigger($sensorValue)) {
                    $this->info("Rule triggered! Starting watering for {$rule->duration} minutes");

                    // Start watering
                    $response = Http::post('http://127.0.0.1:8000/api/watering-control', [
                        'should_water' => false,
                        'duration' => $rule->duration
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully started watering");
                        
                        // Update last triggered time
                        $rule->update(['last_triggered' => now()]);
                        
                        // Log the event
                        Log::info("Watering started by sensor rule", [
                            'rule' => $rule->name,
                            'sensor_type' => $rule->sensor_type,
                            'sensor_value' => $sensorValue,
                            'duration' => $rule->duration
                        ]);

                        // Only trigger one rule at a time
                        break;
                    } else {
                        $this->error("Failed to start watering: " . $response->body());
                    }
                } else {
                    $this->info("Rule conditions not met");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error checking sensor rules: " . $e->getMessage());
            Log::error("Error checking sensor rules: " . $e->getMessage());
        }
    }
}
