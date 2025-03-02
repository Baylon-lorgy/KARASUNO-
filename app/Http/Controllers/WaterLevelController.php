<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaterLevelController extends Controller
{
    //

    public function index() {
        $data = WaterLevel::orderBy('timestamp', 'desc')->get();
        return view('water_levels.index', compact('data'));
    }
}
