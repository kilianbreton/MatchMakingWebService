<?php

namespace App\Http\Controllers;

use App\Models\GameMode;
use App\Services\QueueService;

class QueuePageController extends Controller
{
    public function index(QueueService $queueService)
    {
        $queues = GameMode::orderBy('name')->get()->map(function ($gamemode) use ($queueService) {
            $players = $queueService->getPlayers($gamemode->name);

            return [
                'name' => $gamemode->name,
                'titlepack' => $gamemode->titlepack,
                'players' => $players,
                'count' => count($players),
            ];
        });

        return view('queues.index', [
            'queues' => $queues,
        ]);
    }
}