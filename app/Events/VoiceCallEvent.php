<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoiceCallEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $caller;
    public $callee;
    public $message;

    public function __construct($caller, $callee, $message)
    {
        $this->caller = $caller;
        $this->callee = $callee;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('voice-call.' . $this->callee); // Correct channel for callee
    }

    public function broadcastWith()
    {
        return [
            'caller_id' => $this->caller,
            'callee_id' => $this->callee,
            'message' => $this->message,
        ];
    }
}
