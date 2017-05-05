<?php

namespace App\Events;

use App\Events\Event;
use App\Models\IntermediateResult;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CheckResult extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $roomID;
    public $user;
    public $result;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $roomID, User $user, IntermediateResult $result )
    {
        $this->roomID = $roomID;
        $this->user = $user;
        $this->result = $result;
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
        return 'GetResult';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->roomID,
            'data' => [
                'user' => $this->user->name,
                'result' => $this->result
            ]
        ];
    }

}
