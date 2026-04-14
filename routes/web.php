<?php

use App\Events\TestMessage;
use App\Http\Controllers\ManiaplanetAuthController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueuePageController;
use App\Http\Controllers\ServerController;
use App\Models\player;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [MatchController::class, 'index']);

Route::get('/ws-test', function ()
{
    broadcast(new TestMessage("Hello WebSocket"));
    return "Message envoyé";
});


Route::get('/auth/maniaplanet/redirect', [ManiaplanetAuthController::class, 'redirect'])
    ->name('maniaplanet.redirect');

Route::get('/auth/maniaplanet/callback', [ManiaplanetAuthController::class, 'callback'])
    ->name('maniaplanet.callback');

Route::get('/queues', [QueuePageController::class, 'index'])
    ->name('queues.index');


Route::post('/logout', function ()
{
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function ()
{
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile');

    Route::get('/servers/{login}/configure', [ServerController::class, 'edit'])
        ->name('servers.configure');

    Route::post('/servers/{login}/configure', [ServerController::class, 'update'])
        ->name('servers.configure.update');

    Route::post('/servers/{login}/regenerate-key', [ServerController::class, 'regenerateKey'])
        ->name('servers.regenerate-key');
});
