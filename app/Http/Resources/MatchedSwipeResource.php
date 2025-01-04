<?php

namespace App\Http\Resources;

use App\Strategies\PhotoStorageStrategy\PhotoStorageStrategy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\isEmpty;

class MatchedSwipeResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        // Assuming $photoUrl is something like "storage/filename.jpg"
//        $photoUrl = $this->resource->photos->isEmpty() ? "" : cloudinary()->getUrl(
//            $this->resource->photos->first()->url
//        );

        // Build the photo URL (using Cloudinary or empty string if no photos exist)
        $photoUrl = $this->resource->photos->isEmpty()
            ? ""
            : "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/"
            . $this->resource->photos->first()->url;


        return [
//            'user' => [
                'swipe_id' => $this->resource->id,
                'conversation_id' => $this->resource->conversations?->last()?->id,
                'first_name' => $this->resource->first_name,
                'last_name' => $this->resource->last_name,
                'age' => $this->resource->age,
                'major_photo' =>  $photoUrl,  // Cloudinary URL or empty string
                'photos' => PhotoResource::collection($this->resource->photos),
//            ],
//            'photos' => PhotoResource::collection($this->resource->photos),
            // 'details' => $this->resource->allSelectedDetails(),
        ];

    }
}
