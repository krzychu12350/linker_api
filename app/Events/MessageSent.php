<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // Channel where the event will be broadcast
    public function broadcastOn()
    {
        return new Channel('chat');  // Broadcasting to the "chat" channel
    }

    // Event name that will be used in Vue
    public function broadcastAs()
    {
        return 'message.sent';  // The event name for Vue to listen for
    }
}
