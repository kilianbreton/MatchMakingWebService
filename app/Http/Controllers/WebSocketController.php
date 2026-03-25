<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QueueService;

class WebSocketController
{
    protected $queueService;

    public function __construct(QueueService $qs)
    {
        $this->queueService = $qs;
    }

    /**
     * Recevoir un message JSON du client WebSocket
     */
    public function onMessage($client, $message)
    {
        $data = json_decode($message, true);

        if (!$data || !isset($data['action'])) {
            $client->send(json_encode([
                'event' => 'error',
                'message' => 'Invalid message'
            ]));
            return;
        }

        switch ($data['action']) {
            case 'addPlayerInQueue':
                $queue = $data['queue'] ?? null;
                $playerId = $data['playerId'] ?? null;

                if (!$queue || !$playerId) {
                    $client->send(json_encode([
                        'event' => 'error',
                        'message' => 'Missing parameters'
                    ]));
                    return;
                }

                $this->queueService->addPlayer($queue, $playerId);

                $client->send(json_encode([
                    'event' => 'ok',
                    'message' => "Player $playerId added to $queue"
                ]));
                break;

            case 'removePlayerFromQueue':
                $queue = $data['queue'] ?? null;
                $playerId = $data['playerId'] ?? null;

                if ($queue && $playerId) {
                    $this->queueService->removePlayer($queue, $playerId);

                    $client->send(json_encode([
                        'event' => 'ok',
                        'message' => "Player $playerId removed from $queue"
                    ]));
                }
                break;

            default:
                $client->send(json_encode([
                    'event' => 'error',
                    'message' => 'Unknown action'
                ]));
                break;
        }
    }
}