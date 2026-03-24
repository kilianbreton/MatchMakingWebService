<?php

use App\Http\Controllers\MatchMakingLobbyController;
use App\Http\Controllers\MatchMakingMatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ServerAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

//test
Route::get('/server/create', [ServerAuthController::class, 'create']);


// Routes pour les plugins ManiaControl

Route::post('/server/auth', [ServerAuthController::class, 'auth']);

Route::middleware('auth:server')->group(function () {

    Route::put('/players/', [PlayerController::class, 'update']);

});


//Routes pour le script de MatchMaking

Route::group(['prefix' => 'lobby-server'], function () {
    Route::post('/matchmaking-live', [MatchMakingLobbyController::class, 'matchmaking_live']);
});

Route::group(['prefix' => 'match-server'], function () {
    Route::get('/match', [MatchMakingMatchController::class, 'searchMatch']);
    Route::post('/live', [MatchMakingMatchController::class, 'liveMatch']);
});


Route::get('/redis-test', function () {

    Redis::set('test', 'hello');

    return Redis::get('test');

});