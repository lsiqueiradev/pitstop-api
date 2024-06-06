<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feed\{
    RaceController as RaceFeedController,
    RankingController as RankingFeedController,
    TeamController as TeamFeedController,
    DriverController as DriverFeedController,
};

use App\Http\Controllers\Api\{
    RaceController,
    ResultController,
    RankingController,
    TeamController,
    DriverController,
};

//feed
Route::get('/feed/races/create', [RaceFeedController::class, 'create']);
Route::get('/feed/rankings/teams/create', [RankingFeedController::class, 'getTeams']);
Route::get('/feed/rankings/drivers/create', [RankingFeedController::class, 'getDrivers']);
Route::get('/feed/teams/create', [TeamFeedController::class, 'create']);
Route::get('/feed/drivers/create', [DriverFeedController::class, 'create']);


// api
Route::get('/races', [RaceController::class, 'get']);
Route::get('/results', [ResultController::class, 'get']);
Route::get('/rankings/teams', [RankingController::class, 'getTeams']);
Route::get('/rankings/drivers/{teamId?}', [RankingController::class, 'getDrivers']);
Route::get('/teams/{id}', [TeamController::class, 'get']);
Route::get('/drivers/{id}', [DriverController::class, 'get']);
