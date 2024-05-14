<?php

namespace App\Events;

use App\Models\Office;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfficeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $office;
    /**
     * Create a new event instance.
     * @param Office $office
     * @return void
     */
    public function __construct(Office $office)
    {
        $this->office = $office;
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
