<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Question;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendQuestion extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $roomID;
    public $question;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $roomID, Question $question )
    {
        $this->roomID = $roomID;
        $this->question = $question;
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
                'answers' => $this->question->answers
            ]
        ];
    }
}
