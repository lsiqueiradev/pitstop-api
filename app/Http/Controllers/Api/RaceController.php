<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Race;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    public function get() {
        $data = Race::where('type', 'Race')->where('date', '>=', Carbon::now())->with('results')->get();

        $races = array();
        foreach($data as $i => $race) {
            $firstPractice = Race::where('competition', $race->competition)->where('type', '1st Practice')->first();
            $secondPractice = Race::where('competition', $race->competition)->where('type', '2nd Practice')->first();
            $thirdPractice = Race::where('competition', $race->competition)->where('type', '3rd Practice')->first();
            $qualifying = Race::where('competition', $race->competition)->where('type', '1st Qualifying')->first();
            $sprintQualifying = Race::where('competition', $race->competition)->where('type', '1st Sprint Shootout')->first();
            $sprint = Race::where('competition', $race->competition)->where('type', 'Sprint')->first();

            $races[$i] = $race->content;
            $races[$i]['firstPracticeDate'] = $firstPractice->date;
            $races[$i]['secondPracticeDate'] = $sprint ? null : $secondPractice->date;
            $races[$i]['thirdPracticeDate'] = $sprint ? null : $thirdPractice->date;
            $races[$i]['sprintQualifyingDate'] = $sprint ? $sprintQualifying->date : null;
            $races[$i]['sprintDate'] = $sprint ? $sprint->date : null;
            $races[$i]['qualifyingDate'] = $qualifying->date;
            $races[$i]['resultsRace'] = null;
            $races[$i]['resultsFirstQualifying'] =  null;
            $races[$i]['resultsSecondQualifying'] =  null;
            $races[$i]['resultsThirdQualifying'] =  null;
            $races[$i]['resultsSprint'] = null;
        }

        return response()->json($races, 200);
    }
}
