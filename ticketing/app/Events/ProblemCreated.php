<?php

namespace App\Events;

use App\Models\Problem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProblemCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $problem;
    /**
     * Create a new event instance.
     * @param Problem $problem
     * @return void
     */
    public function __construct(Problem $problem)
    {
        $this->problem = $problem;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
