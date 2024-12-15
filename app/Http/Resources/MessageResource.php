<?php

namespace App\Http\Resources;

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
        return [
            'body' => $this->body,
            'read_at' => $this->read_at,
            'is_read' => (bool) $this->read_at,  // Assuming is_read is determined based on read_at
        ];
    }
}
