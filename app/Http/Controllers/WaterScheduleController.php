<?php

namespace App\Http\Controllers;

use App\Models\WaterSchedule;
use App\Notifications\WateringScheduleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaterScheduleController extends Controller
{
    public function index()
    {
        $schedules = WaterSchedule::all();
        return view('admin.waterschedule', compact('schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1',
        ]);

        $schedule = WaterSchedule::create($validated);

        // Send notification for schedule creation
        Auth::user()->notify(new WateringScheduleNotification(
            "{$schedule->day} {$schedule->time}",
            'SCHEDULED',
            "{$schedule->duration} mins",
            "New watering schedule has been created."
        ));

        return redirect()->back()->with('success', 'Schedule created successfully');
    }

    public function update(Request $request, WaterSchedule $schedule)
    {
        $validated = $request->validate([
            'day' => 'required|string',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1',
        ]);

        $schedule->update($validated);

        return redirect()->back()->with('success', 'Schedule updated successfully');
    }

    public function destroy(WaterSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->back()->with('success', 'Schedule deleted successfully');
    }

    public function startWatering(WaterSchedule $schedule)
    {
        try {
            // Your watering logic here
            $success = true; // Replace with actual watering logic

            if ($success) {
                Auth::user()->notify(new WateringScheduleNotification(
                    "{$schedule->day} {$schedule->time}",
                    'COMPLETED',
                    "{$schedule->duration} mins",
                    "Automatic Plant watering successful."
                ));
            } else {
                Auth::user()->notify(new WateringScheduleNotification(
                    "{$schedule->day} {$schedule->time}",
                    'FAILED',
                    "{$schedule->duration} mins",
                    "Automatic Plan Watering Failed."
                ));
            }

            return response()->json(['status' => $success ? 'success' : 'failed']);
        } catch (\Exception $e) {
            Auth::user()->notify(new WateringScheduleNotification(
                "{$schedule->day} {$schedule->time}",
                'FAILED',
                "{$schedule->duration} mins",
                "Automatic Plan Watering Failed: " . $e->getMessage()
            ));

            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }
} 