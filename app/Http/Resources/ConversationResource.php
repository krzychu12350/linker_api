<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get the photo URL of the matched user
        $photoUrl = "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/"
            . $this->resource['matched_user_photo_url'];

        // Map messages to include author details
        $messagesWithAuthors = $this->resource["messages"]->map(function ($message) {
            return [
                'id' => $message->id,
                'body' => $message->body,
                'type' => $message->type,
                'read_at' => $message->read_at,
                'author' => [
                    'id' => $message->sender->id,
                    'first_name' => $message->sender->first_name,
                ],
            ];
        });

        return [
            'id' => $this->resource["conversation"]->id,
            'receiver_user' => [
                'id' => $this->resource['matched_user']->id,
                'first_name' => $this->resource['matched_user']->first_name,
                'last_name' => $this->resource['matched_user']->last_name,
                'photo' => $photoUrl,
                'last_message' => $this->resource["messages"]?->last()->body ?? "",
            ],
            'messages' => $messagesWithAuthors, // Include messages with author details
        ];
    }
}
