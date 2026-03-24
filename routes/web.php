<?php

use App\Events\TestMessage;
use App\Models\player;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

   

    return view('welcome');
});
Route::get('/ws-test', function () {
    broadcast(new TestMessage("Hello WebSocket"));
    return "Message envoyé";
});