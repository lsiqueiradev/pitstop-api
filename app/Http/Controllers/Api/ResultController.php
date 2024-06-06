<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Race;
use Carbon\Carbon;
use PHPUnit\TextUI\Output\NullPrinter;

class ResultController extends Controller
{
    public function get() {
        $data = Race::where('type', 'Race')->where('date', '<=', Carbon::now())->with('results')->get();

        $races = array();
        foreach($data as $i => $race) {
            $firstPractice = Race::where('competition', $race->competition)->where('type', '1st Practice')->first();
            $secondPractice = Race::where('competition', $race->competition)->where('type', '2nd Practice')->first();
            $thirdPractice = Race::where('competition', $race->competition)->where('type', '3rd Practice')->first();
            $qualifying = Race::where('competition', $race->competition)->where('type', '1st Qualifying')->first();
            $sprintQualifying = Race::where('competition', $race->competition)->where('type', '1st Sprint Shootout')->first();
            $sprint = Race::where('competition', $race->competition)->where('type', 'Sprint')->first();
            $raceResults = $race->results->where('type', 'Race')->first();
            $firstQualifyingResults =  $race->results->where('type', '1st Qualifying')->first();
            $secondQualifyingResults = $race->results->where('type', '2nd Qualifying')->first();
            $thirdQualifyingResults = $race->results->where('type', '3rd Qualifying')->first();
            $sprinResults = $race->results->where('type', 'Sprint')->first();


            $thirdQualifyingResultsLong = null;
            $secondQualifyingResultsLong = null;
            if ($thirdQualifyingResults && $secondQualifyingResults && $thirdQualifyingResults) {
                $firstQualifyingResultsShort = array_slice($firstQualifyingResults['content'], 15, 5);
                $secondQualifyingResultsShort = array_slice($secondQualifyingResults['content'], 10, 5);
                $thirdQualifyingResultsLong =  array_merge($thirdQualifyingResults->content, $secondQualifyingResultsShort, $firstQualifyingResultsShort);
                $secondQualifyingResultsLong =  array_merge($secondQualifyingResults->content, $firstQualifyingResultsShort);
                // dd($thirdQualifyingResultsLong, $secondQualifyingResultsShort,$firstQualifyingResultsShort,);
            }


            $races[$i] = $race->content;
            $races[$i]['firstPracticeDate'] = $firstPractice->date;
            $races[$i]['secondPracticeDate'] = $sprint ? null : $secondPractice->date;
            $races[$i]['thirdPracticeDate'] = $sprint ? null : $thirdPractice->date;
            $races[$i]['sprintQualifyingDate'] = $sprint ? $sprintQualifying->date : null;
            $races[$i]['sprintDate'] = $sprint ? $sprint->date : null;
            $races[$i]['qualifyingDate'] = $qualifying->date;
            $races[$i]['resultsRace'] = $raceResults ? $raceResults->content : null;
            $races[$i]['resultsFirstQualifying'] = $firstQualifyingResults ? $firstQualifyingResults->content : null;
            $races[$i]['resultsSecondQualifying'] = $secondQualifyingResultsLong;
            $races[$i]['resultsThirdQualifying'] = $thirdQualifyingResultsLong;
            $races[$i]['resultsSprint'] =  $sprinResults ? $sprinResults->content : null;
        }

        return response()->json(array_reverse($races), 200);
    }
}
