<?php

use App\Events\TestMessage;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManiaplanetAuthController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueuePageController;
use App\Http\Controllers\ServerController;
use App\Models\player;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StatisticsController;

Route::get('/', [MatchController::class, 'index']);


Route::get('/sp', function () {
    $player = player::where('login', '=', 'kamphare')->first();
    $player->assignRole('admin');
});
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

Route::get('/statistics/{gamemode:name}', [StatisticsController::class, 'show'])
    ->name('statistics.show');

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

Route::middleware(['auth', 'permission:access admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
});
