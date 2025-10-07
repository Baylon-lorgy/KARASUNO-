<?php

namespace App\Http\Controllers;

use App\Models\WateringRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WateringRuleController extends Controller
{
    public function index()
    {
        try {
            $rules = WateringRule::orderBy('name')->get();
            return response()->json($rules);
        } catch (\Exception $e) {
            Log::error('Error fetching watering rules: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch watering rules'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'sensor_type' => 'required|in:soil_moisture,humidity,temperature',
                'condition' => 'required|in:less_than,greater_than,between',
                'value' => 'required',
                'duration' => 'required|integer|min:1|max:5',
                'is_active' => 'boolean',
                'cooldown_minutes' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $rule = WateringRule::create($request->all());
            return response()->json($rule, 201);
        } catch (\Exception $e) {
            Log::error('Error creating watering rule: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create watering rule: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rule = WateringRule::find($id);
            
            if (!$rule) {
                return response()->json(['error' => 'Rule not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string',
                'sensor_type' => 'in:soil_moisture,humidity,temperature',
                'condition' => 'in:less_than,greater_than,between',
                'value' => 'required_with:condition',
                'duration' => 'integer|min:1|max:5',
                'is_active' => 'boolean',
                'cooldown_minutes' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $rule->update($request->all());
            return response()->json($rule);
        } catch (\Exception $e) {
            Log::error('Error updating watering rule: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update watering rule: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $rule = WateringRule::find($id);
            
            if (!$rule) {
                return response()->json(['error' => 'Rule not found'], 404);
            }

            $rule->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting watering rule: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete watering rule'], 500);
        }
    }

    public function toggle($id)
    {
        try {
            $rule = WateringRule::find($id);
            
            if (!$rule) {
                return response()->json(['error' => 'Rule not found'], 404);
            }

            $rule->update(['is_active' => !$rule->is_active]);
            return response()->json($rule);
        } catch (\Exception $e) {
            Log::error('Error toggling watering rule: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to toggle watering rule'], 500);
        }
    }
}
