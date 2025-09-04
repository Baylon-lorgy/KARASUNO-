<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = \App\Models\Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($notifications);
    }
}
