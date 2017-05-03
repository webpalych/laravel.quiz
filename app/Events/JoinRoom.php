<?php

namespace App\Events;

use App\Events\Event;
use App\User;
use App\Models\Room;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class JoinRoom extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $room;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Room $room, User $user)
    {
        $this->room = $room;
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
