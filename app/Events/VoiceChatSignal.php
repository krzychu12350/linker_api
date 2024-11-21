<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoiceChatSignal implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $signalData;
    public $receiverIds; // Array of user IDs receiving the signal
    public $senderId;   // ID of the user sending the signal

    /**
     * Create a new event instance.
     *
     * @param array $signalData
     * @param array $receiverIds
     * @param int $senderId
     */
    public function __construct($signalData, $receiverIds, $senderId)
    {
        $this->signalData = $signalData;
        $this->receiverIds = $receiverIds; // Send signal to multiple users
        $this->senderId = $senderId; // ID of the user sending the signal
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to a public channel for each user in receiverIds
        foreach ($this->receiverIds as $receiverId) {
            $channels[] = new Channel('voice-chat.' . $receiverId);
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'VoiceChatSignal'; // Custom event name to be used on the frontend
    }
}
