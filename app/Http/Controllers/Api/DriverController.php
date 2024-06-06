<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function get(Int $id) {
        $driver = Driver::where('driver', $id)->first();

        if (!$driver) {
            return response()->json([
                'message' => 'Driver not found'
            ], 400);
        }

        return response()->json($driver->content, 200);
    }
}
