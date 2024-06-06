<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\RankingDriver;
use App\Models\RankingTeam;

class RankingController extends Controller
{

    public function getDrivers() {

        $url = 'https://api-formula-1.p.rapidapi.com/rankings/drivers?season=2024';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-rapidapi-key: cd1e14b42amsh2b9dac6c75ac01ap1150c7jsn7ddd7c4206cb',
            'x-rapidapi-host: api-formula-1.p.rapidapi.com'
        ));
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Token inválido'
            ], 401);
        }

        $driversJson = json_decode($response);

        if ($driversJson->errors) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro'
            ], 401);
        }

        RankingDriver::truncate();
        foreach((array) $driversJson->response as $driver) {
            RankingDriver::create([
                'team' => $driver->team->id,
                'content' => json_encode($driver)
            ]);
        }

        return response()->json([
            'message' => 'Ranking Drivers added successfully'
        ], 201);
    }

    public function getTeams() {

        $url = 'https://api-formula-1.p.rapidapi.com/rankings/teams?season=2024';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-rapidapi-key: cd1e14b42amsh2b9dac6c75ac01ap1150c7jsn7ddd7c4206cb',
            'x-rapidapi-host: api-formula-1.p.rapidapi.com'
        ));
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Token inválido'
            ], 401);
        }

        $teamsJson = json_decode($response);

        if ($teamsJson->errors) {
            dd($teamsJson->errors);
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro'
            ], 401);
        }

        RankingTeam::truncate();
        foreach((array) $teamsJson->response as $team) {
            if ($team->team->id === 8) {
                $team->team->id = 18;
                $team->team->name = 'Stake F1 Team Kick Sauber';
            }

            RankingTeam::create([
                'content' => json_encode($team)
            ]);
        }

        return response()->json([
            'message' => 'Ranking Teams added successfully'
        ], 201);
    }
}
