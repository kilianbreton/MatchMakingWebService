<?php

use App\Http\Controllers\MatchMakingLobbyController;
use App\Http\Controllers\MatchMakingMatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ServerAuthController;
use App\Http\Controllers\StatsController;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

//test
Route::get('/server/create', [ServerAuthController::class, 'create']);
Route::get('/redis-test', function () {
    Redis::set('test', 'hello');
    return Redis::get('test');
});

// Routes pour les plugins ManiaControl
Route::post('/server/auth', [ServerAuthController::class, 'auth']);

Route::middleware('auth.server')->group(function () {

    Route::put('/players/', [PlayerController::class, 'update']);
    Route::get('/testauth', function () {
        return response()->json([
            'status' => 'ok'
        ]);
    });
    Route::post('/queue/add', function (Illuminate\Http\Request $request, QueueService $qs) {
        $queue = $request->input('queue');
        $login = $request->input('login');
        $nickname = $request->input('nickname');
        $nickname = isset($nickname) ? $nickname : $login;
    
        if (!$queue || !$login) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing queue or player'
            ], 400);
        }
    
        return $qs->addPlayer($queue, $login, $nickname);
    });
    
    Route::post('/queue/remove', function (Illuminate\Http\Request $request, QueueService $qs) {
        $queue = $request->input('queue');
        $player = $request->input('player');
    
        if (!$queue || !$player) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing queue or player'
            ], 400);
        }
    
        return $qs->removePlayer($queue, $player);
    });
    Route::post('/match/map', [MatchMakingMatchController::class, 'setMap']);
    Route::post('/match/stats', [StatsController::class, 'store']);
});



//Routes pour le script de MatchMaking
/*
Route::group(['prefix' => 'match-server'], function () {
    Route::get('/match', [MatchMakingMatchController::class, 'searchMatch']);
    Route::post('/live', [MatchMakingMatchController::class, 'liveMatch']);
});
*/




