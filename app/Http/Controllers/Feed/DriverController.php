<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\RankingDriver;

class DriverController extends Controller
{

    public function create() {

        $rankingDrivers = RankingDriver::offset(20)->limit(10)->get();

        // dd($rankingDrivers);

        // Driver::truncate();
        foreach($rankingDrivers as $i => $rDriver) {
            if ($i <= 10 && $i > 20) return;
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

            // foreach((array) $driversJson->response as $driver) {
            Driver::create([
                'driver' => $driverId,
                'content' => json_encode($driversJson->response)
            ]);
            // }
        }

        return response()->json([
            'message' => 'Drivers added successfully'
        ], 201);
    }
}
