<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Race;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RaceController extends Controller
{

    public function update() {
        $races = Race::whereIn('type', [
            'Race',
            'Sprint'
        ])
        ->where('date', '>=', Carbon::now()
        ->subDays(3))
        ->where('date', '<=', Carbon::now()
        ->addDays(3))
        ->orderBy('date', 'asc')
        ->limit(2)
        ->get();

        foreach($races as $race) {
            $url = 'https://api-formula-1.p.rapidapi.com/races?id='.$race->race;

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

            $raceJson = json_decode($response);


            Race::where('id', $race->id)
            ->update([
                'content' => json_encode($raceJson->response[0])
            ]);
        }

        return response()->json([
            'message' => 'Race updated successfully'
        ], 201);
    }
    public function races() {

        $url = 'https://api-formula-1.p.rapidapi.com/races?season=2024';

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

        $racesJson = json_decode($response);


        if ($racesJson->errors) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro'
            ], 401);
        }

        Result::query()->delete();
        Race::query()->delete();
        foreach((array) $racesJson->response as $race) {
            Race::create([
                'race' => $race->id,
                'competition' => $race->competition->id,
                'circuit' => $race->circuit->id,
                'type' => $race->type,
                'season' => $race->season,
                'date' => $race->date,
                'content' => json_encode($race)
            ]);
        }

        return response()->json([
            'message' => 'Races and results added successfully'
        ], 201);

    }

    public function results(Int $offset = null) {
        $url = 'https://api-formula-1.p.rapidapi.com/rankings/races?race=';

        $racesCount = Race::whereIn('type', [
            'Race',
            '1st Qualifying',
            '2nd Qualifying',
            '3rd Qualifying',
            'Sprint'
        ])->where('date', '<=', Carbon::now())->orderBy('race', 'asc')->get()->count();

        $count = Result::get()->count();

        if ($racesCount === $count) {
            return response()->json([
                'message' => 'Dados atualizados'
            ], 201);
        }

        $page = $offset ?? $count;

        $data = Race::whereIn('type', [
            'Race',
            '1st Qualifying',
            '2nd Qualifying',
            '3rd Qualifying',
            'Sprint'
        ])->where('date', '<=', Carbon::now())->orderBy('race', 'asc')->offset($page)->limit(10)->get();

        if ($page === 0) {
            Result::truncate();
        }
        foreach ($data as $race) {
            $data = Race::where('type', 'Race')->where('competition', $race->content['competition']['id'])->first();
            $url = 'https://api-formula-1.p.rapidapi.com/rankings/races?race='. $race->race;

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
                    'message' => 'Ocorreu um erro'
                ], 401);
            }

            $resultJson = json_decode($response);

            Result::create([
                'race_id' => $data->id,
                'race' => $race->race,
                'type' => $race->type,
                'content' => json_encode($resultJson->response)
            ]);
        }
        return response()->json([
            'results' => 'Results added successfully',
            'total' => Result::get()->count()
        ], 200);
    }
}
