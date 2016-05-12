<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
//het event voor de chat
class ChatMessageEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $data;
    // public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message,$user)
    {
         $this->data = array(
            'message'=> $message,
            'user'=>$user
        );
        // $this->message = $message;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['test-channel'];
    }
    // public function broadcastWith()
    // {
    //     // return ['message' => $message];
    // }
}
