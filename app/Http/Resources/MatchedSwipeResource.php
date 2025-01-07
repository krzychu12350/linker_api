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
        // dd($this->resource->conversation->users->first());

        $secondUser = $this->resource->conversation->users->first();
        $conversation = $this->resource->conversation;
        $secondUserPhotos = $secondUser->photos;

        // Build the photo URL (using Cloudinary or empty string if no photos exist)
        $photoUrl = $secondUserPhotos->isEmpty()
            ? ""
            : "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/"
            . $secondUserPhotos->first()->url;


        return [
//            'user' => [
            'swipe_id' => $secondUser->id,
            'conversation_id' => $conversation->id,
            'first_name' => $secondUser->first_name,
            'last_name' => $secondUser->last_name,
            'age' => $secondUser->age,
            'major_photo' => $photoUrl,  // Cloudinary URL or empty string
            //'photos' => PhotoResource::collection($this->resource->photos),
//            ],
//            'photos' => PhotoResource::collection($this->resource->photos),
            // 'details' => $this->resource->allSelectedDetails(),
        ];

    }
}
