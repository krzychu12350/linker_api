<?php

namespace App\Http\Resources;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\GroupConversation\Event\EventResource; // You may need to create this
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_message' => new MessageResource($this->messages->last()),
            'users' => UserResource::collection($this->users),
            'events' => $this->events->map(function ($event) {
                // Map events to include their grouped votes
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'time' => $event->time,
                    'votes' => $event->votes(), // Assuming the `votes()` method groups the votes
                ];
            }),
        ];
    }
}
