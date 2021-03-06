<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendQuestion extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $roomID;
    public $question;
    public $answers;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $roomID, $question, $answers )
    {
        $this->roomID = $roomID;
        $this->question = $question;
        $this->answers = $answers;
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
        return 'SendQuestion';
    }


    public function broadcastWith()
    {
        return [
            'room' => $this->roomID,
            'data' => [
                'question' => $this->question,
                'answers' => $this->answers
            ]
        ];
    }
}
