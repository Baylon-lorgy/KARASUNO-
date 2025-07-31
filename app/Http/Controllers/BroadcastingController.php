<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastingController extends Controller
{
    public function authenticate(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'auth' => Auth::user()->id,
                'channel_data' => [
                    'user_id' => Auth::user()->id,
                    'user_info' => [
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email
                    ]
                ]
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
} 