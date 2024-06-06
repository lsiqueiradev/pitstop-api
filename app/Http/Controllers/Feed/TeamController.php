<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function create() {
        $url = 'https://api-formula-1.p.rapidapi.com/teams';

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
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro'
            ], 401);
        }

        Team::truncate();
        foreach((array) $teamsJson->response as $team) {
            Team::create([
                'team' => $team->id,
                'content' => json_encode($team)
            ]);
        }

        return response()->json([
            'message' => 'Teams added successfully'
        ], 201);
    }

}
