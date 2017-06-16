<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerAnswered extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $roomID;
    public $playername;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $roomID, $playername )
    {
        $this->roomID = $roomID;
        $this->playername = $playername;
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
        return 'PlayerAnswered';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->roomID,
            'data' => [
                'palyer' => $this->playername
            ]
        ];
    }
}
