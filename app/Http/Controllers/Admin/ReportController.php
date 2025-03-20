<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SensorData;
use App\Models\WaterLevel;
use App\Models\WaterSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use MongoDB\Laravel\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $waterLevelHistory = WaterLevel::orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        $distributionHistory = WaterSchedule::with('device')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        return view('admin.reports.index', compact('waterLevelHistory', 'distributionHistory'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,csv,excel'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get sensor data
        $sensorData = SensorData::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->orderBy('created_at')
            ->get();

        // Get water level data
        $waterLevels = WaterLevel::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->orderBy('created_at')
            ->get();

        // Prepare data for report
        $reportData = [
            'period' => $request->report_type,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'sensor_data' => $sensorData,
            'water_levels' => $waterLevels,
            'statistics' => $this->calculateStatistics($sensorData, $waterLevels)
        ];

        // Generate report based on requested format
        switch ($request->format) {
            case 'pdf':
                return $this->generatePDF($reportData);
            case 'csv':
            case 'excel': // Handle Excel format same as CSV for now
                return $this->generateCSV($reportData, $request->format === 'excel' ? 'xlsx' : 'csv');
        }
    }

    private function calculateStatistics($sensorData, $waterLevels)
    {
        return [
            'average_water_level' => $waterLevels->avg('level'),
            'max_water_level' => $waterLevels->max('level'),
            'min_water_level' => $waterLevels->min('level'),
            'total_readings' => $sensorData->count(),
            'alerts_triggered' => $waterLevels->where('level', '>=', config('app.alert_threshold'))->count()
        ];
    }

    private function generatePDF($data)
    {
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        return $pdf->download('sensor-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function generateCSV($data, $extension = 'csv')
    {
        $headers = [
            'Content-Type' => $extension === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv',
            'Content-Disposition' => 'attachment; filename=sensor-report-' . now()->format('Y-m-d') . '.' . $extension,
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add report information
            fputcsv($file, ['Report Period:', ucfirst($data['period'])]);
            fputcsv($file, ['Date Range:', $data['start_date'] . ' to ' . $data['end_date']]);
            fputcsv($file, []);

            // Add statistics
            fputcsv($file, ['Statistics Summary']);
            fputcsv($file, ['Average Water Level', number_format($data['statistics']['average_water_level'], 2) . ' cm']);
            fputcsv($file, ['Maximum Water Level', number_format($data['statistics']['max_water_level'], 2) . ' cm']);
            fputcsv($file, ['Minimum Water Level', number_format($data['statistics']['min_water_level'], 2) . ' cm']);
            fputcsv($file, ['Total Readings', $data['statistics']['total_readings']]);
            fputcsv($file, ['Alerts Triggered', $data['statistics']['alerts_triggered']]);
            fputcsv($file, []);

            // Add data headers
            fputcsv($file, ['Date & Time', 'Water Level (cm)', 'Sensor Reading']);
            
            // Combine and sort data
            $combinedData = collect();
            foreach ($data['water_levels'] as $reading) {
                $combinedData->push([
                    'date' => $reading->created_at,
                    'water_level' => $reading->level,
                    'sensor_reading' => null
                ]);
            }
            
            foreach ($data['sensor_data'] as $reading) {
                $combinedData->push([
                    'date' => $reading->created_at,
                    'water_level' => null,
                    'sensor_reading' => $reading->value
                ]);
            }
            
            $sortedData = $combinedData->sortBy('date');
            
            foreach ($sortedData as $row) {
                fputcsv($file, [
                    $row['date']->format('Y-m-d H:i:s'),
                    $row['water_level'] ? number_format($row['water_level'], 2) : '',
                    $row['sensor_reading'] ?? ''
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
} 