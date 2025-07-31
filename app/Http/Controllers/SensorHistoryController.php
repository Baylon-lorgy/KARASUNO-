<?php

namespace App\Http\Controllers;

use App\Models\SensorPerDay;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class SensorHistoryController extends Controller
{
    public function index()
    {
        $readings = SensorPerDay::orderBy('date', 'desc')
            ->get()
            ->map(function ($reading) {
                return [
                    'id' => $reading->_id,
                    'date' => Carbon::parse($reading->date)->format('F j, Y'),
                    'time' => '5:00 PM PHT',
                    'water_level' => number_format($reading->water_level, 1) . '%',
                    'soil_moisture' => number_format($reading->soil_moisture, 1) . '%',
                    'temperature' => number_format($reading->temperature, 1) . '°C',
                    'humidity' => number_format($reading->humidity, 1) . '%',
                    'water_status' => $reading->water_float_status === 'water_detected' ? 'Available' : 'Not Available',
                    'averages' => [
                        'water_level' => number_format($reading->average_water_level, 1) . '%',
                        'soil_moisture' => number_format($reading->average_soil_moisture, 1) . '%',
                        'temperature' => number_format($reading->average_temperature, 1) . '°C',
                        'humidity' => number_format($reading->average_humidity, 1) . '%'
                    ],
                    'min_max' => [
                        'water_level' => [
                            'min' => number_format($reading->min_water_level, 1) . '%',
                            'max' => number_format($reading->max_water_level, 1) . '%'
                        ],
                        'soil_moisture' => [
                            'min' => number_format($reading->min_soil_moisture, 1) . '%',
                            'max' => number_format($reading->max_soil_moisture, 1) . '%'
                        ],
                        'temperature' => [
                            'min' => number_format($reading->min_temperature, 1) . '°C',
                            'max' => number_format($reading->max_temperature, 1) . '°C'
                        ],
                        'humidity' => [
                            'min' => number_format($reading->min_humidity, 1) . '%',
                            'max' => number_format($reading->max_humidity, 1) . '%'
                        ]
                    ]
                ];
            });

        return Inertia::render('Admin/SensorHistory', [
            'readings' => $readings
        ]);
    }
} 