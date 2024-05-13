<?php

namespace App\Events;

use App\Models\Department;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $department;

    /**
     * Create a new event instance.
     * @param Department $department
     * @return void
     */
    public function __construct(Department $department)
    {
        $this->department = $department;
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
