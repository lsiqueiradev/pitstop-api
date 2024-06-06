<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function get(Int $id) {
        $team = Team::where('team', $id)->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found'
            ], 400);
        };

        return response()->json($team->content, 200);
    }
}
