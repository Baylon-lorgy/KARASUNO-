<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaterLevel;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected MqttService $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        try {
            // Get the latest water level reading
            $latestReading = WaterLevel::latest('timestamp')->first();
            Log::info('Latest water level reading:', ['data' => $latestReading]);

            // Get recent activities
            $recentActivities = $this->getRecentActivities();

            return view('admin.dashboard.index', [
                'waterStatus' => $latestReading?->status ?? 'No Data',
                'lastUpdate' => $latestReading?->timestamp,
                'recentActivities' => $recentActivities,
                'mqttConfig' => $this->mqttService->getWebsocketConfig()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in dashboard:', ['error' => $e->getMessage()]);
            return view('admin.dashboard.index', [
                'waterStatus' => 'Error: ' . $e->getMessage(),
                'lastUpdate' => null,
                'recentActivities' => collect(),
                'mqttConfig' => $this->mqttService->getWebsocketConfig()
            ]);
        }
    }

    /**
     * Get chart data for the dashboard.
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $data = WaterLevel::latest()
                ->take(100)
                ->get()
                ->map(function ($reading) {
                    return [
                        'timestamp' => $reading->timestamp->format('Y-m-d H:i:s'),
                        'status' => $reading->status
                    ];
                });

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching chart data:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the latest water status.
     */
    public function getLatestStatus(): JsonResponse
    {
        try {
            $latest = WaterLevel::latest()->first();
            
            return response()->json([
                'status' => $latest?->status ?? 'No Data',
                'lastUpdate' => $latest?->timestamp ? Carbon::parse($latest->timestamp)->diffForHumans() : 'Never'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching latest status:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get recent activities for the dashboard.
     */
    private function getRecentActivities()
    {
        return WaterLevel::latest('timestamp')
            ->take(5)
            ->get()
            ->map(function ($reading) {
                return (object)[
                    'type' => $reading->status === 'Water Detected' ? 'info' : 'alert',
                    'icon' => $reading->status === 'Water Detected' ? 'water_drop' : 'warning',
                    'message' => $reading->status,
                    'created_at' => $reading->timestamp
                ];
            });
    }
} 