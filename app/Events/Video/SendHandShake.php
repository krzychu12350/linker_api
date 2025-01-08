<?php

namespace App\Events\Video;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendHandShake implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data, $senderId, $reciverId, $caller;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($senderId, $reciverId, $data)
    {
        $this->senderId = $senderId;
        $this->reciverId = $reciverId;
        $this->data = $data;
        $this->caller = User::find($senderId);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('handshake.' . $this->reciverId),
        ];
    }

    /**
     * Get the event name that should be broadcast.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'handshake'; // Customize the event name as needed
    }
}
