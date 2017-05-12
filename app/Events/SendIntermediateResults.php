<?php

namespace App\Events;

use App\Events\Event;
use App\Models\IntermediateResult;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendIntermediateResults extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $roomID;
    public $results;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $roomID, $results )
    {
        $this->roomID = $roomID;
        $this->results = $results;
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
        return 'SendIntermediateResults';
    }

    public function broadcastWith()
    {
        return [
            'room' => $this->roomID,
            'data' => [
                'results' => $this->results
            ]
        ];
    }

}
