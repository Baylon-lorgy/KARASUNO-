<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\SensorPerDay;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $startDate = Carbon::parse($request->start)->startOfDay();
        $endDate = Carbon::parse($request->end)->endOfDay();

        // Get sensor data for the date range
        $waterLevelData = SensorData::where('type', 'water_level')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $soilMoistureData = SensorData::where('type', 'soil_moisture')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate statistics
        $stats = [
            'averageWaterLevel' => $waterLevelData->avg('value') ?? 0,
            'averageSoilMoisture' => $soilMoistureData->avg('value') ?? 0,
            'totalReadings' => $waterLevelData->count() + $soilMoistureData->count(),
            'connectionSuccess' => $waterLevelData->where('status', 'success')->count(),
            'connectionFailures' => $waterLevelData->where('status', 'error')->count()
        ];

        return response()->json([
            'waterLevel' => $waterLevelData,
            'soilMoisture' => $soilMoistureData,
            'stats' => $stats
        ]);
    }

    /**
     * Generate PDF report
     */
    public function generateReport(Request $request)
    {
        try {
            // Get parameters from request
            $startDate = $request->get('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            $clientName = $request->get('client_name', 'Client Company');
            $reportTitle = $request->get('report_title', 'Sensor Data Report');
            $orientation = $request->get('orientation', 'portrait');
            
            // Get sensor data from SensorPerDay (same as SensorHistory)
            $sensorData = SensorPerDay::whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($reading) {
                    return [
                        'id' => $reading->_id,
                        'date' => Carbon::parse($reading->date)->format('F j, Y'),
                        'water_level' => number_format($reading->water_level, 1),
                        'soil_moisture' => number_format($reading->soil_moisture, 1),
                        'temperature' => number_format($reading->temperature, 1),
                        'humidity' => number_format($reading->humidity, 1),
                        'water_status' => $reading->water_float_status === 'water_detected' ? 'Available' : 'Not Available',
                        'averages' => [
                            'water_level' => number_format($reading->average_water_level, 1),
                            'soil_moisture' => number_format($reading->average_soil_moisture, 1),
                            'temperature' => number_format($reading->average_temperature, 1),
                            'humidity' => number_format($reading->average_humidity, 1)
                        ],
                        'min_max' => [
                            'water_level' => [
                                'min' => number_format($reading->min_water_level, 1),
                                'max' => number_format($reading->max_water_level, 1)
                            ],
                            'soil_moisture' => [
                                'min' => number_format($reading->min_soil_moisture, 1),
                                'max' => number_format($reading->max_soil_moisture, 1)
                            ],
                            'temperature' => [
                                'min' => number_format($reading->min_temperature, 1),
                                'max' => number_format($reading->max_temperature, 1)
                            ],
                            'humidity' => [
                                'min' => number_format($reading->min_humidity, 1),
                                'max' => number_format($reading->max_humidity, 1)
                            ]
                        ]
                    ];
                });

            // Calculate summary statistics
            $stats = [
                'total_days' => $sensorData->count(),
                'avg_water_level' => $sensorData->count() > 0 ? $sensorData->avg('water_level') : 0,
                'avg_soil_moisture' => $sensorData->count() > 0 ? $sensorData->avg('soil_moisture') : 0,
                'avg_temperature' => $sensorData->count() > 0 ? $sensorData->avg('temperature') : 0,
                'avg_humidity' => $sensorData->count() > 0 ? $sensorData->avg('humidity') : 0,
                'water_available_days' => $sensorData->where('water_status', 'Available')->count()
            ];

            // Prepare data for the PDF
            $data = [
                'title' => $reportTitle,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sensor_data' => $sensorData,
                'stats' => $stats,
                'generated_at' => Carbon::now(),
                'client_name' => $clientName,
                'orientation' => $orientation
            ];

            // Generate PDF using the simple blade template
            $pdf = Pdf::loadView('pdf.report', $data);
            
            // Set paper size and orientation
            $pdf->setPaper('A4', $orientation);
            
            // Return the PDF for download
            return $pdf->download('rainbasin-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return a JSON error response
            return response()->json([
                'error' => 'Failed to generate PDF report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate simple PDF report (alternative method)
     */
    public function generateSimpleReport()
    {
        $data = [
            'title' => 'RainBasin Pro Report',
            'generated_at' => Carbon::now(),
            'client_name' => 'Client Company'
        ];

        $pdf = Pdf::loadView('pdf.report', $data);
        return $pdf->download('rainbasin-simple-report.pdf');
    }
} 