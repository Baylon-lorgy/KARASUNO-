<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaterLevel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HistoryController extends Controller
{
    /**
     * Display the history page.
     */
    public function index(Request $request): View
    {
        $query = WaterLevel::query();

        // Apply date filter if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('timestamp', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Apply status filter if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $history = $query->latest('timestamp')
            ->paginate(15)
            ->withQueryString();

        return view('admin.history.index', compact('history'));
    }

    /**
     * Export history data.
     */
    public function export(Request $request)
    {
        try {
            $query = WaterLevel::query();

            // Apply date filter if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('timestamp', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            // Apply status filter if provided
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $data = $query->latest('timestamp')->get();

            // Generate CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=water-level-history.csv',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Status', 'Timestamp']);

                foreach ($data as $record) {
                    fputcsv($file, [
                        $record->status,
                        $record->timestamp->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting history', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }
} 