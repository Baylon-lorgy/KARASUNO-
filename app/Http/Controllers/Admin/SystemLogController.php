<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SystemLogController extends Controller
{
    /**
     * Display the system logs page.
     */
    public function index(): View
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            $logs = [];

            if (file_exists($logFile)) {
                $logs = array_slice(file($logFile), -100); // Get last 100 lines
                $logs = array_reverse($logs); // Show newest first
            }

            return view('admin.systemlogs.index', compact('logs'));
        } catch (\Exception $e) {
            Log::error('Error reading system logs', ['error' => $e->getMessage()]);
            return view('admin.systemlogs.index', ['logs' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Download the log file.
     */
    public function download()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                return back()->with('error', 'Log file not found');
            }

            return response()->download($logFile, 'system-logs.log', [
                'Content-Type' => 'text/plain'
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading system logs', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error downloading logs: ' . $e->getMessage());
        }
    }

    /**
     * Clear the log file.
     */
    public function clear()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
                Log::info('System logs cleared');
                return back()->with('success', 'Logs cleared successfully');
            }

            return back()->with('error', 'Log file not found');
        } catch (\Exception $e) {
            Log::error('Error clearing system logs', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error clearing logs: ' . $e->getMessage());
        }
    }
} 