<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WateringHistory;
use Illuminate\Support\Facades\Log;

class WateringHistoryController extends Controller
{
    /**
     * Display a listing of watering history entries.
     */
    public function index()
    {
        try {
            $history = WateringHistory::orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('Error fetching watering history: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch watering history',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created watering history entry.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:start,stop',
                'source' => 'required|string|in:manual,automatic,scheduled',
                'duration' => 'nullable|integer|min:1|max:5',
                'reason' => 'nullable|string|max:255'
            ]);

            $history = WateringHistory::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Watering history created successfully',
                'data' => $history
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating watering history: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to create watering history',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified watering history entry.
     */
    public function show($id)
    {
        try {
            $history = WateringHistory::find($id);

            if (!$history) {
                return response()->json([
                    'error' => 'Watering history not found'
                ], 404);
            }

            return response()->json($history);
        } catch (\Exception $e) {
            Log::error('Error fetching watering history entry: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch watering history entry',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified watering history entry.
     */
    public function destroy($id)
    {
        try {
            $history = WateringHistory::find($id);

            if (!$history) {
                return response()->json([
                    'error' => 'Watering history not found'
                ], 404);
            }

            $history->delete();

            return response()->json([
                'success' => true,
                'message' => 'Watering history deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting watering history: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to delete watering history',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get watering history statistics.
     */
    public function statistics()
    {
        try {
            $totalEntries = WateringHistory::count();
            $manualEntries = WateringHistory::where('source', 'manual')->count();
            $automaticEntries = WateringHistory::where('source', 'automatic')->count();
            $scheduledEntries = WateringHistory::where('source', 'scheduled')->count();
            
            $startEntries = WateringHistory::where('action', 'start')->count();
            $stopEntries = WateringHistory::where('action', 'stop')->count();

            return response()->json([
                'total_entries' => $totalEntries,
                'by_source' => [
                    'manual' => $manualEntries,
                    'automatic' => $automaticEntries,
                    'scheduled' => $scheduledEntries
                ],
                'by_action' => [
                    'start' => $startEntries,
                    'stop' => $stopEntries
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching watering history statistics: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to fetch watering history statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 