<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RankingDriver;
use App\Models\RankingTeam;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function getTeams() {
        $data = RankingTeam::get();

        if (!$data) {
            return response()->json([], 200);
        }

        $teams = array();
        foreach($data as $i => $team) {
            $teams[$i] = $team->content;
        }

        return response()->json($teams, 200);
    }

    public function getDrivers(Request $request, Int $teamId = null) {
        $query = RankingDriver::query();

        if ($teamId) {
            $query->where('team', $teamId);
        }

        $data = $query->get();

        if (!$data) {
            return response()->json([], 200);
        }

        $drivers = array();
        foreach($data as $i => $driver) {
            $drivers[$i] = $driver->content;
        }

        return response()->json($drivers, 200);
    }
}
