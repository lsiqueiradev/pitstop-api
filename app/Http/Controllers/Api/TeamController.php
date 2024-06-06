<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function get(Int $id) {
        $team = Team::where('team', $id)->first();

        return response()->json($team->content, 200);
    }
}
