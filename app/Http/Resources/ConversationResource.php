<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\isEmpty;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        dd($this);

        $photoUrl = "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/"
            . $this->resource['matched_user_photo_url'];

        return [
            'conversation' => [
                'id' => $this->resource["conversation"]->id,
                'matched_user' => [
                    'id' => $this->resource['matched_user']->id,
                    'first_name' => $this->resource['matched_user']->first_name,
                    'last_name' => $this->resource['matched_user']->last_name,
                    'photo' => $photoUrl,
                    'last_message' => $this->resource["messages"]?->first()->body ?? "",
                ],
            ]

        ];
    }
}
