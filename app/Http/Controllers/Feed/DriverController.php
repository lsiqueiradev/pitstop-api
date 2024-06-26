<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\RankingDriver;

class DriverController extends Controller
{

    public function create(Int $offset = null) {

        $abbr = array(
            83 => 'ZHO',
            92 => 'SAR',
            97 => 'PIA',
            101 => 'BEA',
        );

        $rankingCount = RankingDriver::get()->count();
        $driversCount = Driver::get()->count();

        if ($rankingCount === $driversCount && $offset === null) {
            return response()->json([
                'message' => 'Dados atualizados'
            ], 201);
        }

        $page = $offset ?? $driversCount;

        $rankingDrivers = RankingDriver::offset($page)->limit(10)->get();

        if ($offset === 0) {
            Driver::truncate();
        }

        foreach($rankingDrivers as $rDriver) {
            $driverId = $rDriver->content['driver']['id'];

            $url = 'https://api-formula-1.p.rapidapi.com/drivers?id=' . $driverId;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'x-rapidapi-host: api-formula-1.p.rapidapi.com',
		        'x-rapidapi-key: cd1e14b42amsh2b9dac6c75ac01ap1150c7jsn7ddd7c4206cb'
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

            $driversJson->response[0]->abbr = $driversJson->response[0]->abbr ?? $abbr[$driversJson->response[0]->id];

            Driver::create([
                'driver' => $driverId,
                'content' => json_encode($driversJson->response)
            ]);
        }

        return response()->json([
            'message' => 'Drivers added successfully',
            'total' => Driver::get()->count()
        ], 201);
    }
}
