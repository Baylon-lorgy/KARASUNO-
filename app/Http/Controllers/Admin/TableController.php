<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaterLevel;
use App\Models\Alert;
use App\Models\Device;
use Carbon\Carbon;

class TableController extends Controller
{
    public function index()
    {
        // Get water levels data
        $waterLevels = WaterLevel::with('device')
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();

        // Get alerts data
        $alerts = Alert::with('device')
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();

        // Get devices data
        $devices = Device::orderBy('last_active_at', 'desc')
            ->get();

        return view('admin.tables', compact('waterLevels', 'alerts', 'devices'));
    }
} 