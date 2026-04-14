<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class QueueUpdated implements ShouldBroadcastNow
{
    public $queue;
    public $players;

    public function __construct($queue, $players)
    {
        $this->queue = strtolower($queue);
        $this->players = $players;
    }

    public function broadcastOn()
    {
        return new Channel("queue.$this->queue");
    }

    public function broadcastAs()
    {
        return 'queue.updated';
    }
}