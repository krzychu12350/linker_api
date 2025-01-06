<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SwipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $photoUrl = $this->resource->photos->isEmpty() ? "" :
            "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/"
            . $this->resource->photos->first()->url;


        return [
            'user' => [
                'swipe_id' => $this->resource->id,
                'first_name' => $this->resource->first_name,
                'age' => $this->resource->age,
                'photo' =>  $photoUrl,  // Cloudinary URL or empty string
                'photos' => PhotoResource::collection($this->resource->photos),
            ],

            // 'details' => $this->resource->allSelectedDetails(),
        ];
    }
}
