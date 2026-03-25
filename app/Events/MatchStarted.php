<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MatchStarted implements ShouldBroadcastNow
{
    public $match;

    public function __construct($match)
    {
        $this->match = $match;
    }

    public function broadcastOn()
    {
        return new Channel("global");
    }

    public function broadcastAs()
    {
        return 'match.started';
    }
}