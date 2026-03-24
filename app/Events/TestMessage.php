<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TestMessage implements ShouldBroadcastNow
{
    public function __construct(
        public string $message
    ) {}

    public function broadcastOn()
    {
        return new Channel('test');
    }

    public function broadcastAs()
    {
        return 'test.message';
    }
}