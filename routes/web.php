<?php

use App\Events\TestMessage;
use App\Http\Controllers\MatchController;
use App\Models\player;
use Illuminate\Support\Facades\Route;

Route::get('/', [MatchController::class, 'index']);

Route::get('/ws-test', function () {
    broadcast(new TestMessage("Hello WebSocket"));
    return "Message envoyé";
});