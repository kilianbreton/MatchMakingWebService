<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveMatchUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $gamemode,
        public array $match
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('livematch.' . strtolower($this->gamemode)),
        ];
    }

    public function broadcastAs(): string
    {
        return 'livematch.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'gamemode' => strtolower($this->gamemode),
            'match' => $this->match,
        ];
    }
}