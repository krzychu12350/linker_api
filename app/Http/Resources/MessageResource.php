<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $author = $this->sender;
        $authorPhotoUrl =  $author->photos->isEmpty() ? "" :
            "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/"
            . $author->photos->first()->url;

        return [
            'body' => $this->body,
            'read_at' => $this->read_at,
            'is_read' => (bool) $this->read_at,  // Assuming is_read is determined based on read_at
            'author' => [
                'id' => $this->sender->id,
                'first_name' => $this->sender->first_name,
                'photo' => $authorPhotoUrl,
            ],
            'files' => FileResource::collection($this->files)
        ];
    }
}
