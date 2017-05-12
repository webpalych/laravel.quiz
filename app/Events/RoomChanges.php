<?php

namespace App\Events;

use App\Events\Event;
use App\User;
use App\Models\Room;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomChanges extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $room;
    public $user;
    public $type;


    public function __construct(Room $room, User $user, $type = 'joined')
    {
        $this->room = $room;
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['quiz'];
    }

    public function broadcastAs()
    {
        return 'RoomChanges';
    }


    public function broadcastWith()
    {
        return [
            'room' => $this->room->id,
            'data' => [
                'user' => $this->user,
                'type' => $this->type,
            ]
        ];
    }

}
