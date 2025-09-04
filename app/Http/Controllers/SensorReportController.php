<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;
use Inertia\Inertia;

class SensorReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SensorReport', [
            'auth' => [
                'user' => auth()->user(),
                'token' => session('auth_token')
            ]
        ]);
    }

    public function generateReport(Request $request)
    {
        // Debug: Log the incoming request parameters
        \Log::info('Report generation request parameters:', $request->all());
        
        $validator = \Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf',
            'orientation' => 'required|in:portrait,landscape',
            'include_header' => 'nullable|string|in:true,false',
            'include_logo' => 'nullable|string|in:true,false',
            'include_signatories' => 'nullable|string|in:true,false',
            'client_name' => 'nullable|string|max:255',
            'report_title' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            // Convert string boolean values to actual booleans
            $includeHeader = $request->include_header === 'true' || $request->include_header === true;
            $includeLogo = $request->include_logo === 'true' || $request->include_logo === true;
            $includeSignatories = $request->include_signatories === 'true' || $request->include_signatories === true;
            
            // Fetch sensor data for the date range
            $sensorData = $this->getSensorData($request->start_date, $request->end_date);
            
            // Prepare report data
            $reportData = [
                'title' => $request->report_title ?: 'Sensor Data Report',
                'client_name' => $request->client_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'generated_at' => now(),
                'sensor_data' => $sensorData,
                'include_header' => $includeHeader,
                'include_logo' => $includeLogo,
                'include_signatories' => $includeSignatories,
                'orientation' => $request->orientation
            ];

            // Generate PDF report
            return $this->generatePDFReport($reportData);

        } catch (\Exception $e) {
            \Log::error('Report generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report'], 500);
        }
    }

    private function getSensorData($startDate, $endDate)
    {
        try {
            $data = DB::table('sensor_data')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            // If no data found, return empty collection with sample structure
            if ($data->isEmpty()) {
                return collect([
                    (object) [
                        'created_at' => $startDate . ' 00:00:00',
                        'temperature' => 0,
                        'humidity' => 0,
                        'water_level' => 0,
                        'status' => 'No Data'
                    ]
                ]);
            }
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Error fetching sensor data: ' . $e->getMessage());
            // Return empty collection with sample structure for error cases
            return collect([
                (object) [
                    'created_at' => $startDate . ' 00:00:00',
                    'temperature' => 0,
                    'humidity' => 0,
                    'water_level' => 0,
                    'status' => 'Error Loading Data'
                ]
            ]);
        }
    }

    private function generatePDFReport($data)
    {
        try {
            // Configure PDF settings
            $orientation = $data['orientation'] === 'landscape' ? 'landscape' : 'portrait';
            
            $pdf = PDF::loadView('reports.sensor-pdf', $data);
            $pdf->setPaper('a4', $orientation);
            
            $filename = $this->generateFilename($data['title'], $data['start_date'], $data['end_date'], 'pdf');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF report'], 500);
        }
    }

    private function generateFilename($title, $startDate, $endDate, $extension)
    {
        $cleanTitle = preg_replace('/[^a-zA-Z0-9\s-]/', '', $title);
        $cleanTitle = strtolower(str_replace(' ', '-', $cleanTitle));
        return "{$cleanTitle}-{$startDate}-to-{$endDate}.{$extension}";
    }
} 